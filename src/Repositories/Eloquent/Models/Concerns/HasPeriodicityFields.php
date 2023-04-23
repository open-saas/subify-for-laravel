<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models\Concerns;

use OpenSaaS\Subify\Enums\PeriodicityUnit;

trait HasPeriodicityFields
{
    private function periodicityToDateInterval(?PeriodicityUnit $unit, ?int $periodicity): ?\DateInterval
    {
        return ($periodicity and $unit)
            ? \DateInterval::createFromDateString($periodicity.' '.$unit->value)
            : null;
    }
}
