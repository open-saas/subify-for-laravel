<?php

namespace Tests\Feature\Repositories\Eloquent\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Entities\Benefit as BenefitEntity;
use OpenSaaS\Subify\Enums\PeriodicityUnit;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Benefit;
use Tests\Feature\TestCase;

/**
 * @internal
 */
class BenefitTest extends TestCase
{
    use RefreshDatabase;

    public function testItCanBeCreated(): void
    {
        $benefit = Benefit::create([
            'name' => 'Test Benefit',
            'is_consumable' => true,
            'is_quota' => false,
            'periodicity' => 1,
            'periodicity_unit' => 'month',
        ]);

        $this->assertDatabaseHas('benefits', [
            'id' => $benefit->id,
            'name' => $benefit->name,
            'is_consumable' => $benefit->is_consumable,
            'is_quota' => $benefit->is_quota,
            'periodicity' => $benefit->periodicity,
            'periodicity_unit' => $benefit->periodicity_unit,
        ]);
    }

    public function testItSoftDeletes(): void
    {
        $benefit = Benefit::factory()->create();

        $benefit->delete();

        $this->assertSoftDeleted($benefit);
    }

    /**
     * @dataProvider periodicityUnitProvider
     */
    public function testItCastsPeriodicityUnit(PeriodicityUnit $unit): void
    {
        $benefit = Benefit::factory()
            ->create(['periodicity_unit' => $unit]);

        $this->assertInstanceOf(PeriodicityUnit::class, $benefit->periodicity_unit);
        $this->assertEquals($unit->value, $benefit->periodicity_unit->value);
    }

    public function testItThrowExceptionWhenPeriodicityUnitIsInvalid(): void
    {
        $this->expectException(\ValueError::class);

        Benefit::factory()->create(['periodicity_unit' => 'invalid']);
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

    public function testItHasAMethodToConvertToEntity(): void
    {
        /** @var Benefit $benefit */
        $benefit = Benefit::factory()->create();
        $benefitEntity = $benefit->toEntity();

        $expectedPeriodicity = \DateInterval::createFromDateString(
            "{$benefit->periodicity} {$benefit->periodicity_unit->value}"
        );

        $this->assertInstanceOf(BenefitEntity::class, $benefitEntity);
        $this->assertEquals($benefit->id, $benefitEntity->getId());
        $this->assertEquals($benefit->name, $benefitEntity->getName());
        $this->assertEquals($benefit->is_consumable, $benefitEntity->isConsumable());
        $this->assertEquals($benefit->is_quota, $benefitEntity->isQuota());
        $this->assertEquals($expectedPeriodicity, $benefitEntity->getPeriodicity());
    }

    public function testItHandlesNullPeriodicityWhenConvertingToEntity(): void
    {
        /** @var Benefit $benefit */
        $benefit = Benefit::factory()->create(['periodicity' => null]);
        $benefitEntity = $benefit->toEntity();

        $this->assertInstanceOf(BenefitEntity::class, $benefitEntity);
        $this->assertEquals($benefit->id, $benefitEntity->getId());
        $this->assertEquals($benefit->name, $benefitEntity->getName());
        $this->assertEquals($benefit->is_consumable, $benefitEntity->isConsumable());
        $this->assertEquals($benefit->is_quota, $benefitEntity->isQuota());
        $this->assertNull($benefitEntity->getPeriodicity());
    }
}
