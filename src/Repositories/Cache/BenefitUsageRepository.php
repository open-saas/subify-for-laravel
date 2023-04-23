<?php

namespace OpenSaaS\Subify\Repositories\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Cache\BenefitUsageRepository as CacheBenefitUsageRepository;
use OpenSaaS\Subify\Entities\BenefitUsage;
use OpenSaaS\Subify\Repositories\Cache\Concerns\HandlesPrefix;

class BenefitUsageRepository implements CacheBenefitUsageRepository
{
    use HandlesPrefix;

    private CacheRepository $cacheRepository;

    public function __construct(
        CacheFactory $cacheFactory,
        ConfigRepository $configRepository,
    ) {
        $this->configRepository = $configRepository;

        $cacheStore = $this->configRepository->get('subify.repositories.cache.benefit_usage.store');
        $this->cacheRepository = $cacheFactory->store($cacheStore);
    }

    public function get(string $subscriberIdentifier): array
    {
        $benefitUsages = $this->cacheRepository->get($this->prefixed('benefit_usages:'.$subscriberIdentifier));

        if (empty($benefitUsages)) {
            return [];
        }

        return array_map($this->optimizedArrayToEntity(...), $benefitUsages);
    }

    public function fill(string $subscriberIdentifier, array $benefitUsages): void
    {
        $this->cacheRepository->put(
            $this->prefixed('benefit_usages:'.$subscriberIdentifier),
            array_map($this->entityToOptimizedArray(...), $benefitUsages),
            $this->configRepository->get('subify.repositories.cache.benefit_usage.ttl'),
        );
    }

    public function has(string $subscriberIdentifier): bool
    {
        return $this->cacheRepository->has($this->prefixed('benefit_usages:'.$subscriberIdentifier));
    }

    public function save(BenefitUsage $benefitUsage): void
    {
        $subscriberIdentifier = $benefitUsage->getSubscriberIdentifier();

        $oldBenefitUsages = $this->get($subscriberIdentifier);
        $updatedBenefitUsages = $this->updateUsage($oldBenefitUsages, $benefitUsage);

        $this->fill($subscriberIdentifier, $updatedBenefitUsages);
    }

    private function updateUsage(array $benefitUsages, BenefitUsage $benefitUsage): array
    {
        $usagesWithoutNew = array_filter(
            $benefitUsages,
            fn (BenefitUsage $usage) => $usage->getId() !== $benefitUsage->getId(),
        );

        return array_merge($usagesWithoutNew, [$benefitUsage]);
    }

    private function entityToOptimizedArray(BenefitUsage $benefitUsage): array
    {
        return [
            'i' => $benefitUsage->getId(),
            'n' => $benefitUsage->getBenefitId(),
            's' => $benefitUsage->getSubscriberIdentifier(),
            'a' => $benefitUsage->getAmount(),
            'e' => $benefitUsage->getExpiredAt(),
        ];
    }

    private function optimizedArrayToEntity(array $benefitUsageData): BenefitUsage
    {
        return new BenefitUsage(
            $benefitUsageData['i'],
            $benefitUsageData['s'],
            $benefitUsageData['n'],
            $benefitUsageData['a'],
            $benefitUsageData['e'],
        );
    }
}
