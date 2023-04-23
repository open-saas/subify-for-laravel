<?php

namespace OpenSaaS\Subify\Contracts\Cache;

use OpenSaaS\Subify\Entities\Benefit;

interface BenefitRepository
{
    /**
     * @return Benefit[]
     */
    public function all(): array;

    public function filled(): bool;

    /**
     * @param Benefit[] $benefits
     */
    public function fill(array $benefits): void;
}
