<?php

namespace Repositories\Eloquent;

use Carbon\Carbon;
use OpenSaaS\Subify\Entities\BenefitUsage as BenefitUsageEntity;
use OpenSaaS\Subify\Repositories\Eloquent\BenefitUsageRepository;
use OpenSaaS\Subify\Repositories\Eloquent\Models\BenefitUsage;
use Tests\Feature\TestCase;

/**
 * @internal
 */
class BenefitUsageRepositoryTest extends TestCase
{
    private BenefitUsageRepository $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(BenefitUsageRepository::class);
    }

    public function testGetReturnBenefitUsages(): void
    {
        /** @var BenefitUsage $benefitUsage */
        $benefitUsage = BenefitUsage::factory()
            ->create([
                'expired_at' => null,
            ]);

        /** @var BenefitUsage $unwantedBenefitUsage */
        $unwantedBenefitUsage = BenefitUsage::factory()
            ->create();

        $benefitUsages = $this->repository->get($benefitUsage->getSubscriberIdentifier());

        $this->assertCount(1, $benefitUsages);
        $this->assertNotContains($unwantedBenefitUsage->toEntity(), $benefitUsages);
        $this->assertEquals([$benefitUsage->toEntity()], $benefitUsages);
    }

    public function testGetReturnBenefitUsagesWithExpired(): void
    {
        /** @var BenefitUsage $benefitUsage */
        $expiredBenefitUsage = BenefitUsage::factory()
            ->create([
                'expired_at' => now()->subDay(),
            ]);

        $benefitUsages = $this->repository->get($expiredBenefitUsage->getSubscriberIdentifier());

        $this->assertCount(1, $benefitUsages);
        $this->assertEquals([$expiredBenefitUsage->toEntity()], $benefitUsages);
    }

    public function testInsertAddsRowToDatabase(): void
    {
        $benefitUsage = $this->repository->insert('class:id', 1, 1.0, null);

        $this->assertDatabaseHas('benefit_usages', [
            'id' => $benefitUsage->getId(),
            'subscriber_id' => 'id',
            'subscriber_type' => 'class',
            'benefit_id' => 1,
            'amount' => 1.0,
            'expired_at' => null,
        ]);
    }

    public function testInsertReturnsBenefitUsageEntity(): void
    {
        $benefitUsage = $this->repository->insert('class:id', 1, 1.0, null);

        $this->assertInstanceOf(BenefitUsageEntity::class, $benefitUsage);
        $this->assertEquals('class:id', $benefitUsage->getSubscriberIdentifier());
        $this->assertEquals(1, $benefitUsage->getBenefitId());
        $this->assertEquals(1.0, $benefitUsage->getAmount());
        $this->assertNull($benefitUsage->getExpiredAt());
    }

    public function testSaveUpdatesRowInDatabase(): void
    {
        Carbon::setTestNow(now());

        /** @var BenefitUsage $benefitUsage */
        $benefitUsage = BenefitUsage::factory()
            ->create([
                'amount' => 1.0,
                'expired_at' => now()->subDay(),
            ]);

        $benefitUsageEntity = $benefitUsage->toEntity();
        $benefitUsageEntity->increase(1.5);
        $benefitUsageEntity->setExpiredAt(now()->addDay()->toDateTimeImmutable());

        $this->repository->save($benefitUsageEntity);

        $this->assertDatabaseHas('benefit_usages', [
            'id' => $benefitUsage->id,
            'amount' => 2.5,
            'expired_at' => now()->addDay()->toDateTimeString(),
        ]);
    }
}
