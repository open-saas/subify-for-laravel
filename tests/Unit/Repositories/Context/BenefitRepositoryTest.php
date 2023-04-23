<?php

namespace Repositories\Context;

use OpenSaaS\Subify\Repositories\Context\BenefitRepository;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitFixture;

/**
 * @internal
 */
class BenefitRepositoryTest extends TestCase
{
    private BenefitRepository $repository;

    private \ReflectionProperty $benefitsProperty;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new BenefitRepository();
        $this->benefitsProperty = new \ReflectionProperty($this->repository, 'benefits');
    }

    public function testAllReturnsBenefit(): void
    {
        $expectedBenefit = BenefitFixture::create();

        $this->benefitsProperty->setValue($this->repository, [$expectedBenefit]);

        $actualBenefits = $this->repository->all();

        $this->assertEquals([$expectedBenefit], $actualBenefits);
    }

    public function testAllReturnsEmpty(): void
    {
        $this->benefitsProperty->setValue($this->repository, []);

        $actualBenefits = $this->repository->all();

        $this->assertEmpty($actualBenefits);
    }

    public function testAllReturnsEmptyWhenNotInitialized(): void
    {
        $actualBenefits = $this->repository->all();

        $this->assertEmpty($actualBenefits);
    }

    public function testFillAddsBenefitToArray(): void
    {
        $expectedBenefit = BenefitFixture::create();

        $this->benefitsProperty->setValue($this->repository, []);

        $this->repository->fill([$expectedBenefit]);

        $actualBenefits = $this->benefitsProperty->getValue($this->repository);

        $this->assertEquals([$expectedBenefit], $actualBenefits);
    }

    public function testFlushContextClearsArray(): void
    {
        $this->benefitsProperty->setValue($this->repository, [BenefitFixture::create()]);

        $this->repository->flush();

        $this->assertFalse($this->benefitsProperty->isInitialized($this->repository));
    }

    public function testIsFilledReturnsTrueWhenFilled(): void
    {
        $this->benefitsProperty->setValue($this->repository, [BenefitFixture::create()]);

        $this->assertTrue($this->repository->filled());
    }

    public function testIsFilledReturnsFalseWhenNotFilled(): void
    {
        $this->assertFalse($this->repository->filled());
    }
}
