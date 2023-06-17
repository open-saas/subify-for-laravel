<?php

namespace Tests\Fixtures;

use OpenSaaS\Subify\Entities\Plan;

class PlanFixture
{
    public static function create(array $attributes = []): Plan
    {
        return new Plan(...array_merge([
            'id' => fake()->numberBetween(1, 100),
            'name' => fake()->name(),
        ], $attributes));
    }
}
