<?php

namespace OpenSaaS\Subify\Contracts\Cache;

use OpenSaaS\Subify\Entities\Subscription;

interface SubscriptionRepository
{
    public function find(string $subscriberIdentifier): ?Subscription;

    public function save(Subscription $subscription): void;

    public function delete(string $subscriberIdentifier): void;
}
