<?php

namespace OpenSaaS\Subify\Repositories\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Cache\BenefitRepository as CacheBenefitRepository;
use OpenSaaS\Subify\Entities\Benefit;
use OpenSaaS\Subify\Repositories\Cache\Concerns\HandlesPrefix;
use OpenSaaS\Subify\Repositories\Cache\Concerns\SerializesIntervals;

class BenefitRepository implements CacheBenefitRepository
{
    use HandlesPrefix;
    use SerializesIntervals;

    private CacheRepository $cacheRepository;

    public function __construct(
        CacheFactory $cacheFactory,
        ConfigRepository $configRepository,
    ) {
        $this->configRepository = $configRepository;

        $cacheStore = $this->configRepository->get('subify.repositories.cache.benefit.store');
        $this->cacheRepository = $cacheFactory->store($cacheStore);
    }

    public function filled(): bool
    {
        return $this->cacheRepository->has($this->prefixed('benefits'));
    }

    /**
     * @return Benefit[]
     */
    public function all(): array
    {
        $benefitsData = $this->cacheRepository->get($this->prefixed('benefits'));

        if (empty($benefitsData)) {
            return [];
        }

        return array_map($this->optimizedArrayToEntity(...), $benefitsData);
    }

    /**
     * @param Benefit[] $benefits
     */
    public function fill(array $benefits): void
    {
        $this->cacheRepository->put(
            $this->prefixed('benefits'),
            array_map($this->entityToOptimizedArray(...), $benefits),
            $this->configRepository->get('subify.repositories.cache.benefit.ttl'),
        );
    }

    private function entityToOptimizedArray(Benefit $benefit): array
    {
        return [
            'i' => $benefit->getId(),
            'n' => $benefit->getName(),
            's' => $benefit->isConsumable(),
            'q' => $benefit->isQuota(),
            'p' => empty($benefit->getPeriodicity()) ? null : $this->serializeInterval($benefit->getPeriodicity()),
        ];
    }

    private function optimizedArrayToEntity(array $benefitData): Benefit
    {
        return new Benefit(
            $benefitData['i'],
            $benefitData['n'],
            $benefitData['s'],
            $benefitData['q'],
            empty($benefitData['p']) ? null : $this->unserializeInterval($benefitData['p']),
        );
    }
}
