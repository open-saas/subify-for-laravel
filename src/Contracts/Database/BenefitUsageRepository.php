<?php

namespace OpenSaaS\Subify\Contracts\Database;

use OpenSaaS\Subify\Entities\BenefitUsage;

interface BenefitUsageRepository
{
    /**
     * @return BenefitUsage[]
     */
    public function get(string $subscriberIdentifier): array;

    public function insert(string $subscriberIdentifier, int $benefitId, float $amount, ?\DateTimeInterface $expiration): BenefitUsage;

    public function save(BenefitUsage $benefitUsage): void;
}
