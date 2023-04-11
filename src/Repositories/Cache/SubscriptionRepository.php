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

        $cacheStore = $this->configRepository->get('subify.repositories.cache.store');
        $this->cacheRepository = $cacheFactory->store($cacheStore);
    }

    public function find(string $subscriberIdentifier): ?Subscription
    {
        $subscriptionData = $this->cacheRepository->get($this->prefixed($subscriberIdentifier));

        if (empty($subscriptionData)) {
            return null;
        }

        return $this->optimizedArrayToEntity($subscriptionData);
    }

    public function save(Subscription $subscription): void
    {
        $this->cacheRepository->put(
            $this->prefixed($subscription->getSubscriberIdentifier()),
            $this->entityToOptimizedArray($subscription),
            $this->configRepository->get('subify.repositories.cache.ttl'),
        );
    }

    public function delete(string $subscriberIdentifier): void
    {
        $this->cacheRepository->delete($this->prefixed($subscriberIdentifier));
    }

    private function entityToOptimizedArray(Subscription $subscription): array
    {
        return [
            'i' => $subscription->getId(),
            's' => $subscription->getSubscriberIdentifier(),
            'p' => $subscription->getPlanId(),
            'r' => $subscription->getPlanRegimeId(),
            'g' => $subscription->getGraceEndedAt(),
            't' => $subscription->getTrialEndedAt(),
            'w' => $subscription->getRenewedAt(),
            'e' => $subscription->getExpiredAt(),
            'c' => $subscription->getCreatedAt(),
            'u' => $subscription->getUpdatedAt(),
        ];
    }

    private function optimizedArrayToEntity(array $subscriptionData): Subscription
    {
        return new Subscription(
            id: $subscriptionData['i'],
            subscriberIdentifier: $subscriptionData['s'],
            planId: $subscriptionData['p'],
            planRegimeId: $subscriptionData['r'],
            graceEndedAt: $subscriptionData['g'],
            trialEndedAt: $subscriptionData['t'],
            renewedAt: $subscriptionData['w'],
            expiredAt: $subscriptionData['e'],
            createdAt: $subscriptionData['c'],
            updatedAt: $subscriptionData['u'],
        );
    }
}
