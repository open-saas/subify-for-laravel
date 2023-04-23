<?php

namespace OpenSaaS\Subify\Repositories\Eloquent;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use OpenSaaS\Subify\Contracts\Database\BenefitPlanRepository as DatabaseBenefitPlanRepository;
use OpenSaaS\Subify\Entities\BenefitPlan as BenefitPlanEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Models\BenefitPlan;

class BenefitPlanRepository implements DatabaseBenefitPlanRepository
{
    private BenefitPlan $model;

    public function __construct(
        private Container $container,
        private ConfigRepository $configRepository,
    ) {
        $modelClass = $this->configRepository->get('subify.repositories.eloquent.benefit_plan.model');
        $this->model = $this->container->make($modelClass);
    }

    /**
     * @return BenefitPlanEntity[]
     */
    public function all(): array
    {
        /** @var BenefitPlan[] $benefits */
        $benefits = $this->model
            ->newQuery()
            ->get()
            ->all();

        return array_map(fn (BenefitPlan $benefit): BenefitPlanEntity => $benefit->toEntity(), $benefits);
    }
}
