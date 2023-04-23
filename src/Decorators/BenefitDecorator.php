<?php

namespace OpenSaaS\Subify\Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Cache\BenefitRepository as CacheBenefitRepository;
use OpenSaaS\Subify\Contracts\Context\BenefitRepository as ContextBenefitRepository;
use OpenSaaS\Subify\Contracts\Database\BenefitRepository as DatabaseBenefitRepository;
use OpenSaaS\Subify\Contracts\Decorators\BenefitDecorator as BenefitDecoratorContract;
use OpenSaaS\Subify\Entities\Benefit;

class BenefitDecorator implements BenefitDecoratorContract
{
    public function __construct(
        private readonly ConfigRepository $configRepository,
        private readonly DatabaseBenefitRepository $databaseBenefitRepository,
        private readonly CacheBenefitRepository $cacheBenefitRepository,
        private readonly ContextBenefitRepository $contextBenefitRepository,
    ) {
    }

    public function flushContext(): void
    {
        $this->contextBenefitRepository->flush();
    }

    public function find(string $name): ?Benefit
    {
        $benefits = $this->all();

        foreach ($benefits as $benefit) {
            if ($benefit->getName() === $name) {
                return $benefit;
            }
        }

        return null;
    }

    /**
     * @return Benefit[]
     */
    private function all(): array
    {
        if ($this->contextBenefitRepository->filled()) {
            return $this->contextBenefitRepository->all();
        }

        $this->isCacheEnabled()
            ? $this->loadWithCache()
            : $this->loadWithoutCache();

        return $this->contextBenefitRepository->all();
    }

    private function isCacheEnabled(): bool
    {
        return $this->configRepository->get('subify.repositories.cache.benefit.enabled');
    }

    private function loadWithCache(): void
    {
        if ($this->cacheBenefitRepository->filled()) {
            $benefits = $this->cacheBenefitRepository->all();
        } else {
            $benefits = $this->databaseBenefitRepository->all();
            $this->cacheBenefitRepository->fill($benefits);
        }

        $this->contextBenefitRepository->fill($benefits);
    }

    private function loadWithoutCache(): void
    {
        $benefits = $this->databaseBenefitRepository->all();

        $this->contextBenefitRepository->fill($benefits);
    }
}
