<?php

namespace Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery\LegacyMockInterface;
use OpenSaaS\Subify\Contracts\Cache\BenefitUsageRepository as CacheBenefitUsageRepository;
use OpenSaaS\Subify\Contracts\Context\BenefitUsageRepository as ContextBenefitUsageRepository;
use OpenSaaS\Subify\Contracts\Database\BenefitUsageRepository as DatabaseBenefitUsageRepository;
use OpenSaaS\Subify\Decorators\BenefitUsageDecorator;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitUsageFixture;

/**
 * @internal
 */
class BenefitUsageDecoratorTest extends TestCase
{
    private ConfigRepository $configRepository;

    private LegacyMockInterface|ContextBenefitUsageRepository $contextBenefitUsageRepository;

    private LegacyMockInterface|CacheBenefitUsageRepository $cacheBenefitUsageRepository;

    private LegacyMockInterface|DatabaseBenefitUsageRepository $databaseBenefitUsageRepository;

    private BenefitUsageDecorator $benefitUsageDecorator;

    public function setUp(): void
    {
        parent::setUp();

        $this->configRepository = \Mockery::mock(ConfigRepository::class);
        $this->contextBenefitUsageRepository = \Mockery::mock(ContextBenefitUsageRepository::class);
        $this->cacheBenefitUsageRepository = \Mockery::mock(CacheBenefitUsageRepository::class);
        $this->databaseBenefitUsageRepository = \Mockery::mock(DatabaseBenefitUsageRepository::class);

        $this->benefitUsageDecorator = new BenefitUsageDecorator(
            $this->configRepository,
            $this->databaseBenefitUsageRepository,
            $this->cacheBenefitUsageRepository,
            $this->contextBenefitUsageRepository,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        \Mockery::close();
    }

    public function testFindReturnsDirectlyFromArrayWhenItHasTheBenefitUsage(): void
    {
        $benefitUsage = BenefitUsageFixture::create();

        $this->contextBenefitUsageRepository
            ->shouldReceive('has')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturnTrue();

        $this->contextBenefitUsageRepository
            ->shouldReceive('find')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturn($benefitUsage);

        $this->cacheBenefitUsageRepository
            ->shouldNotReceive('has');

        $this->cacheBenefitUsageRepository
            ->shouldNotReceive('get');

        $this->databaseBenefitUsageRepository
            ->shouldNotReceive('get');

        $this->assertEquals(
            $benefitUsage,
            $this->benefitUsageDecorator->find($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
        );
    }

    public function testFindGetsFromCacheWhenItIsEnabled(): void
    {
        $benefitUsage = BenefitUsageFixture::create();

        $this->contextBenefitUsageRepository
            ->shouldReceive('has')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.benefit_usage.enabled')
            ->once()
            ->andReturnTrue();

        $this->cacheBenefitUsageRepository
            ->shouldReceive('has')
            ->with($benefitUsage->getSubscriberIdentifier())
            ->once()
            ->andReturnTrue();

        $this->cacheBenefitUsageRepository
            ->shouldReceive('get')
            ->with($benefitUsage->getSubscriberIdentifier())
            ->once()
            ->andReturn([$benefitUsage]);

        $this->contextBenefitUsageRepository
            ->shouldReceive('fill')
            ->with($benefitUsage->getSubscriberIdentifier(), [$benefitUsage])
            ->once();

        $this->contextBenefitUsageRepository
            ->shouldReceive('find')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturn($benefitUsage);

        $this->databaseBenefitUsageRepository
            ->shouldNotReceive('get');

        $this->assertEquals(
            $benefitUsage,
            $this->benefitUsageDecorator->find($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
        );
    }

    public function testFindGetsFromDatabaseAndSavesInCacheWhenItIsEnabled(): void
    {
        $benefitUsage = BenefitUsageFixture::create();

        $this->contextBenefitUsageRepository
            ->shouldReceive('has')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.benefit_usage.enabled')
            ->once()
            ->andReturnTrue();

        $this->cacheBenefitUsageRepository
            ->shouldReceive('has')
            ->with($benefitUsage->getSubscriberIdentifier())
            ->once()
            ->andReturnFalse();

        $this->cacheBenefitUsageRepository
            ->shouldNotReceive('find');

        $this->databaseBenefitUsageRepository
            ->shouldReceive('get')
            ->with($benefitUsage->getSubscriberIdentifier())
            ->once()
            ->andReturn([$benefitUsage]);

        $this->contextBenefitUsageRepository
            ->shouldReceive('fill')
            ->with($benefitUsage->getSubscriberIdentifier(), [$benefitUsage])
            ->once();

        $this->cacheBenefitUsageRepository
            ->shouldReceive('fill')
            ->with($benefitUsage->getSubscriberIdentifier(), [$benefitUsage])
            ->once();

        $this->contextBenefitUsageRepository
            ->shouldReceive('find')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturn($benefitUsage);

        $this->assertEquals(
            $benefitUsage,
            $this->benefitUsageDecorator->find($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
        );
    }

    public function testFindGetsFromDatabaseWhenCacheIsDisabled(): void
    {
        $benefitUsage = BenefitUsageFixture::create();

        $this->contextBenefitUsageRepository
            ->shouldReceive('has')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.benefit_usage.enabled')
            ->once()
            ->andReturnFalse();

        $this->cacheBenefitUsageRepository
            ->shouldNotReceive('has');

        $this->cacheBenefitUsageRepository
            ->shouldNotReceive('find');

        $this->databaseBenefitUsageRepository
            ->shouldReceive('get')
            ->with($benefitUsage->getSubscriberIdentifier())
            ->once()
            ->andReturn([$benefitUsage]);

        $this->contextBenefitUsageRepository
            ->shouldReceive('fill')
            ->with($benefitUsage->getSubscriberIdentifier(), [$benefitUsage])
            ->once();

        $this->contextBenefitUsageRepository
            ->shouldReceive('find')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturn($benefitUsage);

        $this->assertEquals(
            $benefitUsage,
            $this->benefitUsageDecorator->find($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
        );
    }

    public function testGetConsumedReturnsAmount(): void
    {
        $benefitUsage = BenefitUsageFixture::create();

        $this->contextBenefitUsageRepository
            ->shouldReceive('has')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturnTrue();

        $this->contextBenefitUsageRepository
            ->shouldReceive('find')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturn($benefitUsage);

        $this->assertEquals(
            $benefitUsage->getAmount(),
            $this->benefitUsageDecorator->getConsumed($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
        );
    }

    public function testGetConsumedReturnsZeroWhenBenefitUsageIsNotFound(): void
    {
        $benefitUsage = BenefitUsageFixture::create();

        $this->contextBenefitUsageRepository
            ->shouldReceive('has')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturnTrue();

        $this->contextBenefitUsageRepository
            ->shouldReceive('find')
            ->with($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
            ->once()
            ->andReturnNull();

        $this->assertEquals(
            0,
            $this->benefitUsageDecorator->getConsumed($benefitUsage->getSubscriberIdentifier(), $benefitUsage->getBenefitId())
        );
    }

    public function testFlushContextCallsContextRepository(): void
    {
        $this->contextBenefitUsageRepository
            ->shouldReceive('flush')
            ->once();

        $this->benefitUsageDecorator->flushContext();

        $this->assertTrue(true);
    }

    public function testCreateInsertsInDatabase(): void
    {
        $benefitUsage = BenefitUsageFixture::create();

        $this->databaseBenefitUsageRepository
            ->shouldReceive('insert')
            ->with(
                $benefitUsage->getSubscriberIdentifier(),
                $benefitUsage->getBenefitId(),
                $benefitUsage->getAmount(),
                $benefitUsage->getExpiredAt(),
            )
            ->once()
            ->andReturn($benefitUsage);

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.benefit_usage.enabled')
            ->once()
            ->andReturnFalse();

        $this->cacheBenefitUsageRepository
            ->shouldNotReceive('save');

        $this->contextBenefitUsageRepository
            ->shouldReceive('save')
            ->with($benefitUsage);

        $this->benefitUsageDecorator->create(
            $benefitUsage->getSubscriberIdentifier(),
            $benefitUsage->getBenefitId(),
            $benefitUsage->getAmount(),
            $benefitUsage->getExpiredAt(),
        );

        $this->assertTrue(true);
    }

    public function testCreateInsertsInDatabaseAndSavesToCache(): void
    {
        $benefitUsage = BenefitUsageFixture::create();

        $this->databaseBenefitUsageRepository
            ->shouldReceive('insert')
            ->with(
                $benefitUsage->getSubscriberIdentifier(),
                $benefitUsage->getBenefitId(),
                $benefitUsage->getAmount(),
                $benefitUsage->getExpiredAt(),
            )
            ->once()
            ->andReturn($benefitUsage);

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.benefit_usage.enabled')
            ->once()
            ->andReturnTrue();

        $this->cacheBenefitUsageRepository
            ->shouldReceive('save')
            ->with($benefitUsage);

        $this->contextBenefitUsageRepository
            ->shouldReceive('save')
            ->with($benefitUsage);

        $this->benefitUsageDecorator->create(
            $benefitUsage->getSubscriberIdentifier(),
            $benefitUsage->getBenefitId(),
            $benefitUsage->getAmount(),
            $benefitUsage->getExpiredAt(),
        );

        $this->assertTrue(true);
    }

    public function testSaveUpdatesInDatabase(): void
    {
        $benefitUsage = BenefitUsageFixture::create();

        $this->databaseBenefitUsageRepository
            ->shouldReceive('save')
            ->with($benefitUsage)
            ->once();

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.benefit_usage.enabled')
            ->once()
            ->andReturnFalse();

        $this->cacheBenefitUsageRepository
            ->shouldNotReceive('save');

        $this->contextBenefitUsageRepository
            ->shouldReceive('save')
            ->with($benefitUsage);

        $this->benefitUsageDecorator->save($benefitUsage);

        $this->assertTrue(true);
    }

    public function testSaveUpdatesInDatabaseAndCache(): void
    {
        $benefitUsage = BenefitUsageFixture::create();

        $this->databaseBenefitUsageRepository
            ->shouldReceive('save')
            ->with($benefitUsage)
            ->once();

        $this->configRepository
            ->shouldReceive('get')
            ->with('subify.repositories.cache.benefit_usage.enabled')
            ->once()
            ->andReturnTrue();

        $this->cacheBenefitUsageRepository
            ->shouldReceive('save')
            ->with($benefitUsage);

        $this->contextBenefitUsageRepository
            ->shouldReceive('save')
            ->with($benefitUsage);

        $this->benefitUsageDecorator->save($benefitUsage);

        $this->assertTrue(true);
    }
}
