<?php

namespace OpenSaaS\Subify\Entities;

use OpenSaaS\Subify\Entities\Concerns\CalculatesRecurrence;

final class Benefit
{
    use CalculatesRecurrence;

    public function __construct(
        private int $id,
        private string $name,
        private bool $isConsumable,
        private bool $isQuota,
        private ?\DateInterval $periodicity,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isConsumable(): bool
    {
        return $this->isConsumable;
    }

    public function isPositional(): bool
    {
        return !$this->isConsumable();
    }

    public function isQuota(): bool
    {
        return $this->isQuota;
    }

    public function getPeriodicity(): ?\DateInterval
    {
        return $this->periodicity;
    }

    public function calculateUsageExpirationDate(\DateTimeImmutable $from): ?\DateTimeImmutable
    {
        if ($this->isQuota() or $this->isPositional()) {
            return null;
        }

        return $this->calculateNextRecurrence($from, $this->getPeriodicity());
    }
}
