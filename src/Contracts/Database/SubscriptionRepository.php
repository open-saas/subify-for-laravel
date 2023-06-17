<?php

namespace OpenSaaS\Subify\Contracts\Database;

use OpenSaaS\Subify\Entities\Subscription;

interface SubscriptionRepository
{
    public function findActive(string $subscriberIdentifier): ?Subscription;

    public function insert(
        string $subscriberIdentifier,
        int $planId,
        int $planRegimeId,
        \DateTimeInterface $startDate,
        ?\DateTimeInterface $expiration,
        ?\DateTimeInterface $graceEnd,
        ?\DateTimeInterface $trialEnd,
    ): Subscription;
}
