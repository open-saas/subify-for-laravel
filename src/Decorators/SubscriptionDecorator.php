<?php

namespace OpenSaaS\Subify\Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Cache\SubscriptionRepository as CacheSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Context\SubscriptionRepository as ContextSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Database\SubscriptionRepository as DatabaseSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Decorators\SubscriptionDecorator as SubscriptionDecoratorContract;
use OpenSaaS\Subify\Entities\Subscription;
use OpenSaaS\Subify\Exceptions\SubscriptionNotFoundException;

class SubscriptionDecorator implements SubscriptionDecoratorContract
{
    public function __construct(
        private ConfigRepository $configRepository,
        private DatabaseSubscriptionRepository $databaseSubscriptionRepository,
        private CacheSubscriptionRepository $cacheSubscriptionRepository,
        private ContextSubscriptionRepository $contextSubscriptionRepository,
    ) {
    }

    public function find(string $subscriberIdentifier): ?Subscription
    {
        if ($this->contextSubscriptionRepository->has($subscriberIdentifier)) {
            return $this->contextSubscriptionRepository->find($subscriberIdentifier);
        }

        $this->isCacheEnabled()
            ? $this->loadWithCache($subscriberIdentifier)
            : $this->loadWithoutCache($subscriberIdentifier);

        return $this->contextSubscriptionRepository->find($subscriberIdentifier);
    }

    public function flushContext(): void
    {
        $this->contextSubscriptionRepository->flush();
    }

    private function isCacheEnabled(): bool
    {
        return $this->configRepository->get('subify.repositories.cache.subscription.enabled');
    }

    private function loadWithCache(string $subscriberIdentifier): void
    {
        if ($this->cacheSubscriptionRepository->has($subscriberIdentifier)) {
            $subscription = $this->cacheSubscriptionRepository->find($subscriberIdentifier);
            $this->contextSubscriptionRepository->save($subscription);

            return;
        }

        $subscription = $this->databaseSubscriptionRepository->findActive($subscriberIdentifier);

        if (empty($subscription)) {
            return;
        }

        $this->cacheSubscriptionRepository->save($subscription);
        $this->contextSubscriptionRepository->save($subscription);
    }

    private function loadWithoutCache(string $subscriberIdentifier): void
    {
        $subscription = $this->databaseSubscriptionRepository->findActive($subscriberIdentifier);

        if (!empty($subscription)) {
            $this->contextSubscriptionRepository->save($subscription);
        }
    }

    public function findOrFail(string $subscriberIdentifier): Subscription
    {
        $subscription = $this->find($subscriberIdentifier);

        if (empty($subscription)) {
            throw new SubscriptionNotFoundException();
        }

        return $subscription;
    }

    public function create(
        string $subscriberIdentifier,
        int $planId,
        int $planRegimeId,
        \DateTimeInterface $startDate,
        ?\DateTimeInterface $expiration,
        ?\DateTimeInterface $graceEnd,
        ?\DateTimeInterface $trialEnd,
    ): Subscription {
        $subscription = $this->databaseSubscriptionRepository->insert(
            $subscriberIdentifier,
            $planId,
            $planRegimeId,
            $startDate,
            $expiration,
            $graceEnd,
            $trialEnd,
        );

        $this->contextSubscriptionRepository->save($subscription);

        if ($this->isCacheEnabled()) {
            $this->cacheSubscriptionRepository->save($subscription);
        }

        return $subscription;
    }
}
