<?php

namespace Tests\Feature\Repositories\Eloquent\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Entities\Benefit as BenefitEntity;
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
            'periodicity' => \DateInterval::createFromDateString('1 month'),
        ]);

        $this->assertDatabaseHas('benefits', [
            'id' => $benefit->id,
            'name' => $benefit->name,
            'is_consumable' => $benefit->is_consumable,
            'is_quota' => $benefit->is_quota,
            'periodicity' => 'P0Y1M0DT0H0M0S',
        ]);
    }

    public function testItSoftDeletes(): void
    {
        $benefit = Benefit::factory()->create();

        $benefit->delete();

        $this->assertSoftDeleted($benefit);
    }

    public function testItHasAMethodToConvertToEntity(): void
    {
        /** @var Benefit $benefit */
        $benefit = Benefit::factory()->create();
        $benefitEntity = $benefit->toEntity();

        $this->assertInstanceOf(BenefitEntity::class, $benefitEntity);
        $this->assertEquals($benefit->id, $benefitEntity->getId());
        $this->assertEquals($benefit->name, $benefitEntity->getName());
        $this->assertEquals($benefit->is_consumable, $benefitEntity->isConsumable());
        $this->assertEquals($benefit->is_quota, $benefitEntity->isQuota());
        $this->assertEquals($benefit->periodicity, $benefitEntity->getPeriodicity());
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
        $this->assertEquals($benefit->periodicity, $benefitEntity->getPeriodicity());
    }
}
