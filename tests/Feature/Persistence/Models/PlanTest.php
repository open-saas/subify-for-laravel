<?php

namespace Tests\Feature\Persistence\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Persistence\Models\Plan;
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
        $plan = Plan::create([
            'name' => 'Test Plan',
        ]);

        $plan->delete();

        $this->assertSoftDeleted('plans', [
            'id' => $plan->id,
            'name' => $plan->name,
        ]);
    }
}
