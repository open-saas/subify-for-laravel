<?php

namespace Tests\Feature\Repositories\Eloquent\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Benefit;
use OpenSaaS\Subify\Repositories\Eloquent\Models\BenefitUsage;
use Tests\Feature\TestCase;

/**
 * @internal
 */
class BenefitUsageTest extends TestCase
{
    use RefreshDatabase;

    public function testItCanBeCreated(): void
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

    public function testItSoftDeletes(): void
    {
        $benefitUsage = BenefitUsage::factory()->create();

        $benefitUsage->delete();

        $this->assertSoftDeleted($benefitUsage);
    }

    public function testItBelongsToABenefit(): void
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

    public function testItHasAGlobalScopeToOnlyReturnNotExpired(): void
    {
        $benefit = Benefit::factory()->create();

        $expiredBenefitUsage = BenefitUsage::factory()
            ->for($benefit)
            ->create([
                'expired_at' => now()->subDays(30),
            ]);

        $notExpiredBenefitUsage = BenefitUsage::factory()
            ->for($benefit)
            ->create([
                'expired_at' => now()->addDays(30),
            ]);

        $benefitUsageWithoutExpiredAt = BenefitUsage::factory()
            ->for($benefit)
            ->create([
                'expired_at' => null,
            ]);

        $this->assertEmpty(BenefitUsage::find($expiredBenefitUsage->id));
        $this->assertNotEmpty(BenefitUsage::find($notExpiredBenefitUsage->id));
        $this->assertNotEmpty(BenefitUsage::find($benefitUsageWithoutExpiredAt->id));
    }

    public function testItHasAScopeToOnlyReturnExpired(): void
    {
        $benefit = Benefit::factory()->create();

        $expiredBenefitUsage = BenefitUsage::factory()
            ->for($benefit)
            ->create([
                'expired_at' => now()->subDays(30),
            ]);

        $notExpiredBenefitUsage = BenefitUsage::factory()
            ->for($benefit)
            ->create([
                'expired_at' => now()->addDays(30),
            ]);

        $benefitUsageWithoutExpiredAt = BenefitUsage::factory()
            ->for($benefit)
            ->create([
                'expired_at' => null,
            ]);

        $this->assertNotEmpty(BenefitUsage::onlyExpired()->find($expiredBenefitUsage->id));
        $this->assertEmpty(BenefitUsage::onlyExpired()->find($notExpiredBenefitUsage->id));
        $this->assertEmpty(BenefitUsage::onlyExpired()->find($benefitUsageWithoutExpiredAt->id));
    }

    public function testItHasAScopeToReturnWithExpired(): void
    {
        $benefit = Benefit::factory()->create();

        $expiredBenefitUsage = BenefitUsage::factory()
            ->for($benefit)
            ->create([
                'expired_at' => now()->subDays(30),
            ]);

        $notExpiredBenefitUsage = BenefitUsage::factory()
            ->for($benefit)
            ->create([
                'expired_at' => now()->addDays(30),
            ]);

        $benefitUsageWithoutExpiredAt = BenefitUsage::factory()
            ->for($benefit)
            ->create([
                'expired_at' => null,
            ]);

        $this->assertNotEmpty(BenefitUsage::withExpired()->find($expiredBenefitUsage->id));
        $this->assertNotEmpty(BenefitUsage::withExpired()->find($notExpiredBenefitUsage->id));
        $this->assertNotEmpty(BenefitUsage::withExpired()->find($benefitUsageWithoutExpiredAt->id));
    }
}
