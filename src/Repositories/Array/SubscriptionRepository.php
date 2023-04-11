<?php

namespace OpenSaaS\Subify\Repositories\Array;

use OpenSaaS\Subify\Contracts\Array\SubscriptionRepository as ArraySubscriptionRepository;
use OpenSaaS\Subify\Entities\Subscription;

class SubscriptionRepository implements ArraySubscriptionRepository
{
    /** @var array<string, Subscription> */
    private array $subscriptionsByIdentifier = [];

    public function find(string $subscriberIdentifier): ?Subscription
    {
        return $this->subscriptionsByIdentifier[$subscriberIdentifier] ?? null;
    }

    public function save(Subscription $subscription): void
    {
        $this->subscriptionsByIdentifier[$subscription->getSubscriberIdentifier()] = $subscription;
    }

    public function flush(): void
    {
        $this->subscriptionsByIdentifier = [];
    }
}
