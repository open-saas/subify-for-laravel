<?php

namespace Repositories\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery\LegacyMockInterface;
use OpenSaaS\Subify\Repositories\Cache\BenefitUsageRepository;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitUsageFixture;

/**
 * @internal
 */
class BenefitUsageRepositoryTest extends TestCase
{
    private LegacyMockInterface|CacheRepository $cacheRepository;

    private LegacyMockInterface|ConfigRepository $configRepository;

    private BenefitUsageRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $cacheFactory = \Mockery::mock(CacheFactory::class);
        $this->cacheRepository = \Mockery::mock(CacheRepository::class);
        $this->configRepository = \Mockery::mock(ConfigRepository::class);

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.benefit_usage.store')
            ->andReturn('cache-store');

        $cacheFactory
            ->shouldReceive('store')
            ->with('cache-store')
            ->andReturn($this->cacheRepository);

        $this->repository = new BenefitUsageRepository(
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
            ->with('subify.repositories.cache.benefit_usage.store')
            ->andReturn('cache-store');

        $cacheFactory
            ->shouldReceive('store')
            ->once()
            ->with('cache-store')
            ->andReturn($cacheRepository);

        new BenefitUsageRepository($cacheFactory, $configRepository);

        $this->assertTrue(true);
    }

    public function testGetReturnsUsageFromCache(): void
    {
        $expectedBenefitUsage = BenefitUsageFixture::create();

        $benefitUsageData = [
            'i' => $expectedBenefitUsage->getId(),
            'n' => $expectedBenefitUsage->getBenefitId(),
            's' => $expectedBenefitUsage->getSubscriberIdentifier(),
            'a' => $expectedBenefitUsage->getAmount(),
            'e' => $expectedBenefitUsage->getExpiredAt(),
        ];

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:benefit_usages:1')
            ->andReturn([$benefitUsageData]);

        $actualBenefitUsages = $this->repository->get('1');

        $this->assertEquals([$expectedBenefitUsage], $actualBenefitUsages);
    }

    public function testGetReturnsEmptyArrayWhenCacheIsEmpty(): void
    {
        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:benefit_usages:1')
            ->andReturn([]);

        $actualBenefitUsages = $this->repository->get('1');

        $this->assertEquals([], $actualBenefitUsages);
    }

    public function testGetReturnsEmptyArrayWhenCacheIsMissing(): void
    {
        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:benefit_usages:1')
            ->andReturn(null);

        $actualBenefitUsages = $this->repository->get('1');

        $this->assertEquals([], $actualBenefitUsages);
    }

    public function testFillSavesBenefitUsagesInCache(): void
    {
        $expectedBenefitUsage = BenefitUsageFixture::create();

        $benefitUsageData = [
            'i' => $expectedBenefitUsage->getId(),
            'n' => $expectedBenefitUsage->getBenefitId(),
            's' => $expectedBenefitUsage->getSubscriberIdentifier(),
            'a' => $expectedBenefitUsage->getAmount(),
            'e' => $expectedBenefitUsage->getExpiredAt(),
        ];

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit_usage.ttl')
            ->andReturn(60);

        $this->cacheRepository
            ->shouldReceive('put')
            ->once()
            ->with('prefix:benefit_usages:1', [$benefitUsageData], 60)
            ->andReturn(true);

        $this->repository->fill('1', [$expectedBenefitUsage]);

        $this->assertTrue(true);
    }

    public function testFillSavesEmptyArrayInCache(): void
    {
        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit_usage.ttl')
            ->andReturn(60);

        $this->cacheRepository
            ->shouldReceive('put')
            ->once()
            ->with('prefix:benefit_usages:1', [], 60)
            ->andReturn(true);

        $this->repository->fill('1', []);

        $this->assertTrue(true);
    }

    public function testHasReturnsTrueWhenCacheIsNotEmpty(): void
    {
        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('has')
            ->once()
            ->with('prefix:benefit_usages:1')
            ->andReturn(true);

        $actualHas = $this->repository->has('1');

        $this->assertTrue($actualHas);
    }

