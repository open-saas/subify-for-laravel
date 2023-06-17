<?php

namespace OpenSaaS\Subify\Contracts\Decorators;

use OpenSaaS\Subify\Entities\PlanRegime;
use OpenSaaS\Subify\Exceptions\PlanRegimeNotFoundException;

interface PlanRegimeDecorator extends Decorator
{
    /**
     * @throws PlanRegimeNotFoundException
     */
    public function findOrFail(int $planRegimeId): PlanRegime;
}
