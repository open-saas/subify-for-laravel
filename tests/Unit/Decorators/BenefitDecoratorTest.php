<?php

namespace Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery\LegacyMockInterface;
use OpenSaaS\Subify\Contracts\Cache\BenefitRepository as CacheBenefitRepository;
use OpenSaaS\Subify\Contracts\Context\BenefitRepository as ContextBenefitRepository;
use OpenSaaS\Subify\Contracts\Database\BenefitRepository as DatabaseBenefitRepository;
use OpenSaaS\Subify\Decorators\BenefitDecorator;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitFixture;

/**
 * @internal
 */
class BenefitDecoratorTest extends TestCase
{
    private ConfigRepository $configRepository;

    private LegacyMockInterface|ContextBenefitRepository $contextBenefitRepository;

    private LegacyMockInterface|CacheBenefitRepository $cacheBenefitRepository;

    private LegacyMockInterface|DatabaseBenefitRepository $databaseBenefitRepository;

    private BenefitDecorator $benefitDecorator;

    public function setUp(): void
    {
        parent::setUp();

        $this->configRepository = \Mockery::mock(ConfigRepository::class);
        $this->contextBenefitRepository = \Mockery::mock(ContextBenefitRepository::class);
        $this->cacheBenefitRepository = \Mockery::mock(CacheBenefitRepository::class);
        $this->databaseBenefitRepository = \Mockery::mock(DatabaseBenefitRepository::class);

        $this->benefitDecorator = new BenefitDecorator(
            $this->configRepository,
            $this->databaseBenefitRepository,
            $this->cacheBenefitRepository,
            $this->contextBenefitRepository,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        \Mockery::close();
    }

    public function testFindUsesArrayRepository(): void
    {
        $benefit = BenefitFixture::create();

        $this->contextBenefitRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnTrue();

        $this->contextBenefitRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefit]);

        $this->cacheBenefitRepository
            ->shouldNotReceive('all');

        $this->databaseBenefitRepository
            ->shouldNotReceive('all');

        $this->assertEquals(
            $benefit,
            $this->benefitDecorator->find($benefit->getName())
        );
    }

    public function testFindUsesCacheRepositoryWhenEnabled(): void
    {
        $benefit = BenefitFixture::create();

        $this->contextBenefitRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit.enabled')
            ->andReturnTrue();

        $this->cacheBenefitRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnTrue();

        $this->cacheBenefitRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefit]);

        $this->contextBenefitRepository
            ->shouldReceive('fill')
            ->once()
            ->with([$benefit]);

        $this->contextBenefitRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefit]);

        $this->databaseBenefitRepository
            ->shouldNotReceive('all');

        $this->assertEquals(
            $benefit,
            $this->benefitDecorator->find($benefit->getName())
        );
    }

    public function testFindUsesDatabaseAndSavesToCacheWhenItIsEnabled(): void
    {
        $benefit = BenefitFixture::create();

        $this->contextBenefitRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit.enabled')
            ->andReturnTrue();

        $this->cacheBenefitRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnFalse();

        $this->databaseBenefitRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefit]);

        $this->contextBenefitRepository
            ->shouldReceive('fill')
            ->once()
            ->with([$benefit]);

        $this->cacheBenefitRepository
            ->shouldReceive('fill')
            ->once()
            ->with([$benefit]);

        $this->contextBenefitRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefit]);

        $this->assertEquals(
            $benefit,
            $this->benefitDecorator->find($benefit->getName())
        );
    }

    public function testFindUsesDirectlyDatabaseWhenCacheIsNotEnabled(): void
    {
        $benefit = BenefitFixture::create();

        $this->contextBenefitRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit.enabled')
            ->andReturnFalse();

        $this->cacheBenefitRepository
            ->shouldNotReceive('filled');

        $this->databaseBenefitRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefit]);

        $this->contextBenefitRepository
            ->shouldReceive('fill')
            ->once()
            ->with([$benefit]);

        $this->cacheBenefitRepository
            ->shouldNotReceive('fill');

        $this->contextBenefitRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefit]);

        $this->assertEquals(
            $benefit,
            $this->benefitDecorator->find($benefit->getName())
        );
    }

    public function testFindReturnsNullWhenThereAreNoBenefits(): void
    {
        $this->contextBenefitRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnTrue();

        $this->contextBenefitRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([]);

        $this->assertNull(
            $this->benefitDecorator->find('test-benefit')
        );
    }

    public function testFlushContextCallsContextRepository(): void
    {
        $this->contextBenefitRepository
            ->shouldReceive('flush')
            ->once();

        $this->benefitDecorator->flushContext();

        $this->assertTrue(true);
    }
}
