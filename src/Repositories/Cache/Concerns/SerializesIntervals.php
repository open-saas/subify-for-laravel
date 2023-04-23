<?php

namespace OpenSaaS\Subify\Repositories\Cache\Concerns;

trait SerializesIntervals
{
    private function serializeInterval(\DateInterval $interval): string
    {
        return $interval->format('P%yY%mM%dDT%hH%iM%sS');
    }

    private function unserializeInterval(string $interval): \DateInterval
    {
        return new \DateInterval($interval);
    }
}
