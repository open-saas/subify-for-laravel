<?php

namespace OpenSaaS\Subify\Contracts\Cache;

interface PlanRepository
{
    /**
     * @return int[]
     */
    public function all(): array;

    public function filled(): bool;

    /**
     * @param int[] $planIds
     */
    public function fill(array $planIds): void;
}
