<?php

namespace OpenSaaS\Subify\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use OpenSaaS\Subify\Persistence\Models\Plan;
use OpenSaaS\Subify\Persistence\Models\PlanRegime;
use OpenSaaS\Subify\Persistence\Models\Subscription;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'plan_regime_id' => PlanRegime::factory(),
            'subscriber_id' => $this->faker->randomNumber(),
            'subscriber_type' => $this->faker->word(),
            'grace_ended_at' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            'trial_ended_at' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            'renewed_at' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            'expired_at' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
        ];
    }
}
