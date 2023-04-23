<?php

namespace OpenSaaS\Subify;

use OpenSaaS\Subify\Contracts\Decorators\BenefitDecorator;
use OpenSaaS\Subify\Contracts\Decorators\BenefitPlanDecorator;
use OpenSaaS\Subify\Contracts\Decorators\BenefitUsageDecorator;
use OpenSaaS\Subify\Contracts\Decorators\SubscriptionDecorator;
use OpenSaaS\Subify\Contracts\SubscriptionManager as SubscriptionManagerContract;
use OpenSaaS\Subify\Exceptions\CantConsumeException;

class SubscriptionManager implements SubscriptionManagerContract
{
    public function __construct(
        private BenefitDecorator $benefitDecorator,
        private BenefitPlanDecorator $benefitPlanDecorator,
        private BenefitUsageDecorator $benefitUsageDecorator,
        private SubscriptionDecorator $subscriptionDecorator,
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

    /**
     * @throws CantConsumeException if the desired amount is greater than the remaining amount of the benefit
     */
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
        $this->subscriptionDecorator->flushContext();
    }
}
