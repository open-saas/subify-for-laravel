<?php

namespace Tests\Feature\Persistence\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Persistence\Models\Plan;
use OpenSaaS\Subify\Persistence\Models\PlanRegime;
use Tests\Feature\TestCase;

class PlanRegimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created(): void
    {
        $plan = Plan::create([
            'name' => 'Test Plan',
        ]);

        $planRegime = PlanRegime::create([
            'plan_id' => $plan->id,
            'name' => 'Test Plan Regime',
            'price' => 100,
            'periodicity' => 1,
            'periodicity_unit' => 'month',
            'grace' => 1,
            'grace_unit' => 'day',
            'trial' => 1,
            'trial_unit' => 'day',
        ]);

        $this->assertDatabaseHas('plan_regimes', [
            'id' => $planRegime->id,
            'plan_id' => $plan->id,
            'name' => $planRegime->name,
            'price' => $planRegime->price,
            'periodicity' => $planRegime->periodicity,
            'periodicity_unit' => $planRegime->periodicity_unit,
            'grace' => $planRegime->grace,
            'grace_unit' => $planRegime->grace_unit,
            'trial' => $planRegime->trial,
            'trial_unit' => $planRegime->trial_unit,
        ]);
    }

    public function test_it_soft_deletes(): void
    {
        $plan = Plan::create([
            'name' => 'Test Plan',
        ]);

        $planRegime = PlanRegime::create([
            'plan_id' => $plan->id,
            'name' => 'Test Plan Regime',
            'price' => 100,
            'periodicity' => 1,
            'periodicity_unit' => 'month',
            'grace' => 1,
            'grace_unit' => 'day',
            'trial' => 1,
            'trial_unit' => 'day',
        ]);

        $planRegime->delete();

        $this->assertSoftDeleted('plan_regimes', [
            'id' => $planRegime->id,
            'plan_id' => $plan->id,
            'name' => $planRegime->name,
            'price' => $planRegime->price,
            'periodicity' => $planRegime->periodicity,
            'periodicity_unit' => $planRegime->periodicity_unit,
            'grace' => $planRegime->grace,
            'grace_unit' => $planRegime->grace_unit,
            'trial' => $planRegime->trial,
            'trial_unit' => $planRegime->trial_unit,
        ]);
    }

    public function test_it_belongs_to_a_plan(): void
    {
        $plan = Plan::create([
            'name' => 'Test Plan',
        ]);

        $planRegime = PlanRegime::create([
            'plan_id' => $plan->id,
            'name' => 'Test Plan Regime',
            'price' => 100,
            'periodicity' => 1,
            'periodicity_unit' => 'month',
            'grace' => 1,
            'grace_unit' => 'day',
            'trial' => 1,
            'trial_unit' => 'day',
        ]);

        $this->assertEquals($plan->id, $planRegime->plan->id);
    }
}
