<?php

namespace OpenSaaS\Subify\Contracts\Decorators;

use OpenSaaS\Subify\Entities\Plan;

interface PlanDecorator extends Decorator
{
    public function find(int $planId): ?Plan;
}
