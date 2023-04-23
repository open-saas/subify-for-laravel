<?php

namespace Tests\Fixtures;

use OpenSaaS\Subify\Entities\BenefitPlan;

class BenefitPlanFixture
{
    public static function create(array $attributes = []): BenefitPlan
    {
        return new BenefitPlan(...array_merge([
            'id' => fake()->numberBetween(1, 100),
            'benefitId' => fake()->numberBetween(1, 100),
            'planId' => fake()->numberBetween(1, 100),
            'charges' => fake()->numberBetween(1, 100),
            'isUnlimited' => false,
        ], $attributes));
    }
}
