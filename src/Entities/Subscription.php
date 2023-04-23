<?php

namespace OpenSaaS\Subify\Entities;

final class Subscription
{
    public function __construct(
        private int $id,
        private string $subscriberIdentifier,
        private int $planId,
        private int $planRegimeId,
        private \DateTimeImmutable $startedAt,
        private ?\DateTimeImmutable $graceEndedAt,
        private ?\DateTimeImmutable $trialEndedAt,
        private ?\DateTimeImmutable $renewedAt,
        private ?\DateTimeImmutable $expiredAt,
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

    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getGraceEndedAt(): ?\DateTimeImmutable
    {
        return $this->graceEndedAt;
    }

    public function getTrialEndedAt(): ?\DateTimeImmutable
    {
        return $this->trialEndedAt;
    }

    public function getRenewedAt(): ?\DateTimeImmutable
    {
        return $this->renewedAt;
    }

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function isExpired(): bool
    {
        if (empty($this->expiredAt)) {
            return false;
        }

        return $this->expiredAt->getTimestamp() < time();
    }

    public function isNotExpired(): bool
    {
        return !$this->isExpired();
    }

    public function isGrace(): bool
    {
        if (empty($this->graceEndedAt)) {
            return false;
        }

        return $this->graceEndedAt->getTimestamp() > time();
    }

    public function isTrial(): bool
    {
        if (empty($this->trialEndedAt)) {
            return false;
        }

        return $this->trialEndedAt->getTimestamp() > time();
    }

    public function isActive(): bool
    {
        return $this->isNotExpired() or $this->isGrace() or $this->isTrial();
    }

    public function isNotActive(): bool
    {
        return !$this->isActive();
    }
}
