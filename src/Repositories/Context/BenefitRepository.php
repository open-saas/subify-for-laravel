<?php

namespace OpenSaaS\Subify\Repositories\Context;

use OpenSaaS\Subify\Contracts\Context\BenefitRepository as ContextBenefitRepository;
use OpenSaaS\Subify\Entities\Benefit;

class BenefitRepository implements ContextBenefitRepository
{
    /** @var Benefit[] */
    private array $benefits;

    /**
     * @return Benefit[]
     */
    public function all(): array
    {
        if (!isset($this->benefits)) {
            return [];
        }

        return $this->benefits;
    }

    public function filled(): bool
    {
        return isset($this->benefits);
    }

    /**
     * @param Benefit[] $benefits
     */
    public function fill(array $benefits): void
    {
        $this->benefits = $benefits;
    }

    public function flush(): void
    {
        unset($this->benefits);
    }
}
