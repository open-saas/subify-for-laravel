<?php

namespace OpenSaaS\Subify\Contracts\Decorators;

use OpenSaaS\Subify\Entities\Subscription;

interface SubscriptionDecorator
{
    public function find(string $subscriberIdentifier): ?Subscription;

    public function flush(): void;
}
