<?php

namespace OpenSaaS\Subify\Contracts\Cache;

use OpenSaaS\Subify\Entities\BenefitUsage;

interface BenefitUsageRepository
{
    /**
     * @return BenefitUsage[]
     */
    public function get(string $subscriberIdentifier): array;

    public function has(string $subscriberIdentifier): bool;

    /**
     * @param BenefitUsage[] $benefitUsages
     */
    public function fill(string $subscriberIdentifier, array $benefitUsages): void;

    public function save(BenefitUsage $benefitUsage): void;
}
