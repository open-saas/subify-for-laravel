<?php

namespace OpenSaaS\Subify\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use OpenSaaS\Subify\Enums\PeriodicityUnit;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Benefit;

class BenefitFactory extends Factory
{
    protected $model = Benefit::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(asText: true),
            'is_consumable' => $this->faker->boolean(),
            'is_quota' => $this->faker->boolean(),
            'periodicity' => $this->faker->numberBetween(1, 12),
            'periodicity_unit' => $this->faker->randomElement(PeriodicityUnit::cases()),
        ];
    }
}
