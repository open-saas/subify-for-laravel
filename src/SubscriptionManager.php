<?php

namespace OpenSaaS\Subify;

use OpenSaaS\Subify\Contracts\Decorators\BenefitDecorator;
use OpenSaaS\Subify\Contracts\Decorators\BenefitPlanDecorator;
use OpenSaaS\Subify\Contracts\Decorators\BenefitUsageDecorator;
use OpenSaaS\Subify\Contracts\Decorators\PlanDecorator;
use OpenSaaS\Subify\Contracts\Decorators\PlanRegimeDecorator;
use OpenSaaS\Subify\Contracts\Decorators\SubscriptionDecorator;
use OpenSaaS\Subify\Contracts\SubscriptionManager as SubscriptionManagerContract;
use OpenSaaS\Subify\Exceptions\AlreadySubscribedException;
use OpenSaaS\Subify\Exceptions\CantConsumeException;
use OpenSaaS\Subify\Exceptions\SubscriptionCannotBeRenewedException;
use OpenSaaS\Subify\Exceptions\SubscriptionNotFoundException;

class SubscriptionManager implements SubscriptionManagerContract
{
    public function __construct(
        private readonly BenefitDecorator $benefitDecorator,
        private readonly BenefitPlanDecorator $benefitPlanDecorator,
        private readonly BenefitUsageDecorator $benefitUsageDecorator,
        private readonly PlanDecorator $planDecorator,
        private readonly PlanRegimeDecorator $planRegimeDecorator,
        private readonly SubscriptionDecorator $subscriptionDecorator,
    ) {
    }

    public function hasBenefit(string $subscriberIdentifier, string $benefitName): bool
    {
        $subscription = $this->subscriptionDecorator->find($subscriberIdentifier);

        if (empty($subscription) or $subscription->isNotActive()) {
            return false;
        }

        $benefit = $this->benefitDecorator->find($benefitName);

        if (empty($benefit)) {
            return false;
        }

        return $this->benefitPlanDecorator->exists($benefit->getId(), $subscription->getPlanId());
    }

    public function canConsume(string $subscriberIdentifier, string $benefitName, float $amount): bool
    {
        $subscription = $this->subscriptionDecorator->find($subscriberIdentifier);

        if (empty($subscription) or $subscription->isNotActive()) {
            return false;
        }

        $benefit = $this->benefitDecorator->find($benefitName);

        if (empty($benefit)) {
            return false;
        }

        if ($benefit->isPositional()) {
            return true;
        }

        $benefitPlan = $this->benefitPlanDecorator->find($benefit->getId(), $subscription->getPlanId());

        if (empty($benefitPlan)) {
            return false;
        }

        if ($benefitPlan->isUnlimited()) {
            return true;
        }

        if ($amount > $benefitPlan->getCharges()) {
            return false;
        }

        $alreadyConsumed = $this->benefitUsageDecorator->getConsumed($subscriberIdentifier, $benefit->getId());

        return $benefitPlan->getCharges() >= $alreadyConsumed + $amount;
    }

    public function consume(string $subscriberIdentifier, string $benefitName, float $amount): void
    {
        if (!$this->canConsume($subscriberIdentifier, $benefitName, $amount)) {
            throw new CantConsumeException();
        }

        $subscription = $this->subscriptionDecorator->find($subscriberIdentifier);
        $benefit = $this->benefitDecorator->find($benefitName);
        $currentUsage = $this->benefitUsageDecorator->find($subscriberIdentifier, $benefit->getId());

        if (empty($currentUsage)) {
            $expiration = $benefit->calculateUsageExpirationDate($subscription->getStartedAt());
            $this->benefitUsageDecorator->create($subscriberIdentifier, $benefit->getId(), $amount, $expiration);

            return;
        }

        if ($currentUsage->isExpired()) {
            $currentUsage->clearUsage();

            $newExpiration = $benefit->calculateUsageExpirationDate($subscription->getStartedAt());
            $currentUsage->setExpiredAt($newExpiration);
        }

        $currentUsage->increase($amount);
        $this->benefitUsageDecorator->save($currentUsage);
    }

