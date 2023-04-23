<?php

namespace OpenSaaS\Subify\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Plan;
use OpenSaaS\Subify\Repositories\Eloquent\Models\PlanRegime;

class PlanRegimeFactory extends Factory
{
    protected $model = PlanRegime::class;

    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'name' => $this->faker->words(asText: true),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'periodicity' => new \DateInterval('P0Y1M0DT0H0M0S'),
            'grace' => new \DateInterval('P0Y1M0DT0H0M0S'),
            'trial' => new \DateInterval('P0Y1M0DT0H0M0S'),
        ];
    }
}
