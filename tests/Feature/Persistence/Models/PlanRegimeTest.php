<?php

namespace Tests\Feature\Persistence\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use OpenSaaS\Subify\Enums\PeriodicityUnit;
use OpenSaaS\Subify\Persistence\Models\Plan;
use OpenSaaS\Subify\Persistence\Models\PlanRegime;
use Tests\Feature\TestCase;

class PlanRegimeTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_it_can_be_created(): void
    {
        $plan = Plan::factory()->create();

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
        $planRegime = PlanRegime::factory()->create();

        $planRegime->delete();

        $this->assertSoftDeleted($planRegime);
    }

    public function test_it_belongs_to_a_plan(): void
    {
        $plan = Plan::factory()->create();

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

    /**
     * @dataProvider periodicityUnitProvider
     */
    public function test_it_casts_periodicity_unit(PeriodicityUnit $unit): void
    {
        $planRegime = PlanRegime::factory()
            ->create(['periodicity_unit' => $unit]);

        $this->assertInstanceOf(PeriodicityUnit::class, $planRegime->periodicity_unit);
        $this->assertEquals($unit->value, $planRegime->periodicity_unit->value);
    }

    public function test_it_throw_exception_when_periodicity_unit_is_invalid(): void
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('"invalid" is not a valid backing value for enum "OpenSaaS\Subify\Enums\PeriodicityUnit"');

        PlanRegime::factory()->create(['periodicity_unit' => 'invalid']);
    }

    /**
     * @dataProvider periodicityUnitProvider
     */
    public function test_it_casts_grace_unit(PeriodicityUnit $unit): void
    {
        $planRegime = PlanRegime::factory()
            ->create(['grace_unit' => $unit]);

        $this->assertInstanceOf(PeriodicityUnit::class, $planRegime->grace_unit);
        $this->assertEquals($unit->value, $planRegime->grace_unit->value);
    }

    public function test_it_throw_exception_when_grace_unit_is_invalid(): void
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('"invalid" is not a valid backing value for enum "OpenSaaS\Subify\Enums\PeriodicityUnit"');

        PlanRegime::factory()->create(['grace_unit' => 'invalid']);
    }

    /**
     * @dataProvider periodicityUnitProvider
     */
    public function test_it_casts_trial_unit(PeriodicityUnit $unit): void
    {
        $planRegime = PlanRegime::factory()
            ->create(['trial_unit' => $unit]);

        $this->assertInstanceOf(PeriodicityUnit::class, $planRegime->trial_unit);
        $this->assertEquals($unit->value, $planRegime->trial_unit->value);
    }

    public function test_it_throw_exception_when_trial_unit_is_invalid(): void
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('"invalid" is not a valid backing value for enum "OpenSaaS\Subify\Enums\PeriodicityUnit"');

        PlanRegime::factory()->create(['trial_unit' => 'invalid']);
    }

    public static function periodicityUnitProvider(): array
    {
        return [
            'day' => [PeriodicityUnit::Day],
            'week' => [PeriodicityUnit::Week],
            'month' => [PeriodicityUnit::Month],
            'year' => [PeriodicityUnit::Year],
        ];
    }
}
