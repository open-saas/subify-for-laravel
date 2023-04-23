<?php

namespace OpenSaaS\Subify\Contracts\Decorators;

use OpenSaaS\Subify\Entities\Subscription;

interface SubscriptionDecorator extends Decorator
{
    public function find(string $subscriberIdentifier): ?Subscription;
}
