<?php

namespace Tests\Feature\Repositories\Eloquent\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use OpenSaaS\Subify\Enums\PeriodicityUnit;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Plan;
use OpenSaaS\Subify\Repositories\Eloquent\Models\PlanRegime;
use Tests\Feature\TestCase;

/**
 * @internal
 */
class PlanRegimeTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testItCanBeCreated(): void
    {
        $plan = Plan::factory()->create();

        $planRegime = PlanRegime::create([
            'plan_id' => $plan->id,
            'name' => 'Test Plan Regime',
            'price' => 100,
            'periodicity' => \DateInterval::createFromDateString('1 month'),
            'grace' => \DateInterval::createFromDateString('1 month'),
            'trial' => \DateInterval::createFromDateString('1 month'),
        ]);

        $this->assertDatabaseHas('plan_regimes', [
            'id' => $planRegime->id,
            'plan_id' => $plan->id,
            'name' => $planRegime->name,
            'price' => $planRegime->price,
            'periodicity' => 'P0Y1M0DT0H0M0S',
            'grace' => 'P0Y1M0DT0H0M0S',
            'trial' => 'P0Y1M0DT0H0M0S',
        ]);
    }

    public function testItSoftDeletes(): void
    {
        $planRegime = PlanRegime::factory()->create();

        $planRegime->delete();

        $this->assertSoftDeleted($planRegime);
    }

    public function testItBelongsToAPlan(): void
    {
        $plan = Plan::factory()->create();
        $planRegime = PlanRegime::create(['plan_id' => $plan->id]);

        $this->assertEquals($plan->id, $planRegime->plan->id);
    }

    public function testItHasAMethodToConvertToEntity(): void
    {
        $planRegime = PlanRegime::factory()->create();

        $entity = $planRegime->toEntity();

        $this->assertEquals($planRegime->id, $entity->getId());
        $this->assertEquals($planRegime->plan_id, $entity->getPlanId());
        $this->assertEquals($planRegime->periodicity, $entity->getPeriodicity());
        $this->assertEquals($planRegime->grace, $entity->getGrace());
        $this->assertEquals($planRegime->trial, $entity->getTrial());
    }

    public function testItPassesNullPeriodicityGraceAndTrialToEntity(): void
    {
        $planRegime = PlanRegime::factory()
            ->create([
                'periodicity' => null,
                'grace' => null,
                'trial' => null,
            ]);

        $entity = $planRegime->toEntity();

        $this->assertNull($entity->getPeriodicity());
        $this->assertNull($entity->getGrace());
        $this->assertNull($entity->getTrial());
    }
}
