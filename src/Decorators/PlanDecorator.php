<?php

namespace OpenSaaS\Subify\Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Cache\PlanRepository as CachePlanRepository;
use OpenSaaS\Subify\Contracts\Context\PlanRepository as ContextPlanRepository;
use OpenSaaS\Subify\Contracts\Database\PlanRepository as DatabasePlanRepository;
use OpenSaaS\Subify\Contracts\Decorators\PlanDecorator as PlanDecoratorContract;
use OpenSaaS\Subify\Exceptions\PlanNotFoundException;

class PlanDecorator implements PlanDecoratorContract
{
    public function __construct(
        private ConfigRepository $configRepository,
        private DatabasePlanRepository $databasePlanRepository,
        private CachePlanRepository $cachePlanRepository,
        private ContextPlanRepository $contextPlanRepository,
    ) {
    }

    public function assertExists(int $planId): void
    {
        if (! $this->exists($planId)) {
            throw new PlanNotFoundException();
        }
    }

    public function flushContext(): void
    {
        $this->contextPlanRepository->flush();
    }

    private function isCacheEnabled(): bool
    {
        return $this->configRepository->get('subify.repositories.cache.plan.enabled');
    }

    private function exists(int $planId): bool
    {
        $this->load();

        return $this->contextPlanRepository->exists($planId);
    }

    private function load(): void
    {
        $this->isCacheEnabled()
            ? $this->loadWithCache()
            : $this->loadWithoutCache();
    }

    private function loadWithCache(): void
    {
        if ($this->cachePlanRepository->filled()) {
            $planIds = $this->cachePlanRepository->all();
        } else {
            $planIds = $this->databasePlanRepository->allIds();

            $this->cachePlanRepository->fill($planIds);
        }

        $this->contextPlanRepository->fill($planIds);
    }

    private function loadWithoutCache(): void
    {
        $planIds = $this->databasePlanRepository->allIds();

        $this->contextPlanRepository->fill($planIds);
    }
}
