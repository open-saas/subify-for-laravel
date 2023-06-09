<?php

namespace Tests\Feature\Repositories\Eloquent\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Entities\BenefitPlan as BenefitPlanEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Benefit;
use OpenSaaS\Subify\Repositories\Eloquent\Models\BenefitPlan;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Plan;
use Tests\Feature\TestCase;

/**
 * @internal
 */
class BenefitPlanTest extends TestCase
{
    use RefreshDatabase;

    public function testItCanBeCreated(): void
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

    public function testItDoesNotSoftDeletes(): void
    {
        $benefitPlan = BenefitPlan::factory()->create();

        $benefitPlan->delete();

        $this->assertDatabaseMissing('benefit_plan', [
            'id' => $benefitPlan->id,
        ]);
    }

    public function testItBelongsToABenefit(): void
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

    public function testItBelongsToAPlan(): void
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

    public function testItHasAMethodToConvertToEntity(): void
    {
        /** @var BenefitPlan $benefitPlan */
        $benefitPlan = BenefitPlan::factory()->create();
        $benefitPlanEntity = $benefitPlan->toEntity();

        $this->assertInstanceOf(BenefitPlanEntity::class, $benefitPlanEntity);
        $this->assertEquals($benefitPlan->id, $benefitPlanEntity->getId());
        $this->assertEquals($benefitPlan->benefit_id, $benefitPlanEntity->getBenefitId());
        $this->assertEquals($benefitPlan->plan_id, $benefitPlanEntity->getPlanId());
        $this->assertEquals($benefitPlan->charges, $benefitPlanEntity->getCharges());
        $this->assertEquals($benefitPlan->is_unlimited, $benefitPlanEntity->isUnlimited());
    }
}
