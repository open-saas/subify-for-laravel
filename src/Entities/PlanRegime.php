<?php

namespace OpenSaaS\Subify\Entities;

use OpenSaaS\Subify\Entities\Concerns\CalculatesRecurrence;

final class PlanRegime
{
    use CalculatesRecurrence;

    public function __construct(
        private int $id,
        private int $planId,
        private ?\DateInterval $periodicity,
        private ?\DateInterval $grace,
        private ?\DateInterval $trial,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPlanId(): int
    {
        return $this->planId;
    }

    public function getPeriodicity(): ?\DateInterval
    {
        return $this->periodicity;
    }

    public function getGrace(): ?\DateInterval
    {
        return $this->grace;
    }

    public function getTrial(): ?\DateInterval
    {
        return $this->trial;
    }

    public function calculateNextExpiration(\DateTimeInterface $from): ?\DateTimeImmutable
    {
        if (empty($this->periodicity)) {
            return null;
        }

        return $this->calculateNextRecurrence(\DateTimeImmutable::createFromInterface($from), $this->periodicity);
    }

    public function calculateNextGraceEnd(\DateTimeInterface $from): ?\DateTimeImmutable
    {
        if (empty($this->grace)) {
            return null;
        }

        return \DateTimeImmutable::createFromInterface($from)->add($this->grace);
    }

    public function calculateNextTrialEnd(\DateTimeInterface $from): ?\DateTimeImmutable
    {
        if (empty($this->trial)) {
            return null;
        }

        return \DateTimeImmutable::createFromInterface($from)->add($this->trial);
    }
}
