<?php

namespace OpenSaaS\Subify\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Benefit;
use OpenSaaS\Subify\Repositories\Eloquent\Models\BenefitPlan;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Plan;

class BenefitPlanFactory extends Factory
{
    protected $model = BenefitPlan::class;

    public function definition(): array
    {
        return [
            'benefit_id' => Benefit::factory(),
            'plan_id' => Plan::factory(),
            'charges' => $this->faker->randomFloat(2, 0, 100),
            'is_unlimited' => $this->faker->boolean(),
        ];
    }
}
