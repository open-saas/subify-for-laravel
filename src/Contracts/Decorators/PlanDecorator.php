<?php

namespace OpenSaaS\Subify\Contracts\Decorators;

use OpenSaaS\Subify\Entities\Plan;
use OpenSaaS\Subify\Exceptions\PlanNotFoundException;

interface PlanDecorator extends Decorator
{
    /**
     * @throws PlanNotFoundException
     */
    public function assertExists(int $planId): void;
}
