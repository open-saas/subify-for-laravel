<?php

namespace OpenSaaS\Subify\Repositories\Eloquent;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use OpenSaaS\Subify\Contracts\Database\SubscriptionRepository as DatabaseSubscriptionRepository;
use OpenSaaS\Subify\Entities\Subscription as SubscriptionEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Concerns\HasSubscriberIdentifier;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Subscription;

class SubscriptionRepository implements DatabaseSubscriptionRepository
{
    use HasSubscriberIdentifier;

    private Subscription $model;

    public function __construct(
        private Container $container,
        private ConfigRepository $configRepository,
    )
    {
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
}
