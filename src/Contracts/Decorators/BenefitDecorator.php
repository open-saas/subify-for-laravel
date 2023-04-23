<?php

namespace OpenSaaS\Subify\Contracts\Decorators;

use OpenSaaS\Subify\Entities\Benefit;

interface BenefitDecorator extends Decorator
{
    public function find(string $name): ?Benefit;
}