    public function testHasReturnsFalseWhenCacheIsEmpty(): void
    {
        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('has')
            ->once()
            ->with('prefix:benefit_usages:1')
            ->andReturn(false);

        $actualHas = $this->repository->has('1');

        $this->assertFalse($actualHas);
    }

    public function testSaveAddsToExistingCache(): void
    {
        $newBenefitUsage = BenefitUsageFixture::create([
            'id' => 1,
            'subscriberIdentifier' => '1',
        ]);

        $alreadyExistingBenefitUsage = BenefitUsageFixture::create(['id' => 2]);

        $alreadyExistingBenefitUsageData = [
            'i' => $alreadyExistingBenefitUsage->getId(),
            'n' => $alreadyExistingBenefitUsage->getBenefitId(),
            's' => $alreadyExistingBenefitUsage->getSubscriberIdentifier(),
            'a' => $alreadyExistingBenefitUsage->getAmount(),
            'e' => $alreadyExistingBenefitUsage->getExpiredAt(),
        ];

        $newBenefitUsageData = [
            'i' => $newBenefitUsage->getId(),
            'n' => $newBenefitUsage->getBenefitId(),
            's' => $newBenefitUsage->getSubscriberIdentifier(),
            'a' => $newBenefitUsage->getAmount(),
            'e' => $newBenefitUsage->getExpiredAt(),
        ];

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:benefit_usages:1')
            ->andReturn([$alreadyExistingBenefitUsageData]);

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit_usage.ttl')
            ->andReturn(60);

        $this->cacheRepository
            ->shouldReceive('put')
            ->once()
            ->with('prefix:benefit_usages:1', [$alreadyExistingBenefitUsageData, $newBenefitUsageData], 60)
            ->andReturn(true);

        $this->repository->save($newBenefitUsage);

        $this->assertTrue(true);
    }

    public function testSaveFillsEmptyCache(): void
    {
        $newBenefitUsage = BenefitUsageFixture::create([
            'id' => 1,
            'subscriberIdentifier' => '1',
        ]);

        $newBenefitUsageData = [
            'i' => $newBenefitUsage->getId(),
            'n' => $newBenefitUsage->getBenefitId(),
            's' => $newBenefitUsage->getSubscriberIdentifier(),
            'a' => $newBenefitUsage->getAmount(),
            'e' => $newBenefitUsage->getExpiredAt(),
        ];

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:benefit_usages:1')
            ->andReturnNull();

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit_usage.ttl')
            ->andReturn(60);

        $this->cacheRepository
            ->shouldReceive('put')
            ->once()
            ->with('prefix:benefit_usages:1', [$newBenefitUsageData], 60)
            ->andReturn(true);

        $this->repository->save($newBenefitUsage);

        $this->assertTrue(true);
    }

    public function testSaveReplacesCurrentUsage(): void
    {
        $newBenefitUsage = BenefitUsageFixture::create([
            'id' => 1,
            'subscriberIdentifier' => '1',
        ]);

        $alreadyExistingBenefitUsage = BenefitUsageFixture::create([
            'id' => 1,
        ]);

        $newBenefitUsageData = [
            'i' => $newBenefitUsage->getId(),
            'n' => $newBenefitUsage->getBenefitId(),
            's' => $newBenefitUsage->getSubscriberIdentifier(),
            'a' => $newBenefitUsage->getAmount(),
            'e' => $newBenefitUsage->getExpiredAt(),
        ];

        $alreadyExistingBenefitUsageData = [
            'i' => $alreadyExistingBenefitUsage->getId(),
            'n' => $alreadyExistingBenefitUsage->getBenefitId(),
            's' => $alreadyExistingBenefitUsage->getSubscriberIdentifier(),
            'a' => $alreadyExistingBenefitUsage->getAmount(),
            'e' => $alreadyExistingBenefitUsage->getExpiredAt(),
        ];

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:');

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:benefit_usages:1')
            ->andReturn([$alreadyExistingBenefitUsageData]);

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit_usage.ttl')
            ->andReturn(60);

        $this->cacheRepository
            ->shouldReceive('put')
            ->once()
            ->with('prefix:benefit_usages:1', [$newBenefitUsageData], 60)
            ->andReturn(true);

        $this->repository->save($newBenefitUsage);

        $this->assertTrue(true);
    }
}
