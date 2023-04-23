<?php

namespace Repositories\Context;

use OpenSaaS\Subify\Repositories\Context\BenefitUsageRepository;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitUsageFixture;

/**
 * @internal
 */
class BenefitUsageRepositoryTest extends TestCase
{
    private BenefitUsageRepository $repository;

    private \ReflectionProperty $benefitUsagesProperty;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new BenefitUsageRepository();
        $this->benefitUsagesProperty = new \ReflectionProperty($this->repository, 'benefitUsages');
    }

    public function testFindReturnsBenefitUsage(): void
    {
        $expectedBenefitUsage = BenefitUsageFixture::create();

        $this->benefitUsagesProperty->setValue($this->repository, [
            $expectedBenefitUsage->getSubscriberIdentifier() => [
                $expectedBenefitUsage->getBenefitId() => $expectedBenefitUsage,
            ],
        ]);

        $actualBenefitUsages = $this->repository->find(
            $expectedBenefitUsage->getSubscriberIdentifier(),
            $expectedBenefitUsage->getBenefitId(),
        );

        $this->assertEquals($expectedBenefitUsage, $actualBenefitUsages);
    }

    public function testFindReturnsNull(): void
    {
        $this->benefitUsagesProperty->setValue($this->repository, []);

        $actualBenefitUsages = $this->repository->find(
            'subscriber-identifier',
            1,
        );

        $this->assertNull($actualBenefitUsages);
    }

    public function testFindReturnsNullWhenNotInitialized(): void
    {
        $actualBenefitUsages = $this->repository->find(
            'subscriber-identifier',
            1,
        );

        $this->assertNull($actualBenefitUsages);
    }

    public function testHasReturnsTrue(): void
    {
        $expectedBenefitUsage = BenefitUsageFixture::create();

        $this->benefitUsagesProperty->setValue($this->repository, [
            $expectedBenefitUsage->getSubscriberIdentifier() => [
                $expectedBenefitUsage->getBenefitId() => $expectedBenefitUsage,
            ],
        ]);

        $actualBenefitUsages = $this->repository->has(
            $expectedBenefitUsage->getSubscriberIdentifier(),
            $expectedBenefitUsage->getBenefitId(),
        );

        $this->assertTrue($actualBenefitUsages);
    }

    public function testHasReturnsFalse(): void
    {
        $this->benefitUsagesProperty->setValue($this->repository, []);

        $actualBenefitUsages = $this->repository->has(
            'subscriber-identifier',
            1,
        );

        $this->assertFalse($actualBenefitUsages);
    }

    public function testHasReturnsFalseWhenNotInitialized(): void
    {
        $actualBenefitUsages = $this->repository->has(
            'subscriber-identifier',
            1,
        );

        $this->assertFalse($actualBenefitUsages);
    }

    public function testFill(): void
    {
        $expectedBenefitUsages = [
            BenefitUsageFixture::create(),
            BenefitUsageFixture::create(),
        ];

        $this->repository->fill(
            $expectedBenefitUsages[0]->getSubscriberIdentifier(),
            $expectedBenefitUsages,
        );

        $this->assertEquals(
            [
                $expectedBenefitUsages[0]->getSubscriberIdentifier() => [
                    $expectedBenefitUsages[0]->getBenefitId() => $expectedBenefitUsages[0],
                    $expectedBenefitUsages[1]->getBenefitId() => $expectedBenefitUsages[1],
                ],
            ],
            $this->benefitUsagesProperty->getValue($this->repository),
        );
    }

    public function testSave(): void
    {
        $expectedBenefitUsage = BenefitUsageFixture::create();

        $this->repository->save($expectedBenefitUsage);

        $this->assertEquals(
            [
                $expectedBenefitUsage->getSubscriberIdentifier() => [
                    $expectedBenefitUsage->getBenefitId() => $expectedBenefitUsage,
                ],
            ],
            $this->benefitUsagesProperty->getValue($this->repository),
        );
    }

    public function testSaveWhenNotInitialized(): void
    {
        $expectedBenefitUsage = BenefitUsageFixture::create();

        $this->repository->save($expectedBenefitUsage);

        $this->assertEquals(
            [
                $expectedBenefitUsage->getSubscriberIdentifier() => [
                    $expectedBenefitUsage->getBenefitId() => $expectedBenefitUsage,
                ],
            ],
            $this->benefitUsagesProperty->getValue($this->repository),
        );
    }

    public function testSaveWhenBenefitUsageExists(): void
    {
        $expectedBenefitUsage = BenefitUsageFixture::create();

        $this->benefitUsagesProperty->setValue($this->repository, [
            $expectedBenefitUsage->getSubscriberIdentifier() => [
                $expectedBenefitUsage->getBenefitId() => $expectedBenefitUsage,
            ],
        ]);

        $this->repository->save($expectedBenefitUsage);

        $this->assertEquals(
            [
                $expectedBenefitUsage->getSubscriberIdentifier() => [
                    $expectedBenefitUsage->getBenefitId() => $expectedBenefitUsage,
                ],
            ],
            $this->benefitUsagesProperty->getValue($this->repository),
        );
    }

    public function testFlushContext(): void
    {
        $this->benefitUsagesProperty->setValue($this->repository, [
            'subscriber-identifier' => [
                1 => BenefitUsageFixture::create(),
            ],
        ]);

        $this->repository->flush();

        $this->assertFalse($this->benefitUsagesProperty->isInitialized($this->repository));
    }
}
