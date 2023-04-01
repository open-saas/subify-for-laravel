<?php

namespace Tests\Feature\Persistence\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Persistence\Models\Benefit;
use OpenSaaS\Subify\Persistence\Models\BenefitPlan;
use OpenSaaS\Subify\Persistence\Models\Plan;
use Tests\Feature\TestCase;

class BenefitPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created(): void
    {
        $benefit = Benefit::factory()->create();
        $plan = Plan::factory()->create();

        $benefitPlan = BenefitPlan::create([
            'benefit_id' => $benefit->id,
            'plan_id' => $plan->id,
            'charges' => 100,
            'is_unlimited' => false,
        ]);

        $this->assertDatabaseHas('benefit_plan', [
            'id' => $benefitPlan->id,
            'benefit_id' => $benefit->id,
            'plan_id' => $plan->id,
            'charges' => $benefitPlan->charges,
            'is_unlimited' => $benefitPlan->is_unlimited,
        ]);
    }

    public function test_it_does_not_soft_deletes(): void
    {
        $benefitPlan = BenefitPlan::factory()->create();

        $benefitPlan->delete();

        $this->assertDatabaseMissing('benefit_plan', [
            'id' => $benefitPlan->id,
        ]);
    }

    public function test_it_belongs_to_a_benefit(): void
    {
        $benefit = Benefit::factory()->create();
        $plan = Plan::factory()->create();

        $benefitPlan = BenefitPlan::create([
            'benefit_id' => $benefit->id,
            'plan_id' => $plan->id,
            'charges' => 100,
            'is_unlimited' => false,
        ]);

        $this->assertEquals($benefit->id, $benefitPlan->benefit->id);
    }

    public function test_it_belongs_to_a_plan(): void
    {
        $benefit = Benefit::factory()->create();
        $plan = Plan::factory()->create();

        $benefitPlan = BenefitPlan::create([
            'benefit_id' => $benefit->id,
            'plan_id' => $plan->id,
            'charges' => 100,
            'is_unlimited' => false,
        ]);

        $this->assertEquals($plan->id, $benefitPlan->plan->id);
    }
}
