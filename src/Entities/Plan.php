<?php

namespace OpenSaaS\Subify\Entities;

use DateTime as DateTime;

final class Plan
{
    public function __construct(
        private int $id,
        private string $name,
        /** @var PlanRegime[] */
        private array $regimes,
        private DateTime $createdAt,
        private DateTime $updatedAt,
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

    public function getRegimes(): array
    {
        return $this->regimes;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
