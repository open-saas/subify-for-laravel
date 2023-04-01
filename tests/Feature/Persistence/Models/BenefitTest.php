<?php

namespace Tests\Feature\Persistence\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Persistence\Models\Benefit;
use Tests\Feature\TestCase;

class BenefitTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created(): void
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

    public function test_it_soft_deletes(): void
    {
        $benefit = Benefit::factory()->create();

        $benefit->delete();

        $this->assertSoftDeleted($benefit);
    }
}
