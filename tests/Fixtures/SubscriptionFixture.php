<?php

namespace Tests\Fixtures;

use OpenSaaS\Subify\Entities\Subscription;

class SubscriptionFixture
{
    public static function create(array $attributes = []): Subscription
    {
        return new Subscription(...array_merge([
            'id' => fake()->numberBetween(1, 100),
            'subscriberIdentifier' => fake()->uuid(),
            'planId' => fake()->numberBetween(1, 100),
            'planRegimeId' => fake()->numberBetween(1, 100),
            'startedAt' => \DateTimeImmutable::createFromInterface(fake()->dateTime()),
            'graceEndedAt' => \DateTimeImmutable::createFromInterface(fake()->dateTime()),
            'trialEndedAt' => \DateTimeImmutable::createFromInterface(fake()->dateTime()),
            'renewedAt' => \DateTimeImmutable::createFromInterface(fake()->dateTime()),
            'expiredAt' => \DateTimeImmutable::createFromInterface(fake()->dateTime()),
        ], $attributes));
    }
}
