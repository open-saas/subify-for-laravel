<?php

namespace Tests\Feature\Persistence\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Persistence\Models\Benefit;
use OpenSaaS\Subify\Persistence\Models\BenefitUsage;
use Tests\Feature\TestCase;

class BenefitUsageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created(): void
    {
        $benefit = Benefit::factory()->create();

        $benefitUsage = BenefitUsage::create([
            'benefit_id' => $benefit->id,
            'amount' => 100,
            'subscriber_id' => 1,
            'subscriber_type' => 'user',
            'expired_at' => now()->addDays(30),
        ]);

        $this->assertDatabaseHas('benefit_usages', [
            'id' => $benefitUsage->id,
            'benefit_id' => $benefit->id,
            'amount' => $benefitUsage->amount,
            'subscriber_id' => $benefitUsage->subscriber_id,
            'subscriber_type' => $benefitUsage->subscriber_type,
            'expired_at' => $benefitUsage->expired_at,
        ]);
    }

    public function test_it_soft_deletes(): void
    {
        $benefitUsage = BenefitUsage::factory()->create();

        $benefitUsage->delete();

        $this->assertSoftDeleted($benefitUsage);
    }

    public function test_it_belongs_to_a_benefit(): void
    {
        $benefit = Benefit::factory()->create();

        $benefitUsage = BenefitUsage::create([
            'benefit_id' => $benefit->id,
            'amount' => 100,
            'subscriber_id' => 1,
            'subscriber_type' => 'user',
            'expired_at' => now()->addDays(30),
        ]);

        $this->assertEquals($benefit->id, $benefitUsage->benefit->id);
    }
}
