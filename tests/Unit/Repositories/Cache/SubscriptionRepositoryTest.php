<?php

namespace Tests\Unit\Repositories\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery\MockInterface;
use OpenSaaS\Subify\Repositories\Cache\SubscriptionRepository;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\SubscriptionFixture;

/**
 * @internal
 *
 * @coversNothing
 */
class SubscriptionRepositoryTest extends TestCase
{
    private MockInterface|CacheRepository $cacheRepository;

    private MockInterface|ConfigRepository $configRepository;

    private SubscriptionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $cacheFactory = \Mockery::mock(CacheFactory::class);
        $this->cacheRepository = \Mockery::mock(CacheRepository::class);
        $this->configRepository = \Mockery::mock(ConfigRepository::class);

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.store')
            ->andReturn('cache-store')
        ;

        $cacheFactory
            ->shouldReceive('store')
            ->with('cache-store')
            ->andReturn($this->cacheRepository)
        ;

        $this->repository = new SubscriptionRepository(
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
            ->with('subify.repositories.cache.store')
            ->andReturn('cache-store')
        ;

        $cacheFactory
            ->shouldReceive('store')
            ->once()
            ->with('cache-store')
            ->andReturn($cacheRepository)
        ;

        new SubscriptionRepository($cacheFactory, $configRepository);

        $this->assertTrue(true);
    }

    public function testFindReturnsSubscription(): void
    {
        $expectedSubscription = SubscriptionFixture::create();

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:')
        ;

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:subscriber-identifier')
            ->andReturn([
                'i' => $expectedSubscription->getId(),
                's' => $expectedSubscription->getSubscriberIdentifier(),
                'p' => $expectedSubscription->getPlanId(),
                'r' => $expectedSubscription->getPlanRegimeId(),
                'g' => $expectedSubscription->getGraceEndedAt(),
                't' => $expectedSubscription->getTrialEndedAt(),
                'w' => $expectedSubscription->getRenewedAt(),
                'e' => $expectedSubscription->getExpiredAt(),
                'c' => $expectedSubscription->getCreatedAt(),
                'u' => $expectedSubscription->getUpdatedAt(),
            ])
        ;

        $actualSubscription = $this->repository->find('subscriber-identifier');

        $this->assertEquals($expectedSubscription, $actualSubscription);
    }

    public function testFindReturnsNullWhenSubscriptionNotFound(): void
    {
        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:')
        ;

        $this->cacheRepository
            ->shouldReceive('get')
            ->once()
            ->with('prefix:subscriber-identifier')
            ->andReturnNull()
        ;

        $actualSubscription = $this->repository->find('subscriber-identifier');

        $this->assertNull($actualSubscription);
    }

    public function testSaveCallsPutOnCacheRepository(): void
    {
        $subscription = SubscriptionFixture::create();
        $expectedSubscriberIdentifier = 'prefix:'.$subscription->getSubscriberIdentifier();

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:')
        ;

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.ttl')
            ->andReturn(60)
        ;

        $this->cacheRepository
            ->shouldReceive('put')
            ->once()
            ->with($expectedSubscriberIdentifier, [
                'i' => $subscription->getId(),
                's' => $subscription->getSubscriberIdentifier(),
                'p' => $subscription->getPlanId(),
                'r' => $subscription->getPlanRegimeId(),
                'g' => $subscription->getGraceEndedAt(),
                't' => $subscription->getTrialEndedAt(),
                'w' => $subscription->getRenewedAt(),
                'e' => $subscription->getExpiredAt(),
                'c' => $subscription->getCreatedAt(),
                'u' => $subscription->getUpdatedAt(),
            ], 60)
        ;

        $this->repository->save($subscription);

        $this->assertTrue(true);
    }

    public function testDeleteCallsForgetOnCacheRepository(): void
    {
        $subscription = SubscriptionFixture::create();
        $expectedSubscriberIdentifier = 'prefix:'.$subscription->getSubscriberIdentifier();

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.prefix')
            ->andReturn('prefix:')
        ;

        $this->cacheRepository
            ->shouldReceive('delete')
            ->once()
            ->with($expectedSubscriberIdentifier)
        ;

        $this->repository->delete($subscription->getSubscriberIdentifier());

        $this->assertTrue(true);
    }
}
