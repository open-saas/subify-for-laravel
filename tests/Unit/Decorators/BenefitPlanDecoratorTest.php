<?php

namespace Decorators;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery\LegacyMockInterface;
use OpenSaaS\Subify\Contracts\Cache\BenefitPlanRepository as CacheBenefitPlanRepository;
use OpenSaaS\Subify\Contracts\Context\BenefitPlanRepository as ContextBenefitPlanRepository;
use OpenSaaS\Subify\Contracts\Database\BenefitPlanRepository as DatabaseBenefitPlanRepository;
use OpenSaaS\Subify\Decorators\BenefitPlanDecorator;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitPlanFixture;

/**
 * @internal
 */
class BenefitPlanDecoratorTest extends TestCase
{
    private ConfigRepository $configRepository;

    private LegacyMockInterface|ContextBenefitPlanRepository $contextBenefitPlanRepository;

    private LegacyMockInterface|CacheBenefitPlanRepository $cacheBenefitPlanRepository;

    private LegacyMockInterface|DatabaseBenefitPlanRepository $databaseBenefitPlanRepository;

    private BenefitPlanDecorator $benefitPlanDecorator;

    public function setUp(): void
    {
        parent::setUp();

        $this->configRepository = \Mockery::mock(ConfigRepository::class);
        $this->contextBenefitPlanRepository = \Mockery::mock(ContextBenefitPlanRepository::class);
        $this->cacheBenefitPlanRepository = \Mockery::mock(CacheBenefitPlanRepository::class);
        $this->databaseBenefitPlanRepository = \Mockery::mock(DatabaseBenefitPlanRepository::class);

        $this->benefitPlanDecorator = new BenefitPlanDecorator(
            $this->configRepository,
            $this->databaseBenefitPlanRepository,
            $this->cacheBenefitPlanRepository,
            $this->contextBenefitPlanRepository,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        \Mockery::close();
    }

    public function testFindUsesArrayRepository(): void
    {
        $benefitPlan = BenefitPlanFixture::create();

        $this->contextBenefitPlanRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnTrue();

        $this->contextBenefitPlanRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefitPlan]);

        $this->cacheBenefitPlanRepository
            ->shouldNotReceive('all');

        $this->databaseBenefitPlanRepository
            ->shouldNotReceive('all');

        $this->assertEquals(
            $benefitPlan,
            $this->benefitPlanDecorator->find($benefitPlan->getBenefitId(), $benefitPlan->getPlanId())
        );
    }

    public function testFindUsesCacheRepositoryWhenEnabled(): void
    {
        $benefitPlan = BenefitPlanFixture::create();

        $this->contextBenefitPlanRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit_plan.enabled')
            ->andReturnTrue();

        $this->cacheBenefitPlanRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnTrue();

        $this->cacheBenefitPlanRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefitPlan]);

        $this->contextBenefitPlanRepository
            ->shouldReceive('fill')
            ->once()
            ->with([$benefitPlan]);

        $this->contextBenefitPlanRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefitPlan]);

        $this->databaseBenefitPlanRepository
            ->shouldNotReceive('all');

        $this->assertEquals(
            $benefitPlan,
            $this->benefitPlanDecorator->find($benefitPlan->getBenefitId(), $benefitPlan->getPlanId())
        );
    }

    public function testFindUsesDatabaseAndSavesToCacheWhenItIsEnabled(): void
    {
        $benefitPlan = BenefitPlanFixture::create();

        $this->contextBenefitPlanRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit_plan.enabled')
            ->andReturnTrue();

        $this->cacheBenefitPlanRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnFalse();

        $this->databaseBenefitPlanRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefitPlan]);

        $this->contextBenefitPlanRepository
            ->shouldReceive('fill')
            ->once()
            ->with([$benefitPlan]);

        $this->cacheBenefitPlanRepository
            ->shouldReceive('fill')
            ->once()
            ->with([$benefitPlan]);

        $this->contextBenefitPlanRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefitPlan]);

        $this->assertEquals(
            $benefitPlan,
            $this->benefitPlanDecorator->find($benefitPlan->getBenefitId(), $benefitPlan->getPlanId())
        );
    }

    public function testFindUsesDirectlyDatabaseWhenCacheIsNotEnabled(): void
    {
        $benefitPlan = BenefitPlanFixture::create();

        $this->contextBenefitPlanRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnFalse();

        $this->configRepository
            ->shouldReceive('get')
            ->once()
            ->with('subify.repositories.cache.benefit_plan.enabled')
            ->andReturnFalse();

        $this->cacheBenefitPlanRepository
            ->shouldNotReceive('filled');

        $this->databaseBenefitPlanRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefitPlan]);

        $this->contextBenefitPlanRepository
            ->shouldReceive('fill')
            ->once()
            ->with([$benefitPlan]);

        $this->cacheBenefitPlanRepository
            ->shouldNotReceive('fill');

        $this->contextBenefitPlanRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefitPlan]);

        $this->assertEquals(
            $benefitPlan,
            $this->benefitPlanDecorator->find($benefitPlan->getBenefitId(), $benefitPlan->getPlanId())
        );
    }

    public function testFindReturnsNullWhenThereAreNoBenefitPlans(): void
    {
        $this->contextBenefitPlanRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnTrue();

        $this->contextBenefitPlanRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([]);

        $this->assertNull(
            $this->benefitPlanDecorator->find(1, 1)
        );
    }

    public function testExistsReturnsTrueWhenThereIsABenefitPlan(): void
    {
        $benefitPlan = BenefitPlanFixture::create();

        $this->contextBenefitPlanRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnTrue();

        $this->contextBenefitPlanRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([$benefitPlan]);

        $this->assertTrue($this->benefitPlanDecorator->exists($benefitPlan->getBenefitId(), $benefitPlan->getPlanId()));
    }

    public function testExistsReturnsFalseWhenThereIsNoBenefitPlan(): void
    {
        $this->contextBenefitPlanRepository
            ->shouldReceive('filled')
            ->once()
            ->andReturnTrue();

        $this->contextBenefitPlanRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([]);

        $this->assertFalse($this->benefitPlanDecorator->exists(1, 1));
    }

    public function testFlushContextCallsContextRepository(): void
    {
        $this->contextBenefitPlanRepository
            ->shouldReceive('flush')
            ->once();

        $this->benefitPlanDecorator->flushContext();

        $this->assertTrue(true);
    }
}
