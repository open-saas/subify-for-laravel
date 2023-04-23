<?php

namespace Tests\Fixtures;

use OpenSaaS\Subify\Entities\Benefit;

class BenefitFixture
{
    public static function create(array $attributes = []): Benefit
    {
        return new Benefit(...array_merge([
            'id' => fake()->numberBetween(1, 100),
            'name' => fake()->word(),
            'isConsumable' => true,
            'isQuota' => false,
            'periodicity' => \DateInterval::createFromDateString(fake()->randomElement(['1 month', '1 year'])),
        ], $attributes));
    }
}
