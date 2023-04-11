<?php

namespace OpenSaaS\Subify\Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Array\SubscriptionRepository as ArraySubscriptionRepository;
use OpenSaaS\Subify\Contracts\Cache\SubscriptionRepository as CacheSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Database\SubscriptionRepository as DatabaseSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Decorators\SubscriptionDecorator as SubscriptionDecoratorContract;
use OpenSaaS\Subify\Entities\Subscription;

class SubscriptionDecorator implements SubscriptionDecoratorContract
{
    public function __construct(
        private ConfigRepository $configRepository,
        private DatabaseSubscriptionRepository $databaseSubscriptionRepository,
        private CacheSubscriptionRepository $cacheSubscriptionRepository,
        private ArraySubscriptionRepository $arraySubscriptionRepository,
    ) {
    }

    public function find(string $subscriberIdentifier): ?Subscription
    {
        $subscription = $this->arraySubscriptionRepository->find($subscriberIdentifier);

        if ($subscription) {
            return $subscription;
        }

        return $this->isCacheEnabled()
            ? $this->findWithCache($subscriberIdentifier)
            : $this->findWithoutCache($subscriberIdentifier);
    }

    public function flush(): void
    {
        $this->arraySubscriptionRepository->flush();
    }

    private function isCacheEnabled(): bool
    {
        return $this->configRepository->get('subify.repositories.cache.subscription.enabled');
    }

    private function findWithCache(string $subscriberIdentifier): ?Subscription
    {
        $subscription = $this->cacheSubscriptionRepository->find($subscriberIdentifier);

        if ($subscription) {
            $this->arraySubscriptionRepository->save($subscription);

            return $subscription;
        }

        $subscription = $this->databaseSubscriptionRepository->findActive($subscriberIdentifier);

        if ($subscription) {
            $this->arraySubscriptionRepository->save($subscription);
            $this->cacheSubscriptionRepository->save($subscription);

            return $subscription;
        }

        return null;
    }

    private function findWithoutCache(string $subscriberIdentifier): ?Subscription
    {
        $subscription = $this->databaseSubscriptionRepository->findActive($subscriberIdentifier);

        if ($subscription) {
            $this->arraySubscriptionRepository->save($subscription);

            return $subscription;
        }

        return null;
    }
}
