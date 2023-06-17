<?php

namespace OpenSaaS\Subify\Contracts\Decorators;

use OpenSaaS\Subify\Entities\BenefitUsage;

interface BenefitUsageDecorator extends Decorator
{
    public function getConsumed(string $subscriberIdentifier, int $benefitId): float;

    public function find(string $subscriberIdentifier, int $benefitId): ?BenefitUsage;

    public function save(BenefitUsage $benefitUsage): void;

    public function create(string $subscriberIdentifier, int $benefitId, float $amount, ?\DateTimeInterface $expiration): void;
}
