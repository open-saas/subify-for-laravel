<?php

namespace OpenSaaS\Subify\Contracts\Cache;

use OpenSaaS\Subify\Entities\BenefitPlan;

interface BenefitPlanRepository
{
    /**
     * @return BenefitPlan[]
     */
    public function all(): array;

    /**
     * @param BenefitPlan[] $benefitPlans
     */
    public function fill(array $benefitPlans): void;

    public function filled(): bool;
}
