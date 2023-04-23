<?php

namespace Tests\Fixtures;

use OpenSaaS\Subify\Entities\PlanRegime;

class PlanRegimeFixture
{
    public static function create(array $attributes = []): PlanRegime
    {
        return new PlanRegime(...array_merge([
            'id' => fake()->numberBetween(1, 100),
            'planId' => fake()->numberBetween(1, 100),
            'name' => fake()->word(),
            'price' => fake()->randomFloat(2, 1, 100),
            'periodicity' => \DateInterval::createFromDateString(fake()->randomElement(['1 month', '1 year'])),
            'grace' => \DateInterval::createFromDateString(fake()->randomElement(['1 month', '1 year'])),
            'trial' => \DateInterval::createFromDateString(fake()->randomElement(['1 month', '1 year'])),
        ], $attributes));
    }
}
