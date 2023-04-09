<?php

namespace OpenSaaS\Subify\Contracts\Database;

use OpenSaaS\Subify\Entities\Subscription;

interface SubscriptionRepository
{
    public function findActive(string $subscriberIdentifier): ?Subscription;
}
