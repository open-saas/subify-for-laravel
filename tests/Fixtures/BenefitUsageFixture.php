<?php

namespace Tests\Fixtures;

use OpenSaaS\Subify\Entities\BenefitUsage;

class BenefitUsageFixture
{
    public static function create(array $attributes = []): BenefitUsage
    {
        return new BenefitUsage(...array_merge([
            'id' => fake()->numberBetween(1, 100),
            'subscriberIdentifier' => fake()->uuid(),
            'benefitId' => fake()->numberBetween(1, 100),
            'amount' => fake()->randomFloat(2, 0, 100),
            'expiredAt' => \DateTimeImmutable::createFromInterface(fake()->dateTime()),
        ], $attributes));
    }
}
