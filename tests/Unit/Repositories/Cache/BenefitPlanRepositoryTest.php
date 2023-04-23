<?php

namespace Repositories\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery\LegacyMockInterface;
use OpenSaaS\Subify\Repositories\Cache\BenefitPlanRepository;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitPlanFixture;

/**
 * @internal
 */
class BenefitPlanRepositoryTest extends TestCase
{
    private LegacyMockInterface|CacheRepository $cacheRepository;

    private LegacyMockInterface|ConfigRepository $configRepository;

    private BenefitPlanRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $cacheFactory = \Mockery::mock(CacheFactory::class);
        $this->cacheRepository = \Mockery::mock(CacheRepository::class);
        $this->configRepository = \Mockery::mock(ConfigRepository::class);

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.benefit_plan.store')
            ->andReturn('cache-store');

        $cacheFactory
            ->shouldReceive('store')
            ->with('cache-store')
            ->andReturn($this->cacheRepository);

        $this->repository = new BenefitPlanRepository(
            $cacheFactory,
            $this->configRepository,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        \Mockery::close();
    }

    public function testGetsStoreOnConstructor(): void
    {
        $cacheFactory = \Mockery::mock(CacheFactory::class);
        $cacheRepository = \Mockery::mock(CacheRepository::class);
        $configRepository = \Mockery::mock(ConfigRepository::class);

        $configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit_plan.store')
            ->andReturn('cache-store');

        $cacheFactory
            ->shouldReceive('store')
            ->once()
            ->with('cache-store')
            ->andReturn($cacheRepository);

        new BenefitPlanRepository($cacheFactory, $configRepository);

        $this->assertTrue(true);
    }

    public function testAllReturnsBenefitPlan(): void
    {
        $expectedBenefitPlan = BenefitPlanFixture::create();

        $benefitPlanData = [
            'i' => $expectedBenefitPlan->getId(),
            'n' => $expectedBenefitPlan->getBenefitId(),
            'p' => $expectedBenefitPlan->getPlanId(),
            'q' => $expectedBenefitPlan->getCharges(),
            's' => $expectedBenefitPlan->isUnlimited(),
        ];

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:benefit_plans')
            ->andReturn([$benefitPlanData]);

        $actualBenefitPlans = $this->repository->all();

        $this->assertEquals([$expectedBenefitPlan], $actualBenefitPlans);
    }

    public function testAllReturnsEmptyArrayWhenCacheIsEmpty(): void
    {
        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:benefit_plans')
            ->andReturn([]);

        $actualBenefitPlans = $this->repository->all();

        $this->assertEquals([], $actualBenefitPlans);
    }

    public function testAllReturnsEmptyArrayWhenCacheIsNotFilled(): void
    {
        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:benefit_plans')
            ->andReturnNull();

        $actualBenefitPlans = $this->repository->all();

        $this->assertEquals([], $actualBenefitPlans);
    }

    public function testFilledChecksIfCacheIsFilled(): void
    {
        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('has')
            ->once()
            ->with('prefix:benefit_plans')
            ->andReturnTrue();

        $this->assertTrue($this->repository->filled());
    }

    public function testFilledChecksIfCacheIsNotFilled(): void
    {
        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('has')
            ->once()
            ->with('prefix:benefit_plans')
            ->andReturnFalse();

        $this->assertFalse($this->repository->filled());
    }

    public function testFillStoresBenefitPlansInCache(): void
    {
        $expectedBenefitPlan = BenefitPlanFixture::create();

        $benefitPlanData = [
            'i' => $expectedBenefitPlan->getId(),
            'n' => $expectedBenefitPlan->getBenefitId(),
            'p' => $expectedBenefitPlan->getPlanId(),
            'q' => $expectedBenefitPlan->getCharges(),
            's' => $expectedBenefitPlan->isUnlimited(),
        ];

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit_plan.ttl')
            ->andReturn(60);

        $this->cacheRepository
            ->shouldReceive('put')
            ->once()
            ->with('prefix:benefit_plans', [$benefitPlanData], 60);

        $this->repository->fill([$expectedBenefitPlan]);

        $this->assertTrue(true);
    }
}
