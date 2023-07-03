<?php

namespace OpenSaaS\Subify\Contracts\Context;

interface PlanRepository
{
    public function exists(int $planId): bool;

    public function filled(): bool;

    /**
     * @param int[] $planIds
     */
    public function fill(array $planIds): void;

    public function flush(): void;
}
