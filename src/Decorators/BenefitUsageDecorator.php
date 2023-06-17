<?php

namespace OpenSaaS\Subify\Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use OpenSaaS\Subify\Contracts\Cache\BenefitUsageRepository as CacheBenefitUsageRepository;
use OpenSaaS\Subify\Contracts\Context\BenefitUsageRepository as ContextBenefitUsageRepository;
use OpenSaaS\Subify\Contracts\Database\BenefitUsageRepository as DatabaseBenefitUsageRepository;
use OpenSaaS\Subify\Contracts\Decorators\BenefitUsageDecorator as BenefitUsageDecoratorContract;
use OpenSaaS\Subify\Entities\BenefitUsage;

class BenefitUsageDecorator implements BenefitUsageDecoratorContract
{
    public function __construct(
        private ConfigRepository $configRepository,
        private DatabaseBenefitUsageRepository $databaseBenefitUsageRepository,
        private CacheBenefitUsageRepository $cacheBenefitUsageRepository,
        private ContextBenefitUsageRepository $contextBenefitUsageRepository,
    ) {
    }

    public function getConsumed(string $subscriberIdentifier, int $benefitId): float
    {
        $benefitUsage = $this->find($subscriberIdentifier, $benefitId);

        return $benefitUsage ? $benefitUsage->getAmount() : 0;
    }

    public function flushContext(): void
    {
        $this->contextBenefitUsageRepository->flush();
    }

    public function find(string $subscriberIdentifier, int $benefitId): ?BenefitUsage
    {
        if ($this->contextBenefitUsageRepository->has($subscriberIdentifier, $benefitId)) {
            return $this->contextBenefitUsageRepository->find($subscriberIdentifier, $benefitId);
        }

        $this->isCacheEnabled()
            ? $this->loadWithCache($subscriberIdentifier)
            : $this->loadWithoutCache($subscriberIdentifier);

        return $this->contextBenefitUsageRepository->find($subscriberIdentifier, $benefitId);
    }

    public function create(string $subscriberIdentifier, int $benefitId, float $amount, ?\DateTimeInterface $expiration): void
    {
        $benefitUsage = $this->databaseBenefitUsageRepository->insert(
            $subscriberIdentifier,
            $benefitId,
            $amount,
            $expiration,
        );

        if ($this->isCacheEnabled()) {
            $this->cacheBenefitUsageRepository->save($benefitUsage);
        }

        $this->contextBenefitUsageRepository->save($benefitUsage);
    }

    public function save(BenefitUsage $benefitUsage): void
    {
        if ($this->isCacheEnabled()) {
            $this->cacheBenefitUsageRepository->save($benefitUsage);
        }

        $this->databaseBenefitUsageRepository->save($benefitUsage);
        $this->contextBenefitUsageRepository->save($benefitUsage);
    }

    private function isCacheEnabled(): bool
    {
        return $this->configRepository->get('subify.repositories.cache.benefit_usage.enabled');
    }

    private function loadWithCache(string $subscriberIdentifier): void
    {
        if ($this->cacheBenefitUsageRepository->has($subscriberIdentifier)) {
            $benefitUsages = $this->cacheBenefitUsageRepository->get($subscriberIdentifier);
        } else {
            $benefitUsages = $this->databaseBenefitUsageRepository->get($subscriberIdentifier);
            $this->cacheBenefitUsageRepository->fill($subscriberIdentifier, $benefitUsages);
        }

        $this->contextBenefitUsageRepository->fill($subscriberIdentifier, $benefitUsages);
    }

    private function loadWithoutCache(string $subscriberIdentifier): void
    {
        $benefitUsages = $this->databaseBenefitUsageRepository->get($subscriberIdentifier);

        $this->contextBenefitUsageRepository->fill($subscriberIdentifier, $benefitUsages);
    }
}
