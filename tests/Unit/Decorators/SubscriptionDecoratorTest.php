<?php

namespace Tests\Unit\Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery;
use Mockery\MockInterface;
use OpenSaaS\Subify\Contracts\Array\SubscriptionRepository as ArraySubscriptionRepository;
use OpenSaaS\Subify\Contracts\Cache\SubscriptionRepository as CacheSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Database\SubscriptionRepository as DatabaseSubscriptionRepository;
use OpenSaaS\Subify\Decorators\SubscriptionDecorator;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\SubscriptionFixture;

class SubscriptionDecoratorTest extends TestCase
{
    private ConfigRepository $configRepository;

    private MockInterface|ArraySubscriptionRepository $arraySubscriptionRepository;

    private MockInterface|CacheSubscriptionRepository $cacheSubscriptionRepository;

    private MockInterface|DatabaseSubscriptionRepository $databaseSubscriptionRepository;

    private SubscriptionDecorator $subscriptionDecorator;

    public function setUp(): void
    {
        parent::setUp();

        $this->configRepository = Mockery::mock(ConfigRepository::class);
        $this->arraySubscriptionRepository = Mockery::mock(ArraySubscriptionRepository::class);
        $this->cacheSubscriptionRepository = Mockery::mock(CacheSubscriptionRepository::class);
        $this->databaseSubscriptionRepository = Mockery::mock(DatabaseSubscriptionRepository::class);

        $this->subscriptionDecorator = new SubscriptionDecorator(
            $this->configRepository,
            $this->databaseSubscriptionRepository,
            $this->cacheSubscriptionRepository,
            $this->arraySubscriptionRepository,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    public function testFindUsesArrayRepository(): void
    {
        $subscription = SubscriptionFixture::create();

        $this->arraySubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('find');

        $this->databaseSubscriptionRepository
            ->shouldNotReceive('findActive');

        $this->assertEquals(
            $subscription,
            $this->subscriptionDecorator->find($subscription->getSubscriberIdentifier())
        );
    }

    public function testFindUsesCacheRepositoryWhenEnabled(): void
    {
        $subscription = SubscriptionFixture::create();

        $this->arraySubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnNull();

        $this->cacheSubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->arraySubscriptionRepository
            ->shouldReceive('save')
            ->with($subscription)
            ->once();

        $this->databaseSubscriptionRepository
            ->shouldNotReceive('findActive');

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.subscription.enabled')
            ->once()
            ->andReturnTrue();

        $this->assertEquals(
            $subscription,
            $this->subscriptionDecorator->find($subscription->getSubscriberIdentifier())
        );
    }

    public function testFindUsesDatabaseRepositoryWhenCacheIsDisabled(): void
    {
        $subscription = SubscriptionFixture::create();

        $this->arraySubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnNull();

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('find');

        $this->databaseSubscriptionRepository
            ->shouldReceive('findActive')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->arraySubscriptionRepository
            ->shouldReceive('save')
            ->with($subscription)
            ->once();

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('save');

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.subscription.enabled')
            ->once()
            ->andReturnFalse();

        $this->assertEquals(
            $subscription,
            $this->subscriptionDecorator->find($subscription->getSubscriberIdentifier())
        );
    }

    public function testFindSavesResultToCacheWhenItIsEnabled(): void
    {
        $subscription = SubscriptionFixture::create();

        $this->arraySubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnNull();

        $this->cacheSubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnNull();

        $this->databaseSubscriptionRepository
            ->shouldReceive('findActive')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->arraySubscriptionRepository
            ->shouldReceive('save')
            ->with($subscription)
            ->once();

        $this->cacheSubscriptionRepository
            ->shouldReceive('save')
            ->with($subscription)
            ->once();

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.subscription.enabled')
            ->once()
            ->andReturnTrue();

        $this->assertEquals(
            $subscription,
            $this->subscriptionDecorator->find($subscription->getSubscriberIdentifier())
        );
    }

    public function testFindReturnsNullWhenCacheIsEnabledButThereIsNoResult(): void
    {
        $subscription = SubscriptionFixture::create();

        $this->arraySubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnNull();

        $this->cacheSubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnNull();

        $this->databaseSubscriptionRepository
            ->shouldReceive('findActive')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnNull();

        $this->arraySubscriptionRepository
            ->shouldNotReceive('save');

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('save');

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.subscription.enabled')
            ->once()
            ->andReturnTrue();

        $this->assertNull(
            $this->subscriptionDecorator->find($subscription->getSubscriberIdentifier())
        );
    }

    public function testFindReturnsNullWhenCacheIsDisabledAndThereIsNoResult(): void
    {
        $subscription = SubscriptionFixture::create();

        $this->arraySubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnNull();

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('find');

        $this->databaseSubscriptionRepository
            ->shouldReceive('findActive')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnNull();

        $this->arraySubscriptionRepository
            ->shouldNotReceive('save');

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('save');

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.subscription.enabled')
            ->once()
            ->andReturnFalse();

        $this->assertNull(
            $this->subscriptionDecorator->find($subscription->getSubscriberIdentifier())
        );
    }

    public function testFlushCallsArrayRepository(): void
    {
        $this->arraySubscriptionRepository
            ->shouldReceive('flush')
            ->once();

        $this->subscriptionDecorator->flush();

        $this->assertTrue(true);
    }
}
