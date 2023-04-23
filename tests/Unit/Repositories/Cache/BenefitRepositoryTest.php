<?php

namespace Repositories\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery\LegacyMockInterface;
use OpenSaaS\Subify\Repositories\Cache\BenefitRepository;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitFixture;

/**
 * @internal
 */
class BenefitRepositoryTest extends TestCase
{
    private LegacyMockInterface|CacheRepository $cacheRepository;

    private LegacyMockInterface|ConfigRepository $configRepository;

    private BenefitRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $cacheFactory = \Mockery::mock(CacheFactory::class);
        $this->cacheRepository = \Mockery::mock(CacheRepository::class);
        $this->configRepository = \Mockery::mock(ConfigRepository::class);

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.benefit.store')
            ->andReturn('cache-store');

        $cacheFactory
            ->shouldReceive('store')
            ->with('cache-store')
            ->andReturn($this->cacheRepository);

        $this->repository = new BenefitRepository(
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
            ->with('subify.repositories.cache.benefit.store')
            ->andReturn('cache-store');

        $cacheFactory
            ->shouldReceive('store')
            ->once()
            ->with('cache-store')
            ->andReturn($cacheRepository);

        new BenefitRepository($cacheFactory, $configRepository);

        $this->assertTrue(true);
    }

    public function testAllReturnsBenefit(): void
    {
        $expectedBenefit = BenefitFixture::create([
            'periodicity' => new \DateInterval('P0Y1M0DT0H0M0S'),
        ]);

        $benefitData = [
            'i' => $expectedBenefit->getId(),
            'n' => $expectedBenefit->getName(),
            's' => $expectedBenefit->isConsumable(),
            'q' => $expectedBenefit->isQuota(),
            'p' => 'P0Y1M0DT0H0M0S',
        ];

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:benefits')
            ->andReturn([$benefitData]);

        $actualBenefits = $this->repository->all();

        $this->assertEquals([$expectedBenefit], $actualBenefits);
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
            ->with('prefix:benefits')
            ->andReturn([]);

        $actualBenefits = $this->repository->all();

        $this->assertEquals([], $actualBenefits);
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
            ->with('prefix:benefits')
            ->andReturnNull();

        $actualBenefits = $this->repository->all();

        $this->assertEquals([], $actualBenefits);
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
            ->with('prefix:benefits')
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
            ->with('prefix:benefits')
            ->andReturnFalse();

        $this->assertFalse($this->repository->filled());
    }

    public function testFillStoresBenefitsInCache(): void
    {
        $expectedBenefit = BenefitFixture::create([
            'periodicity' => \DateInterval::createFromDateString('1 month'),
        ]);

        $benefitData = [
            'i' => $expectedBenefit->getId(),
            'n' => $expectedBenefit->getName(),
            's' => $expectedBenefit->isConsumable(),
            'q' => $expectedBenefit->isQuota(),
            'p' => 'P0Y1M0DT0H0M0S',
        ];

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit.ttl')
            ->andReturn(60);

        $this->cacheRepository
            ->shouldReceive('put')
            ->once()
            ->with('prefix:benefits', [$benefitData], 60);

        $this->repository->fill([$expectedBenefit]);

        $this->assertTrue(true);
    }

    public function testFillStoresBenefitsInCacheWithNullPeriodicity(): void
    {
        $expectedBenefit = BenefitFixture::create([
            'periodicity' => null,
        ]);

        $benefitData = [
            'i' => $expectedBenefit->getId(),
            'n' => $expectedBenefit->getName(),
            's' => $expectedBenefit->isConsumable(),
            'q' => $expectedBenefit->isQuota(),
            'p' => null,
        ];

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit.ttl')
            ->andReturn(60);

        $this->cacheRepository
            ->shouldReceive('put')
            ->once()
            ->with('prefix:benefits', [$benefitData], 60);

        $this->repository->fill([$expectedBenefit]);

        $this->assertTrue(true);
    }
}
