<?php

namespace OpenSaaS\Subify\Repositories\Context;

use OpenSaaS\Subify\Contracts\Context\BenefitPlanRepository as ContextBenefitPlanRepository;
use OpenSaaS\Subify\Entities\BenefitPlan;

class BenefitPlanRepository implements ContextBenefitPlanRepository
{
    /** @var BenefitPlan[] */
    private array $benefitPlans;

    /**
     * @return BenefitPlan[]
     */
    public function all(): array
    {
        if (!isset($this->benefitPlans)) {
            return [];
        }

        return $this->benefitPlans;
    }

    public function filled(): bool
    {
        return isset($this->benefitPlans);
    }

    /**
     * @param BenefitPlan[] $benefitPlans
     */
    public function fill(array $benefitPlans): void
    {
        $this->benefitPlans = $benefitPlans;
    }

    public function flush(): void
    {
        unset($this->benefitPlans);
    }
}
