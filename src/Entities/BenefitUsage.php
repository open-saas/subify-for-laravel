<?php

namespace OpenSaaS\Subify\Entities;

class BenefitUsage
{
    public function __construct(
        private int $id,
        private string $subscriberIdentifier,
        private int $benefitId,
        private float $amount,
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

    public function getBenefitId(): int
    {
        return $this->benefitId;
    }

    public function getAmount(): float
    {
        return $this->amount;
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

    public function increase(float $amount): void
    {
        $this->amount += $amount;
    }

    public function clearUsage(): void
    {
        $this->amount = 0;
    }

    public function setExpiredAt(?\DateTimeImmutable $expiredAt): void
    {
        $this->expiredAt = $expiredAt;
    }
}
