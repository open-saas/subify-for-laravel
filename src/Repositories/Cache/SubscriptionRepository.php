<?php

namespace OpenSaaS\Subify\Repositories\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Cache\SubscriptionRepository as CacheSubscriptionRepository;
use OpenSaaS\Subify\Entities\Subscription;
use OpenSaaS\Subify\Repositories\Cache\Concerns\HandlesPrefix;

class SubscriptionRepository implements CacheSubscriptionRepository
{
    use HandlesPrefix;

    private CacheRepository $cacheRepository;

    public function __construct(
        CacheFactory $cacheFactory,
        ConfigRepository $configRepository,
    ) {
        $this->configRepository = $configRepository;

        $cacheStore = $this->configRepository->get('subify.repositories.cache.subscription.store');
        $this->cacheRepository = $cacheFactory->store($cacheStore);
    }

    public function find(string $subscriberIdentifier): ?Subscription
    {
        $subscriptionData = $this->cacheRepository->get($this->prefixed('subscriptions:'.$subscriberIdentifier));

        if (empty($subscriptionData)) {
            return null;
        }

        return $this->optimizedArrayToEntity($subscriptionData);
    }

    public function save(Subscription $subscription): void
    {
        $this->cacheRepository->put(
            $this->prefixed('subscriptions:'.$subscription->getSubscriberIdentifier()),
            $this->entityToOptimizedArray($subscription),
            $this->configRepository->get('subify.repositories.cache.subscription.ttl'),
        );
    }

    public function has(string $subscriberIdentifier): bool
    {
        return $this->cacheRepository->has($this->prefixed('subscriptions:'.$subscriberIdentifier));
    }

    public function delete(string $subscriberIdentifier): void
    {
        $this->cacheRepository->delete($this->prefixed('subscriptions:'.$subscriberIdentifier));
    }

    private function entityToOptimizedArray(Subscription $subscription): array
    {
        return [
            'i' => $subscription->getId(),
            's' => $subscription->getSubscriberIdentifier(),
            'p' => $subscription->getPlanId(),
            'r' => $subscription->getPlanRegimeId(),
            'a' => $subscription->getStartedAt(),
            'g' => $subscription->getGraceEndedAt(),
            't' => $subscription->getTrialEndedAt(),
            'w' => $subscription->getRenewedAt(),
            'e' => $subscription->getExpiredAt(),
        ];
    }

    private function optimizedArrayToEntity(array $subscriptionData): Subscription
    {
        return new Subscription(
            $subscriptionData['i'],
            $subscriptionData['s'],
            $subscriptionData['p'],
            $subscriptionData['r'],
            $subscriptionData['a'],
            $subscriptionData['g'],
            $subscriptionData['t'],
            $subscriptionData['w'],
            $subscriptionData['e'],
        );
    }
}
