<?php

namespace OpenSaaS\Subify\Entities\Concerns;

trait CalculatesRecurrence
{
    private function calculateNextRecurrence(
        \DateTimeImmutable $recurrenceStart,
        \DateInterval $periodicity,
    ): \DateTimeImmutable {
        $nextRecurrence = $recurrenceStart;

        while ($nextRecurrence->getTimestamp() <= time()) {
            $nextRecurrence = $nextRecurrence->add($periodicity);
        }

        return $nextRecurrence;
    }
}
