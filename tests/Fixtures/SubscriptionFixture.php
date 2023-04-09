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
            'graceEndedAt' => fake()->dateTime(),
            'trialEndedAt' => fake()->dateTime(),
            'renewedAt' => fake()->dateTime(),
            'expiredAt' => fake()->dateTime(),
            'createdAt' => fake()->dateTime(),
            'updatedAt' => fake()->dateTime(),
        ], $attributes));
    }
}
