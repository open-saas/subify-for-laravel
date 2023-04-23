<?php

namespace OpenSaaS\Subify\Repositories\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Cache\BenefitPlanRepository as CacheBenefitPlanRepository;
use OpenSaaS\Subify\Entities\BenefitPlan;
use OpenSaaS\Subify\Repositories\Cache\Concerns\HandlesPrefix;

class BenefitPlanRepository implements CacheBenefitPlanRepository
{
    use HandlesPrefix;

    private CacheRepository $cacheRepository;

    public function __construct(
        CacheFactory $cacheFactory,
        ConfigRepository $configRepository,
    ) {
        $this->configRepository = $configRepository;

        $cacheStore = $this->configRepository->get('subify.repositories.cache.benefit_plan.store');
        $this->cacheRepository = $cacheFactory->store($cacheStore);
    }

    public function filled(): bool
    {
        return $this->cacheRepository->has($this->prefixed('benefit_plans'));
    }

    /**
     * @return BenefitPlan[]
     */
    public function all(): array
    {
        $benefitPlansData = $this->cacheRepository->get($this->prefixed('benefit_plans'));

        if (empty($benefitPlansData)) {
            return [];
        }

        return array_map($this->optimizedArrayToEntity(...), $benefitPlansData);
    }

    /**
     * @param BenefitPlan[] $benefitPlans
     */
    public function fill(array $benefitPlans): void
    {
        $this->cacheRepository->put(
            $this->prefixed('benefit_plans'),
            array_map($this->entityToOptimizedArray(...), $benefitPlans),
            $this->configRepository->get('subify.repositories.cache.benefit_plan.ttl'),
        );
    }

    private function entityToOptimizedArray(BenefitPlan $benefitPlan): array
    {
        return [
            'i' => $benefitPlan->getId(),
            'n' => $benefitPlan->getBenefitId(),
            'p' => $benefitPlan->getPlanId(),
            'q' => $benefitPlan->getCharges(),
            's' => $benefitPlan->isUnlimited(),
        ];
    }

    private function optimizedArrayToEntity(array $benefitPlanData): BenefitPlan
    {
        return new BenefitPlan(
            $benefitPlanData['i'],
            $benefitPlanData['n'],
            $benefitPlanData['p'],
            $benefitPlanData['q'],
            $benefitPlanData['s'],
        );
    }
}
