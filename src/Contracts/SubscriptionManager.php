<?php

namespace OpenSaaS\Subify\Contracts;

interface SubscriptionManager
{
    public function hasBenefit(string $subscriberIdentifier, string $benefitName): bool;

    public function canConsume(string $subscriberIdentifier, string $benefitName, float $amount): bool;

    public function flushContext(): void;
}
