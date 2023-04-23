<?php

namespace OpenSaaS\Subify\Contracts\Database;

use OpenSaaS\Subify\Entities\Benefit;

interface BenefitRepository
{
    /**
     * @return Benefit[]
     */
    public function all(): array;
}
