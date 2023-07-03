<?php

namespace OpenSaaS\Subify\Contracts\Database;

use OpenSaaS\Subify\Entities\Plan;

interface PlanRepository
{
    /**
     * @return int[]
     */
    public function allIds(): array;
}
