<?php

namespace OpenSaaS\Subify\Contracts\Context;

use OpenSaaS\Subify\Entities\BenefitUsage;

interface BenefitUsageRepository
{
    public function find(string $subscriberIdentifier, int $benefitId): ?BenefitUsage;

    public function has(string $subscriberIdentifier, int $benefitId): bool;

    /**
     * @param BenefitUsage[] $benefitUsages
     */
    public function fill(string $subscriberIdentifier, array $benefitUsages): void;

    public function save(BenefitUsage $benefitUsage): void;

    public function flush(): void;
}
