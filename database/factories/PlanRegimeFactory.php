<?php

namespace OpenSaaS\Subify\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use OpenSaaS\Subify\Enums\PeriodicityUnit;
use OpenSaaS\Subify\Persistence\Models\Plan;
use OpenSaaS\Subify\Persistence\Models\PlanRegime;

class PlanRegimeFactory extends Factory
{
    protected $model = PlanRegime::class;

    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'name' => $this->faker->words(asText: true),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'periodicity' => $this->faker->numberBetween(1, 12),
            'periodicity_unit' => $this->faker->randomElement(PeriodicityUnit::cases()),
            'grace' => $this->faker->numberBetween(1, 12),
            'grace_unit' => $this->faker->randomElement(PeriodicityUnit::cases()),
            'trial' => $this->faker->numberBetween(1, 12),
            'trial_unit' => $this->faker->randomElement(PeriodicityUnit::cases()),
        ];
    }
}
