<?php

namespace Tests\Unit\Entities;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\SubscriptionFixture;

/**
 * @internal
 */
class SubscriptionTest extends TestCase
{
    public function testItReturnsExpiredTrueWhenExpiredAtIsPast(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->sub(new \DateInterval('P1D')),
        ]);

        $this->assertTrue($subscription->isExpired());
    }

    public function testItReturnsExpiredFalseWhenExpiredAtIsFuture(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->add(new \DateInterval('P1D')),
        ]);

        $this->assertFalse($subscription->isExpired());
    }

    public function testItReturnsExpiredFalseWhenExpiredAtIsNull(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $this->assertFalse($subscription->isExpired());
    }

    public function testItReturnsNotExpiredTrueWhenExpiredAtIsFuture(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->add(new \DateInterval('P1D')),
        ]);

        $this->assertTrue($subscription->isNotExpired());
    }

    public function testItReturnsNotExpiredFalseWhenExpiredAtIsPast(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->sub(new \DateInterval('P1D')),
        ]);

        $this->assertFalse($subscription->isNotExpired());
    }

    public function testItReturnsNotExpiredTrueWhenExpiredAtIsNull(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $this->assertTrue($subscription->isNotExpired());
    }

    public function testItReturnsTrialTrueWhenTrialEndedAtIsFuture(): void
    {
        $subscription = SubscriptionFixture::create([
            'trialEndedAt' => (new \DateTimeImmutable())->add(new \DateInterval('P1D')),
        ]);

        $this->assertTrue($subscription->isTrial());
    }

    public function testItReturnsTrialFalseWhenTrialEndedAtIsPast(): void
    {
        $subscription = SubscriptionFixture::create([
            'trialEndedAt' => (new \DateTimeImmutable())->sub(new \DateInterval('P1D')),
        ]);

        $this->assertFalse($subscription->isTrial());
    }

    public function testItReturnsTrialFalseWhenTrialEndedAtIsNull(): void
    {
        $subscription = SubscriptionFixture::create([
            'trialEndedAt' => null,
        ]);

        $this->assertFalse($subscription->isTrial());
    }

    public function testItReturnsGraceTrueWhenGraceEndedAtIsFuture(): void
    {
        $subscription = SubscriptionFixture::create([
            'graceEndedAt' => (new \DateTimeImmutable())->add(new \DateInterval('P1D')),
        ]);

        $this->assertTrue($subscription->isGrace());
    }

    public function testItReturnsGraceFalseWhenGraceEndedAtIsPast(): void
    {
        $subscription = SubscriptionFixture::create([
            'graceEndedAt' => (new \DateTimeImmutable())->sub(new \DateInterval('P1D')),
        ]);

        $this->assertFalse($subscription->isGrace());
    }

    public function testItReturnsGraceFalseWhenGraceEndedAtIsNull(): void
    {
        $subscription = SubscriptionFixture::create([
            'graceEndedAt' => null,
        ]);

        $this->assertFalse($subscription->isGrace());
    }

    public function testItReturnsActiveTrueWhenNotTrialOrGraceButNotExpired(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->add(new \DateInterval('P1D')),
            'graceEndedAt' => null,
            'trialEndedAt' => null,
        ]);

        $this->assertTrue($subscription->isActive());
    }

    public function testItReturnsActiveTrueWhenExpiredButGrace(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->sub(new \DateInterval('P1D')),
            'graceEndedAt' => (new \DateTimeImmutable())->add(new \DateInterval('P1D')),
            'trialEndedAt' => null,
        ]);

        $this->assertTrue($subscription->isActive());
    }

    public function testItReturnsActiveTrueWhenExpiredButTrial(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->sub(new \DateInterval('P1D')),
            'graceEndedAt' => null,
            'trialEndedAt' => (new \DateTimeImmutable())->add(new \DateInterval('P1D')),
        ]);

        $this->assertTrue($subscription->isActive());
    }

    public function testItReturnsActiveFalseWhenExpiredAndNotGraceOrTrial(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->sub(new \DateInterval('P1D')),
            'graceEndedAt' => null,
            'trialEndedAt' => null,
        ]);

        $this->assertFalse($subscription->isActive());
    }

    public function testItReturnsNotActiveTrueWhenExpiredAndNotGraceOrTrial(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->sub(new \DateInterval('P1D')),
            'graceEndedAt' => null,
            'trialEndedAt' => null,
        ]);

        $this->assertTrue($subscription->isNotActive());
    }

    public function testItReturnsNotActiveFalseWhenNotTrialOrGraceButNotExpired(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->add(new \DateInterval('P1D')),
            'graceEndedAt' => null,
            'trialEndedAt' => null,
        ]);

        $this->assertFalse($subscription->isNotActive());
    }

    public function testItReturnsNotActiveFalseWhenExpiredButGrace(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->sub(new \DateInterval('P1D')),
            'graceEndedAt' => (new \DateTimeImmutable())->add(new \DateInterval('P1D')),
            'trialEndedAt' => null,
        ]);

        $this->assertFalse($subscription->isNotActive());
    }

    public function testItReturnsNotActiveFalseWhenExpiredButTrial(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => (new \DateTimeImmutable())->sub(new \DateInterval('P1D')),
            'graceEndedAt' => null,
            'trialEndedAt' => (new \DateTimeImmutable())->add(new \DateInterval('P1D')),
        ]);

        $this->assertFalse($subscription->isNotActive());
    }
}
