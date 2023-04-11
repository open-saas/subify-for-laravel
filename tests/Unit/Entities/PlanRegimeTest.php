<?php

namespace Tests\Unit\Entities;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\PlanRegimeFixture;

/**
 * @internal
 */
class PlanRegimeTest extends TestCase
{
    public function testItCalculatesNextExpiration(): void
    {
        $periodicity = \DateInterval::createFromDateString('1 month');

        $regime = PlanRegimeFixture::create([
            'periodicity' => $periodicity,
        ]);

        $this->assertEquals(
            (new \DateTimeImmutable())->add($periodicity)->getTimestamp(),
            $regime->calculateNextExpiration()->getTimestamp(),
        );
    }

    public function testItCalculatesNextExpirationFromACertainDate(): void
    {
        $periodicity = \DateInterval::createFromDateString('1 month');

        $regime = PlanRegimeFixture::create([
            'periodicity' => $periodicity,
        ]);

        $this->assertEquals(
            (new \DateTimeImmutable('2021-01-01'))->add($periodicity)->getTimestamp(),
            $regime->calculateNextExpiration('2021-01-01')->getTimestamp(),
        );
    }

    public function testItCalculatesNextExpirationAsNullIfThereIsNoValueSet(): void
    {
        $regime = PlanRegimeFixture::create([
            'periodicity' => null,
        ]);

        $this->assertNull($regime->calculateNextExpiration());
    }

    public function testItCalculatesNextGraceEnd(): void
    {
        $grace = \DateInterval::createFromDateString('1 month');

        $regime = PlanRegimeFixture::create([
            'grace' => $grace,
        ]);

        $this->assertEquals(
            (new \DateTimeImmutable())->add($grace)->getTimestamp(),
            $regime->calculateNextGraceEnd()->getTimestamp(),
        );
    }

    public function testItCalculatesNextGraceEndFromACertainDate(): void
    {
        $grace = \DateInterval::createFromDateString('1 month');

        $regime = PlanRegimeFixture::create([
            'grace' => $grace,
        ]);

        $this->assertEquals(
            (new \DateTimeImmutable('2021-01-01'))->add($grace)->getTimestamp(),
            $regime->calculateNextGraceEnd('2021-01-01')->getTimestamp(),
        );
    }

    public function testItCalculatesNextGraceEndAsNullIfThereIsNoValueSet(): void
    {
        $regime = PlanRegimeFixture::create([
            'grace' => null,
        ]);

        $this->assertNull($regime->calculateNextGraceEnd());
    }

    public function testItCalculatesNextTrialEnd(): void
    {
        $trial = \DateInterval::createFromDateString('1 month');

        $regime = PlanRegimeFixture::create([
            'trial' => $trial,
        ]);

        $this->assertEquals(
            (new \DateTimeImmutable())->add($trial)->getTimestamp(),
            $regime->calculateNextTrialEnd()->getTimestamp(),
        );
    }

    public function testItCalculatesNextTrialEndFromACertainDate(): void
    {
        $trial = \DateInterval::createFromDateString('1 month');

        $regime = PlanRegimeFixture::create([
            'trial' => $trial,
        ]);

        $this->assertEquals(
            (new \DateTimeImmutable('2021-01-01'))->add($trial)->getTimestamp(),
            $regime->calculateNextTrialEnd('2021-01-01')->getTimestamp(),
        );
    }

    public function testItCalculatesNextTrialEndAsNullIfThereIsNoValueSet(): void
    {
        $regime = PlanRegimeFixture::create([
            'trial' => null,
        ]);

        $this->assertNull($regime->calculateNextTrialEnd());
    }
}
