<?php

namespace OpenSaaS\Subify\Repositories\Eloquent;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use OpenSaaS\Subify\Contracts\Database\BenefitRepository as DatabaseBenefitRepository;
use OpenSaaS\Subify\Entities\Benefit as BenefitEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Benefit;

class BenefitRepository implements DatabaseBenefitRepository
{
    private Benefit $model;

    public function __construct(
        private Container $container,
        private ConfigRepository $configRepository,
    ) {
        $modelClass = $this->configRepository->get('subify.repositories.eloquent.benefit.model');
        $this->model = $this->container->make($modelClass);
    }

    /**
     * @return BenefitEntity[]
     */
    public function all(): array
    {
        /** @var Benefit[] $benefits */
        $benefits = $this->model
            ->newQuery()
            ->get()
            ->all();

        return array_map(fn (Benefit $benefit) => $benefit->toEntity(), $benefits);
    }
}
