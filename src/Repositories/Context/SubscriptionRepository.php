<?php

namespace OpenSaaS\Subify\Repositories\Context;

use OpenSaaS\Subify\Contracts\Context\SubscriptionRepository as ContextSubscriptionRepository;
use OpenSaaS\Subify\Entities\Subscription;

class SubscriptionRepository implements ContextSubscriptionRepository
{
    /** @var array<string, Subscription> */
    private array $subscriptionsByIdentifier = [];

    public function find(string $subscriberIdentifier): ?Subscription
    {
        return $this->subscriptionsByIdentifier[$subscriberIdentifier] ?? null;
    }

    public function has(string $subscriberIdentifier): bool
    {
        return isset($this->subscriptionsByIdentifier[$subscriberIdentifier]);
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
