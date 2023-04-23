<?php

namespace Entities;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitUsageFixture;

/**
 * @internal
 */
class BenefitUsageTest extends TestCase
{
    #[DataProvider('isExpiredProvider')]
    public function testIsExpired(bool $expected, ?\DateTimeImmutable $expiredAt): void
    {
        $benefitUsage = BenefitUsageFixture::create(['expiredAt' => $expiredAt]);

        $this->assertEquals($expected, $benefitUsage->isExpired());
    }

    #[DataProvider('isExpiredProvider')]
    public function testIsNotExpired(bool $expected, ?\DateTimeImmutable $expiredAt): void
    {
        $benefitUsage = BenefitUsageFixture::create(['expiredAt' => $expiredAt]);

        $this->assertNotEquals($expected, $benefitUsage->isNotExpired());
    }

    public static function isExpiredProvider(): array
    {
        return [
            'expired in past' => [true, (new \DateTimeImmutable())->sub(new \DateInterval('P1M'))],
            'expired in future' => [false, (new \DateTimeImmutable())->add(new \DateInterval('P1M'))],
            'expired null' => [false, null],
        ];
    }

    public function testIncrease(): void
    {
        $benefitUsage = BenefitUsageFixture::create(['amount' => 10]);

        $benefitUsage->increase(5);

        $this->assertEquals(15, $benefitUsage->getAmount());
    }

    public function testClearUsage(): void
    {
        $benefitUsage = BenefitUsageFixture::create(['amount' => 10]);

        $benefitUsage->clearUsage();

        $this->assertEquals(0, $benefitUsage->getAmount());
    }
}
