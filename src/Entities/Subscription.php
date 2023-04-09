<?php

namespace OpenSaaS\Subify\Entities;

use DateTime as DateTime;

final class Subscription
{
    public function __construct(
        private int $id,
        private string $subscriberIdentifier,
        private int $planId,
        private int $planRegimeId,
        private ?DateTime $graceEndedAt,
        private ?DateTime $trialEndedAt,
        private ?DateTime $renewedAt,
        private ?DateTime $expiredAt,
        private DateTime $createdAt,
        private DateTime $updatedAt,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubscriberIdentifier(): string
    {
        return $this->subscriberIdentifier;
    }

    public function getPlanId(): int
    {
        return $this->planId;
    }

    public function getPlanRegimeId(): int
    {
        return $this->planRegimeId;
    }

    public function getGraceEndedAt(): ?DateTime
    {
        return $this->graceEndedAt;
    }

    public function getTrialEndedAt(): ?DateTime
    {
        return $this->trialEndedAt;
    }

    public function getRenewedAt(): ?DateTime
    {
        return $this->renewedAt;
    }

    public function getExpiredAt(): ?DateTime
    {
        return $this->expiredAt;
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
