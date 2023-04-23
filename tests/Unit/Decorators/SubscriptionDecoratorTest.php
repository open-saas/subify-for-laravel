<?php

namespace Tests\Unit\Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery\LegacyMockInterface;
use OpenSaaS\Subify\Contracts\Cache\SubscriptionRepository as CacheSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Context\SubscriptionRepository as ContextSubscriptionRepository;
use OpenSaaS\Subify\Contracts\Database\SubscriptionRepository as DatabaseSubscriptionRepository;
use OpenSaaS\Subify\Decorators\SubscriptionDecorator;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\SubscriptionFixture;

/**
 * @internal
 */
class SubscriptionDecoratorTest extends TestCase
{
    private ConfigRepository $configRepository;

    private LegacyMockInterface|ContextSubscriptionRepository $contextSubscriptionRepository;

    private LegacyMockInterface|CacheSubscriptionRepository $cacheSubscriptionRepository;

    private LegacyMockInterface|DatabaseSubscriptionRepository $databaseSubscriptionRepository;

    private SubscriptionDecorator $subscriptionDecorator;

    public function setUp(): void
    {
        parent::setUp();

        $this->configRepository = \Mockery::mock(ConfigRepository::class);
        $this->contextSubscriptionRepository = \Mockery::mock(ContextSubscriptionRepository::class);
        $this->cacheSubscriptionRepository = \Mockery::mock(CacheSubscriptionRepository::class);
        $this->databaseSubscriptionRepository = \Mockery::mock(DatabaseSubscriptionRepository::class);

        $this->subscriptionDecorator = new SubscriptionDecorator(
            $this->configRepository,
            $this->databaseSubscriptionRepository,
            $this->cacheSubscriptionRepository,
            $this->contextSubscriptionRepository,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        \Mockery::close();
    }

    public function testFindReturnsDirectlyFromArrayWhenItHasTheSubscription(): void
    {
        $subscription = SubscriptionFixture::create();

        $this->contextSubscriptionRepository
            ->shouldReceive('has')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnTrue();

        $this->contextSubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('has');

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('find');

        $this->databaseSubscriptionRepository
            ->shouldNotReceive('findActive');

        $this->assertEquals(
            $subscription,
            $this->subscriptionDecorator->find($subscription->getSubscriberIdentifier())
        );
    }

    public function testFindGetsFromCacheWhenItIsEnabled(): void
    {
        $subscription = SubscriptionFixture::create();

        $this->contextSubscriptionRepository
            ->shouldReceive('has')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.subscription.enabled')
            ->once()
            ->andReturnTrue();

        $this->cacheSubscriptionRepository
            ->shouldReceive('has')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnTrue();

        $this->cacheSubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->contextSubscriptionRepository
            ->shouldReceive('save')
            ->with($subscription)
            ->once();

        $this->contextSubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->databaseSubscriptionRepository
            ->shouldNotReceive('findActive');

        $this->assertEquals(
            $subscription,
            $this->subscriptionDecorator->find($subscription->getSubscriberIdentifier())
        );
    }

    public function testFindGetsFromDatabaseAndSavesInCacheWhenItIsEnabled(): void
    {
        $subscription = SubscriptionFixture::create();

        $this->contextSubscriptionRepository
            ->shouldReceive('has')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.subscription.enabled')
            ->once()
            ->andReturnTrue();

        $this->cacheSubscriptionRepository
            ->shouldReceive('has')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnFalse();

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('find');

        $this->databaseSubscriptionRepository
            ->shouldReceive('findActive')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->contextSubscriptionRepository
            ->shouldReceive('save')
            ->with($subscription)
            ->once();

        $this->cacheSubscriptionRepository
            ->shouldReceive('save')
            ->with($subscription)
            ->once();

        $this->contextSubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->assertEquals(
            $subscription,
            $this->subscriptionDecorator->find($subscription->getSubscriberIdentifier())
        );
    }

    public function testFindGetsFromDatabaseWhenCacheIsDisabled(): void
    {
        $subscription = SubscriptionFixture::create();

        $this->contextSubscriptionRepository
            ->shouldReceive('has')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.subscription.enabled')
            ->once()
            ->andReturnFalse();

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('has');

        $this->cacheSubscriptionRepository
            ->shouldNotReceive('find');

        $this->databaseSubscriptionRepository
            ->shouldReceive('findActive')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->contextSubscriptionRepository
            ->shouldReceive('save')
            ->with($subscription)
            ->once();

        $this->contextSubscriptionRepository
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->once()
            ->andReturn($subscription);

        $this->assertEquals(
            $subscription,
            $this->subscriptionDecorator->find($subscription->getSubscriberIdentifier())
        );
    }

    public function testFlushContextCallsContextRepository(): void
    {
        $this->contextSubscriptionRepository
            ->shouldReceive('flush')
            ->once();

        $this->subscriptionDecorator->flushContext();

        $this->assertTrue(true);
    }
}
