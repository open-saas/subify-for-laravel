<?php

namespace OpenSaaS\Subify\Entities;

final class PlanRegime
{
    public function __construct(
        private int $id,
        private int $planId,
        private string $name,
        private float $price,
        private ?\DateInterval $periodicity,
        private ?\DateInterval $grace,
        private ?\DateInterval $trial,
        private \DateTime $createdAt,
        private \DateTime $updatedAt,
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
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

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function calculateNextExpiration(string|\DateTimeInterface $from = 'now'): ?\DateTimeInterface
    {
        if (empty($this->periodicity)) {
            return null;
        }

        return (new \DateTime($from))->add($this->periodicity);
    }

    public function calculateNextGraceEnd(string|\DateTimeInterface $from = 'now'): ?\DateTimeInterface
    {
        if (empty($this->grace)) {
            return null;
        }

        return (new \DateTime($from))->add($this->grace);
    }

    public function calculateNextTrialEnd(string|\DateTimeInterface $from = 'now'): ?\DateTimeInterface
    {
        if (empty($this->trial)) {
            return null;
        }

        return (new \DateTime($from))->add($this->trial);
    }
}
