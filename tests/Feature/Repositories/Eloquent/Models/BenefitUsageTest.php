<?php

namespace Tests\Feature\Repositories\Eloquent\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Entities\BenefitUsage as BenefitUsageEntity;
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

    public function testItHasAScopeToOnlyReturnNotExpired(): void
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

        $this->assertEmpty(BenefitUsage::withoutExpired()->find($expiredBenefitUsage->id));
        $this->assertNotEmpty(BenefitUsage::withoutExpired()->find($notExpiredBenefitUsage->id));
        $this->assertNotEmpty(BenefitUsage::withoutExpired()->find($benefitUsageWithoutExpiredAt->id));
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

    public function testItByDefaultReturnsExpired(): void
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

        $this->assertNotEmpty(BenefitUsage::find($expiredBenefitUsage->id));
        $this->assertNotEmpty(BenefitUsage::find($notExpiredBenefitUsage->id));
        $this->assertNotEmpty(BenefitUsage::find($benefitUsageWithoutExpiredAt->id));
    }

    public function testItHasAMethodToConvertToEntity(): void
    {
        /** @var BenefitUsage $benefitUsage */
        $benefitUsage = BenefitUsage::factory()->create();
        $benefitUsageEntity = $benefitUsage->toEntity();

        $expectedSubscriberIdentifier = $benefitUsage->subscriber_type.':'.$benefitUsage->subscriber_id;

        $this->assertInstanceOf(BenefitUsageEntity::class, $benefitUsageEntity);
        $this->assertEquals($benefitUsage->id, $benefitUsageEntity->getId());
        $this->assertEquals($expectedSubscriberIdentifier, $benefitUsageEntity->getSubscriberIdentifier());
        $this->assertEquals($benefitUsage->benefit_id, $benefitUsageEntity->getBenefitId());
        $this->assertEquals($benefitUsage->amount, $benefitUsageEntity->getAmount());
        $this->assertEquals($benefitUsage->expired_at, $benefitUsageEntity->getExpiredAt());
    }
}
