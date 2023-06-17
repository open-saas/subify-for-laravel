<?php

namespace OpenSaaS\Subify\Contracts;

use OpenSaaS\Subify\Exceptions\AlreadySubscribedException;
use OpenSaaS\Subify\Exceptions\CantConsumeException;
use OpenSaaS\Subify\Exceptions\PlanNotFoundException;
use OpenSaaS\Subify\Exceptions\PlanRegimeNotFoundException;
use OpenSaaS\Subify\Exceptions\SubscriptionNotFoundException;

interface SubscriptionManager
{
    public function hasBenefit(string $subscriberIdentifier, string $benefitName): bool;

    public function canConsume(string $subscriberIdentifier, string $benefitName, float $amount): bool;

    /**
     * @throws CantConsumeException if the desired amount is greater than the remaining amount of the benefit
     */
    public function consume(string $subscriberIdentifier, string $benefitName, float $amount): void;

    public function flushContext(): void;

    /**
     * @throws AlreadySubscribedException if the subscriber already has an active subscription
     * @throws PlanNotFoundException if the plan does not exist
     * @throws PlanRegimeNotFoundException if the plan regime does not exist
     */
    public function subscribeTo(
        string $subscriberIdentifier,
        int $planId,
        int $planRegimeId,
        \DateTimeInterface $startDate = new \DateTime,
    ): void;

    /**
     * @throws AlreadySubscribedException if the subscriber already has an active subscription
     * @throws PlanNotFoundException if the plan does not exist
     * @throws PlanRegimeNotFoundException if the plan regime does not exist
     */
    public function tryPlan(
        string $subscriberIdentifier,
        int $planId,
        int $planRegimeId,
        \DateTimeInterface $startDate = new \DateTime,
    ): void;

    /**
     * @throws PlanNotFoundException if the plan does not exist
     * @throws PlanRegimeNotFoundException if the plan regime does not exist
     * @throws SubscriptionNotFoundException if the plan regime does not exist
     */
    public function switchTo(
        string $subscriberIdentifier,
        int $planId,
        int $planRegimeId,
        bool $immediately = false,
    ): void;
}
