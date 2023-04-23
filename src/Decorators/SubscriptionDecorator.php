<?php

namespace OpenSaaS\Subify\Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Cache\SubscriptionRepository as CacheSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Context\SubscriptionRepository as ContextSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Database\SubscriptionRepository as DatabaseSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Decorators\SubscriptionDecorator as SubscriptionDecoratorContract;
use OpenSaaS\Subify\Entities\Subscription;

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
        } else {
            $subscription = $this->databaseSubscriptionRepository->findActive($subscriberIdentifier);
            $this->cacheSubscriptionRepository->save($subscription);
        }

        $this->contextSubscriptionRepository->save($subscription);
    }

    private function loadWithoutCache(string $subscriberIdentifier): void
    {
        $subscription = $this->databaseSubscriptionRepository->findActive($subscriberIdentifier);

        $this->contextSubscriptionRepository->save($subscription);
    }
}
