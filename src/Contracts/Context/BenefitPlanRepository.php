<?php

namespace OpenSaaS\Subify\Contracts\Context;

use OpenSaaS\Subify\Entities\BenefitPlan;

interface BenefitPlanRepository
{
    /**
     * @return BenefitPlan[]
     */
    public function all(): array;

    public function filled(): bool;

    /**
     * @param BenefitPlan[] $benefitPlans
     */
    public function fill(array $benefitPlans): void;

    public function flush(): void;
}
