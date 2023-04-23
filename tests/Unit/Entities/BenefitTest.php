<?php

namespace Entities;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitFixture;

/**
 * @internal
 */
class BenefitTest extends TestCase
{
    #[DataProvider('calculateUsageExpirationDate')]
    public function testCalculateUsageExpirationDate(\DateInterval $periodicity, \DateTimeImmutable $from, \DateTimeImmutable $expected): void
    {
        $benefit = BenefitFixture::create(['periodicity' => $periodicity]);

        $received = $benefit->calculateUsageExpirationDate($from);

        $this->assertEquals(
            $expected->format('Y-m-d H:i'),
            $received->format('Y-m-d H:i'),
        );
    }

    public static function calculateUsageExpirationDate(): array
    {
        $startExpiredAt = new \DateTimeImmutable();

        return [
            'from past' => [
                'periodicity' => new \DateInterval('P1W'),
                'from' => $startExpiredAt->modify('-1 week'),
                'expected' => $startExpiredAt->modify('+1 week'),
            ],
            'from near past' => [
                'periodicity' => new \DateInterval('P1W'),
                'from' => $startExpiredAt->modify('-3 days'),
                'expected' => $startExpiredAt->modify('+1 week -3 days'),
            ],
            'from long past' => [
                'periodicity' => new \DateInterval('P1W'),
                'from' => $startExpiredAt->modify('-1 week -1 day'),
                'expected' => $startExpiredAt->modify('+1 week -1 day'),
            ],
            'from future' => [
                'periodicity' => new \DateInterval('P1W'),
                'from' => $startExpiredAt->modify('+1 week'),
                'expected' => $startExpiredAt->modify('+1 week'),
            ],
        ];
    }

    public function testCalculateUsageExpirationDateReturnsNullForQuota(): void
    {
        $benefit = BenefitFixture::create([
            'isQuota' => true,
        ]);

        $received = $benefit->calculateUsageExpirationDate(new \DateTimeImmutable());

        $this->assertNull($received);
    }

    public function testCalculateUsageExpirationDateReturnsNullForNotConsumable(): void
    {
        $benefit = BenefitFixture::create([
            'isConsumable' => false,
        ]);

        $received = $benefit->calculateUsageExpirationDate(new \DateTimeImmutable());

        $this->assertNull($received);
    }
}
