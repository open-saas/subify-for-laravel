<?php

namespace OpenSaaS\Subify\Contracts\Decorators;

use OpenSaaS\Subify\Entities\Plan;
use OpenSaaS\Subify\Exceptions\PlanNotFoundException;

interface PlanDecorator extends Decorator
{
    public function find(int $planId): ?Plan;

    /**
     * @throws PlanNotFoundException
     */
    public function assertExists(int $planId): void;
}
