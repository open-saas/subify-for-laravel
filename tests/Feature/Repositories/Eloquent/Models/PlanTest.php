<?php

namespace Tests\Feature\Repositories\Eloquent\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Plan;
use OpenSaaS\Subify\Repositories\Eloquent\Models\PlanRegime;
use Tests\Feature\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function testItCanBeCreated(): void
    {
        $plan = Plan::create([
            'name' => 'Test Plan',
        ]);

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => $plan->name,
        ]);
    }

    public function testItSoftDeletes(): void
    {
        $plan = Plan::factory()->create();

        $plan->delete();

        $this->assertSoftDeleted($plan);
    }

    public function testItHasRegimes(): void
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

    public function testItHasAMethodToConvertToEntity(): void
    {
        $plan = Plan::factory()->create();

        $planEntity = $plan->toEntity();

        $this->assertEquals($plan->id, $planEntity->getId());
        $this->assertEquals($plan->name, $planEntity->getName());
        $this->assertEquals([], $planEntity->getRegimes());
        $this->assertEquals($plan->created_at, $planEntity->getCreatedAt());
        $this->assertEquals($plan->updated_at, $planEntity->getUpdatedAt());
    }

    public function testItAddsRegimesToEntityIfLoaded(): void
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

    public function testItAddsPassedRegimesToEntityIfRelationNotLoaded(): void
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
