<?php

namespace OpenSaaS\Subify\Repositories\Context;

use OpenSaaS\Subify\Contracts\Context\BenefitUsageRepository as ContextBenefitUsageRepository;
use OpenSaaS\Subify\Entities\BenefitUsage;

class BenefitUsageRepository implements ContextBenefitUsageRepository
{
    /** @var array<string, array<int, BenefitUsage>> */
    private array $benefitUsages;

    public function find(string $subscriberIdentifier, int $benefitId): ?BenefitUsage
    {
        return $this->benefitUsages[$subscriberIdentifier][$benefitId] ?? null;
    }

    public function has(string $subscriberIdentifier, int $benefitId): bool
    {
        return isset($this->benefitUsages[$subscriberIdentifier][$benefitId]);
    }

    public function fill(string $subscriberIdentifier, array $benefitUsages): void
    {
        $this->benefitUsages[$subscriberIdentifier] = [];

        foreach ($benefitUsages as $benefitUsage) {
            $benefitId = $benefitUsage->getBenefitId();

            $this->benefitUsages[$subscriberIdentifier][$benefitId] = $benefitUsage;
        }
    }

    public function save(BenefitUsage $benefitUsage): void
    {
        $subscriberIdentifier = $benefitUsage->getSubscriberIdentifier();
        $benefitId = $benefitUsage->getBenefitId();

        if (!isset($this->benefitUsages)) {
            $this->benefitUsages = [];
        }

        if (!isset($this->benefitUsages[$subscriberIdentifier])) {
            $this->benefitUsages[$subscriberIdentifier] = [];
        }

        $this->benefitUsages[$subscriberIdentifier][$benefitId] = $benefitUsage;
    }

    public function flush(): void
    {
        unset($this->benefitUsages);
    }
}
