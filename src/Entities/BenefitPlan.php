<?php

namespace OpenSaaS\Subify\Entities;

final class BenefitPlan
{
    public function __construct(
        private int $id,
        private int $benefitId,
        private int $planId,
        private float $charges,
        private bool $isUnlimited,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBenefitId(): int
    {
        return $this->benefitId;
    }

    public function getPlanId(): int
    {
        return $this->planId;
    }

    public function getCharges(): float
    {
        return $this->charges;
    }

    public function isUnlimited(): bool
    {
        return $this->isUnlimited;
    }
}
