<?php

namespace OpenSaaS\Subify\Contracts\Context;

use OpenSaaS\Subify\Entities\Subscription;

interface SubscriptionRepository
{
    public function find(string $subscriberIdentifier): ?Subscription;

    public function has(string $subscriberIdentifier): bool;

    public function save(Subscription $subscription): void;

    public function flush(): void;
}
