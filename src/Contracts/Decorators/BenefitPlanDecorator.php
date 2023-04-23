<?php

namespace OpenSaaS\Subify\Contracts\Decorators;

use OpenSaaS\Subify\Entities\BenefitPlan;

interface BenefitPlanDecorator extends Decorator
{
    public function exists(int $benefitId, int $planId): bool;

    public function find(int $benefitId, int $planId): ?BenefitPlan;
}
