<?php

namespace OpenSaaS\Subify\Repositories\Eloquent;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use OpenSaaS\Subify\Contracts\Database\SubscriptionRepository as DatabaseSubscriptionRepository;
use OpenSaaS\Subify\Entities\Subscription as SubscriptionEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Concerns\QueriesBySubscriberIdentifier;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Subscription;

class SubscriptionRepository implements DatabaseSubscriptionRepository
{
    use QueriesBySubscriberIdentifier;

    private Subscription $model;

    public function __construct(
        private Container $container,
        private ConfigRepository $configRepository,
    ) {
        $modelClass = $this->configRepository->get('subify.repositories.eloquent.subscription.model');
        $this->model = $this->container->make($modelClass);
    }

    public function findActive(string $subscriberIdentifier): ?SubscriptionEntity
    {
        return $this->model
            ->newQuery()
            ->where($this->subscriberIs($subscriberIdentifier))
            ->first()
            ?->toEntity();
    }

    public function insert(
        string $subscriberIdentifier,
        int $planId,
        int $planRegimeId,
        \DateTimeInterface $startDate,
        ?\DateTimeInterface $expiration,
        ?\DateTimeInterface $graceEnd,
        ?\DateTimeInterface $trialEnd,
    ): SubscriptionEntity {
        $subscription = $this->model->newInstance([
            'plan_id' => $planId,
            'plan_regime_id' => $planRegimeId,
            'started_at' => $startDate,
            'expired_at' => $expiration,
            'grace_ended_at' => $graceEnd,
            'trial_ended_at' => $trialEnd,
        ]);

        $subscription->setSubscriberIdentifier($subscriberIdentifier);
        $subscription->save();

        return $subscription->toEntity();
    }
}
