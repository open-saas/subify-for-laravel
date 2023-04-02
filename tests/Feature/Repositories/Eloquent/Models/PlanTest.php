<?php

namespace Tests\Feature\Repositories\Eloquent\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Plan;
use OpenSaaS\Subify\Repositories\Eloquent\Models\PlanRegime;
use Tests\Feature\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created(): void
    {
        $plan = Plan::create([
            'name' => 'Test Plan',
        ]);

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => $plan->name,
        ]);
    }

    public function test_it_soft_deletes(): void
    {
        $plan = Plan::factory()->create();

        $plan->delete();

        $this->assertSoftDeleted($plan);
    }

    public function test_it_has_regimes(): void
    {
        $plan = Plan::factory()->create();

        $planRegime = PlanRegime::factory()
            ->for($plan)
            ->create();

        $this->assertDatabaseHas('plan_regimes', [
            'id' => $planRegime->id,
            'name' => $planRegime->name,
            'plan_id' => $plan->id,
        ]);

        $this->assertEquals($plan->regimes->first()->id, $planRegime->id);
    }
}
