<?php

namespace OpenSaaS\Subify\Contracts\Database;

use OpenSaaS\Subify\Entities\BenefitPlan;

interface BenefitPlanRepository
{
    /**
     * @return BenefitPlan[]
     */
    public function all(): array;
}
