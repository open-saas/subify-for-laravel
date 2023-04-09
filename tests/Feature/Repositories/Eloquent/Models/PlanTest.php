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

    public function test_it_has_a_method_to_convert_to_entity(): void
    {
        $plan = Plan::factory()->create();

        $planEntity = $plan->toEntity();

        $this->assertEquals($plan->id, $planEntity->getId());
        $this->assertEquals($plan->name, $planEntity->getName());
        $this->assertEquals([], $planEntity->getRegimes());
        $this->assertEquals($plan->created_at, $planEntity->getCreatedAt());
        $this->assertEquals($plan->updated_at, $planEntity->getUpdatedAt());
    }

    public function test_it_adds_regimes_to_entity_if_loaded(): void
    {
        $plan = Plan::factory()->create();

        $regimes = PlanRegime::factory()
            ->for($plan)
            ->count(3)
            ->create();

        $expectedRegimes = $regimes->map->toEntity()->toArray();

        $plan->load('regimes');

        $planEntity = $plan->toEntity();

        $this->assertEquals($expectedRegimes, $planEntity->getRegimes());
    }

    public function test_it_adds_passed_regimes_to_entity_if_relation_not_loaded(): void
    {
        $plan = Plan::factory()->create();

        $regimes = PlanRegime::factory()
            ->for($plan)
            ->count(3)
            ->create();

        $expectedRegimes = $regimes->map->toEntity()->toArray();

        $planEntity = $plan->toEntity($expectedRegimes);

        $this->assertEquals($expectedRegimes, $planEntity->getRegimes());
    }
}
