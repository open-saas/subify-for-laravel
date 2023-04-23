<?php

namespace OpenSaaS\Subify\Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Cache\BenefitPlanRepository as CacheBenefitPlanRepository;
use OpenSaaS\Subify\Contracts\Context\BenefitPlanRepository as ContextBenefitPlanRepository;
use OpenSaaS\Subify\Contracts\Database\BenefitPlanRepository as DatabaseBenefitPlanRepository;
use OpenSaaS\Subify\Contracts\Decorators\BenefitPlanDecorator as BenefitPlanDecoratorContract;
use OpenSaaS\Subify\Entities\BenefitPlan;

class BenefitPlanDecorator implements BenefitPlanDecoratorContract
{
    public function __construct(
        private ConfigRepository $configRepository,
        private DatabaseBenefitPlanRepository $databaseBenefitPlanRepository,
        private CacheBenefitPlanRepository $cacheBenefitPlanRepository,
        private ContextBenefitPlanRepository $contextBenefitPlanRepository,
    ) {
    }

    public function flushContext(): void
    {
        $this->contextBenefitPlanRepository->flush();
    }

    public function find(int $benefitId, int $planId): ?BenefitPlan
    {
        $benefitPlans = $this->all();

        foreach ($benefitPlans as $benefitPlan) {
            if ($benefitPlan->getBenefitId() === $benefitId and $benefitPlan->getPlanId() === $planId) {
                return $benefitPlan;
            }
        }

        return null;
    }

    public function exists(int $benefitId, int $planId): bool
    {
        return !empty($this->find($benefitId, $planId));
    }

    /**
     * @return BenefitPlan[]
     */
    private function all(): array
    {
        if ($this->contextBenefitPlanRepository->filled()) {
            return $this->contextBenefitPlanRepository->all();
        }

        $this->isCacheEnabled()
            ? $this->loadWithCache()
            : $this->loadWithoutCache();

        return $this->contextBenefitPlanRepository->all();
    }

    private function isCacheEnabled(): bool
    {
        return $this->configRepository->get('subify.repositories.cache.benefit_plan.enabled');
    }

    private function loadWithCache(): void
    {
        if ($this->cacheBenefitPlanRepository->filled()) {
            $benefits = $this->cacheBenefitPlanRepository->all();
        } else {
            $benefits = $this->databaseBenefitPlanRepository->all();
            $this->cacheBenefitPlanRepository->fill($benefits);
        }

        $this->contextBenefitPlanRepository->fill($benefits);
    }

    private function loadWithoutCache(): void
    {
        $benefits = $this->databaseBenefitPlanRepository->all();

        $this->contextBenefitPlanRepository->fill($benefits);
    }
}
