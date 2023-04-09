<?php

namespace OpenSaaS\Subify\Contracts\Array;

use OpenSaaS\Subify\Entities\Subscription;

interface SubscriptionRepository
{
    public function find(string $subscriberIdentifier): ?Subscription;

    public function save(Subscription $subscription): void;

    public function flush(): void;
}
