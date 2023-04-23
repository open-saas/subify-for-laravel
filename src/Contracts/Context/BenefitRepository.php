<?php

namespace OpenSaaS\Subify\Contracts\Context;

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

    public function flush(): void;
}
