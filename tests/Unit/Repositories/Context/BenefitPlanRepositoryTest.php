<?php

namespace Repositories\Context;

use OpenSaaS\Subify\Repositories\Context\BenefitPlanRepository;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitPlanFixture;

/**
 * @internal
 */
class BenefitPlanRepositoryTest extends TestCase
{
    private BenefitPlanRepository $repository;

    private \ReflectionProperty $benefitPlansProperty;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new BenefitPlanRepository();
        $this->benefitPlansProperty = new \ReflectionProperty($this->repository, 'benefitPlans');
    }

    public function testAllReturnsBenefitPlan(): void
    {
        $expectedBenefitPlan = BenefitPlanFixture::create();

        $this->benefitPlansProperty->setValue($this->repository, [$expectedBenefitPlan]);

        $actualBenefitPlans = $this->repository->all();

        $this->assertEquals([$expectedBenefitPlan], $actualBenefitPlans);
    }

    public function testAllReturnsEmpty(): void
    {
        $this->benefitPlansProperty->setValue($this->repository, []);

        $actualBenefitPlans = $this->repository->all();

        $this->assertEmpty($actualBenefitPlans);
    }

    public function testAllReturnsEmptyWhenNotInitialized(): void
    {
        $actualBenefitPlans = $this->repository->all();

        $this->assertEmpty($actualBenefitPlans);
    }

    public function testFillAddsBenefitPlanToArray(): void
    {
        $expectedBenefitPlan = BenefitPlanFixture::create();

        $this->benefitPlansProperty->setValue($this->repository, []);

        $this->repository->fill([$expectedBenefitPlan]);

        $actualBenefitPlans = $this->benefitPlansProperty->getValue($this->repository);

        $this->assertEquals([$expectedBenefitPlan], $actualBenefitPlans);
    }

    public function testFlushContextClearsArray(): void
    {
        $this->benefitPlansProperty->setValue($this->repository, [BenefitPlanFixture::create()]);

        $this->repository->flush();

        $this->assertFalse($this->benefitPlansProperty->isInitialized($this->repository));
    }

    public function testIsFilledReturnsTrueWhenFilled(): void
    {
        $this->benefitPlansProperty->setValue($this->repository, [BenefitPlanFixture::create()]);

        $this->assertTrue($this->repository->filled());
    }

    public function testIsFilledReturnsFalseWhenNotFilled(): void
    {
        $this->assertFalse($this->repository->filled());
    }
}
