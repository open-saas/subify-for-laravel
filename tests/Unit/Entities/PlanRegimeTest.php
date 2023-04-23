<?php

namespace Tests\Unit\Entities;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\PlanRegimeFixture;

/**
 * @internal
 */
class PlanRegimeTest extends TestCase
{
    public function testItCalculatesNextExpirationFromACertainDate(): void
    {
        $periodicity = \DateInterval::createFromDateString('1 month');

        $regime = PlanRegimeFixture::create([
            'periodicity' => $periodicity,
        ]);

        $this->assertEquals(
            \DateTimeImmutable::createFromInterface(new \DateTime())->add($periodicity)->getTimestamp(),
            $regime->calculateNextExpiration(new \DateTime())->getTimestamp(),
        );
    }

    public function testItCalculatesNextExpirationAsNullIfThereIsNoValueSet(): void
    {
        $regime = PlanRegimeFixture::create([
            'periodicity' => null,
        ]);

        $this->assertNull($regime->calculateNextExpiration(new \DateTime()));
    }

    public function testItCalculatesNextGraceEndFromACertainDate(): void
    {
        $grace = \DateInterval::createFromDateString('1 month');

        $regime = PlanRegimeFixture::create([
            'grace' => $grace,
        ]);

        $this->assertEquals(
            \DateTimeImmutable::createFromInterface(new \DateTime())->add($grace)->getTimestamp(),
            $regime->calculateNextGraceEnd(new \DateTime())->getTimestamp(),
        );
    }

    public function testItCalculatesNextGraceEndAsNullIfThereIsNoValueSet(): void
    {
        $regime = PlanRegimeFixture::create([
            'grace' => null,
        ]);

        $this->assertNull($regime->calculateNextGraceEnd(new \DateTime()));
    }

    public function testItCalculatesNextTrialEndFromACertainDate(): void
    {
        $trial = \DateInterval::createFromDateString('1 month');

        $regime = PlanRegimeFixture::create([
            'trial' => $trial,
        ]);

        $this->assertEquals(
            \DateTimeImmutable::createFromInterface(new \DateTime())->add($trial)->getTimestamp(),
            $regime->calculateNextTrialEnd(new \DateTime())->getTimestamp(),
        );
    }

    public function testItCalculatesNextTrialEndAsNullIfThereIsNoValueSet(): void
    {
        $regime = PlanRegimeFixture::create([
            'trial' => null,
        ]);

        $this->assertNull($regime->calculateNextTrialEnd(new \DateTime()));
    }
}