    public function flushContext(): void
    {
        $this->benefitDecorator->flushContext();
        $this->benefitPlanDecorator->flushContext();
        $this->benefitUsageDecorator->flushContext();
        $this->planDecorator->flushContext();
        $this->planRegimeDecorator->flushContext();
        $this->subscriptionDecorator->flushContext();
    }

    public function subscribeTo(
        string $subscriberIdentifier,
        int $planId,
        int $planRegimeId,
        \DateTimeInterface $startDate = new \DateTime,
    ): void {
        $subscription = $this->subscriptionDecorator->find($subscriberIdentifier);

        if ($subscription and $subscription->isActive() and !$subscription->isTrial()) {
            throw new AlreadySubscribedException();
        }

        $this->planDecorator->assertExists($planId);

        $planRegime = $this->planRegimeDecorator->findOrFail($planRegimeId);

        $expiration = $planRegime->calculateNextExpiration($startDate);
        $graceEnd = $planRegime->calculateNextGraceEnd($expiration);

        $this->subscriptionDecorator->create(
            $subscriberIdentifier,
            $planId,
            $planRegimeId,
            $startDate,
            expiration: $expiration,
            graceEnd: $graceEnd,
            trialEnd: null,
        );
    }

    public function tryPlan(
        string $subscriberIdentifier,
        int $planId,
        int $planRegimeId,
        \DateTimeInterface $startDate = new \DateTime,
    ): void {
        $subscription = $this->subscriptionDecorator->find($subscriberIdentifier);

        if ($subscription and $subscription->isActive()) {
            throw new AlreadySubscribedException();
        }

        $this->planDecorator->assertExists($planId);

        $planRegime = $this->planRegimeDecorator->findOrFail($planRegimeId);

        $trialEnd = $planRegime->calculateNextTrialEnd($startDate);
        $graceEnd = $planRegime->calculateNextGraceEnd($trialEnd);

        $this->subscriptionDecorator->create(
            $subscriberIdentifier,
            $planId,
            $planRegimeId,
            $startDate,
            expiration: $trialEnd,
            graceEnd: $graceEnd,
            trialEnd: $trialEnd,
        );
    }

    public function switchTo(
        string $subscriberIdentifier,
        int $planId,
        int $planRegimeId,
        bool $immediately = false,
    ): void {
        $this->planDecorator->assertExists($planId);

        $planRegime = $this->planRegimeDecorator->findOrFail($planRegimeId);
        $subscription = $this->subscriptionDecorator->findOrFail($subscriberIdentifier);

        $startDate = $immediately ? new \DateTime : $subscription->getExpiredAt();

        $expiration = $planRegime->calculateNextExpiration($startDate);
        $graceEnd = $planRegime->calculateNextGraceEnd($expiration);

        $this->subscriptionDecorator->create(
            $subscriberIdentifier,
            $planId,
            $planRegimeId,
            $startDate,
            expiration: $expiration,
            graceEnd: $graceEnd,
            trialEnd: null,
        );
    }

    public function renew(string $subscriberIdentifier): void
    {
        $subscription = $this->subscriptionDecorator->findOrFail($subscriberIdentifier);

        if ($subscription->isNotActive()) {
            throw new SubscriptionCannotBeRenewedException();
        }

        $planRegime = $this->planRegimeDecorator->findOrFail($subscription->getPlanRegimeId());

        $expiration = $planRegime->calculateNextExpiration($subscription->getExpiredAt());
        $graceEnd = $planRegime->calculateNextGraceEnd($expiration);

        $this->subscriptionDecorator->create(
            $subscriberIdentifier,
            $subscription->getPlanId(),
            $subscription->getPlanRegimeId(),
            $subscription->getExpiredAt(),
            expiration: $expiration,
            graceEnd: $graceEnd,
            trialEnd: null,
        );
    }
}
