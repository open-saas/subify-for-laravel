<?php

namespace OpenSaaS\Subify\Contracts\Decorators;

use OpenSaaS\Subify\Entities\Subscription;
use OpenSaaS\Subify\Exceptions\SubscriptionNotFoundException;

interface SubscriptionDecorator extends Decorator
{
    public function find(string $subscriberIdentifier): ?Subscription;

    /**
     * @throws SubscriptionNotFoundException
     */
    public function findOrFail(string $subscriberIdentifier): Subscription;

    public function create(
        string $subscriberIdentifier,
        int $planId,
        int $planRegimeId,
        \DateTimeInterface $startDate,
        ?\DateTimeInterface $expiration,
        ?\DateTimeInterface $graceEnd,
        ?\DateTimeInterface $trialEnd,
    ): Subscription;
}
