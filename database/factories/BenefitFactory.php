<?php

namespace OpenSaaS\Subify\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
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
            'periodicity' => new \DateInterval('P0Y1M0DT0H0M0S'),
        ];
    }
}
