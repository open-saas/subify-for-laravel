<?php

namespace Tests\Unit;

use Mockery\LegacyMockInterface;
use OpenSaaS\Subify\Contracts\Decorators\BenefitDecorator;
use OpenSaaS\Subify\Contracts\Decorators\BenefitPlanDecorator;
use OpenSaaS\Subify\Contracts\Decorators\BenefitUsageDecorator;
use OpenSaaS\Subify\Contracts\Decorators\SubscriptionDecorator;
use OpenSaaS\Subify\Entities\BenefitUsage;
use OpenSaaS\Subify\Exceptions\CantConsumeException;
use OpenSaaS\Subify\SubscriptionManager;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BenefitFixture;
use Tests\Fixtures\BenefitPlanFixture;
use Tests\Fixtures\BenefitUsageFixture;
use Tests\Fixtures\SubscriptionFixture;

/**
 * @internal
 */
class SubscriptionManagerTest extends TestCase
{
    private LegacyMockInterface|BenefitDecorator $benefitDecorator;

    private LegacyMockInterface|BenefitPlanDecorator $benefitPlanDecorator;

    private LegacyMockInterface|BenefitUsageDecorator $benefitUsageDecorator;

    private LegacyMockInterface|SubscriptionDecorator $subscriptionDecorator;

    private SubscriptionManager $subscriptionManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->benefitDecorator = \Mockery::mock(BenefitDecorator::class);
        $this->benefitPlanDecorator = \Mockery::mock(BenefitPlanDecorator::class);
        $this->benefitUsageDecorator = \Mockery::mock(BenefitUsageDecorator::class);
        $this->subscriptionDecorator = \Mockery::mock(SubscriptionDecorator::class);

        $this->subscriptionManager = new SubscriptionManager(
            $this->benefitDecorator,
            $this->benefitPlanDecorator,
            $this->benefitUsageDecorator,
            $this->subscriptionDecorator,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        \Mockery::close();
    }

    public function testHasBenefit(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $benefit = BenefitFixture::create();

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('exists')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturnTrue();

        $this->assertTrue(
            $this->subscriptionManager->hasBenefit($subscription->getSubscriberIdentifier(), $benefit->getName())
        );
    }

    public function testHasBenefitReturnsFalseWhenThereIsNoSubscription(): void
    {
        $benefit = BenefitFixture::create();

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with('subscriber-identifier')
            ->andReturnNull();

        $this->benefitDecorator
            ->shouldNotReceive('find');

        $this->benefitPlanDecorator
            ->shouldNotReceive('exists');

        $this->assertFalse(
            $this->subscriptionManager->hasBenefit('subscriber-identifier', $benefit->getName())
        );
    }

    public function testHasBenefitReturnsFalseForInactiveSubscriptions(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => now()->subDay()->toDateTimeImmutable(),
        ]);

        $benefit = BenefitFixture::create();

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldNotReceive('find');

        $this->benefitPlanDecorator
            ->shouldNotReceive('exists');

        $this->assertFalse(
            $this->subscriptionManager->hasBenefit($subscription->getSubscriberIdentifier(), $benefit->getName())
        );
    }

    public function testHasBenefitReturnsFalseWhenThereIsNoBenefit(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with('benefit-name')
            ->andReturnNull();

        $this->benefitPlanDecorator
            ->shouldNotReceive('exists');

        $this->assertFalse(
            $this->subscriptionManager->hasBenefit($subscription->getSubscriberIdentifier(), 'benefit-name')
        );
    }

    public function testHasBenefitReturnsFalseWhenBenefitPlanDoesNotExist(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $benefit = BenefitFixture::create();

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('exists')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturnFalse();

        $this->assertFalse(
            $this->subscriptionManager->hasBenefit($subscription->getSubscriberIdentifier(), $benefit->getName())
        );
    }

    public function testCanConsume(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $benefit = BenefitFixture::create();
        $benefitPlan = BenefitPlanFixture::create();

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('find')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturn($benefitPlan);

        $this->benefitUsageDecorator
            ->shouldReceive('getConsumed')
            ->with($subscription->getSubscriberIdentifier(), $benefit->getId())
            ->andReturn(0.0);

        $this->assertTrue(
            $this->subscriptionManager->canConsume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0)
        );
    }

    public function testCanConsumeReturnsFalseWhenThereIsNoSubscription(): void
    {
        $benefit = BenefitFixture::create();

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with('subscriber-identifier')
            ->andReturnNull();

        $this->benefitDecorator
            ->shouldNotReceive('find');

        $this->benefitPlanDecorator
            ->shouldNotReceive('find');

        $this->benefitUsageDecorator
            ->shouldNotReceive('getConsumed');

        $this->assertFalse(
            $this->subscriptionManager->canConsume('subscriber-identifier', $benefit->getName(), 1.0)
        );
    }

    public function testCanConsumeReturnsFalseForInactiveSubscriptions(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => now()->subDay()->toDateTimeImmutable(),
        ]);

        $benefit = BenefitFixture::create();

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldNotReceive('find');

        $this->benefitPlanDecorator
            ->shouldNotReceive('find');

        $this->benefitUsageDecorator
            ->shouldNotReceive('getConsumed');

        $this->assertFalse(
            $this->subscriptionManager->canConsume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0)
        );
    }

    public function testCanConsumeReturnsFalseWhenThereIsNoBenefit(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with('benefit-name')
            ->andReturnNull();

        $this->benefitPlanDecorator
            ->shouldNotReceive('find');

        $this->benefitUsageDecorator
            ->shouldNotReceive('getConsumed');

        $this->assertFalse(
            $this->subscriptionManager->canConsume($subscription->getSubscriberIdentifier(), 'benefit-name', 1.0)
        );
    }

    public function testCanConsumeReturnsFalseWhenBenefitPlanDoesNotExist(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $benefit = BenefitFixture::create();

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('find')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturnNull();

        $this->benefitUsageDecorator
            ->shouldNotReceive('getConsumed');

        $this->assertFalse(
            $this->subscriptionManager->canConsume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0)
        );
    }

    public function testCanConsumeReturnsFalseWhenBenefitPlanChargesAreNotEnough(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $benefit = BenefitFixture::create();
        $benefitPlan = BenefitPlanFixture::create([
            'charges' => 0.5,
        ]);

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('find')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturn($benefitPlan);

        $this->benefitUsageDecorator
            ->shouldNotReceive('getConsumed');

        $this->assertFalse(
            $this->subscriptionManager->canConsume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0)
        );
    }

    public function testCanConsumeReturnsFalseWhenConsumedPlusWantedAmountIsGreaterThanTheCharges(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $benefit = BenefitFixture::create();
        $benefitPlan = BenefitPlanFixture::create([
            'charges' => 1.5,
        ]);

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('find')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturn($benefitPlan);

        $this->benefitUsageDecorator
            ->shouldReceive('getConsumed')
            ->with($subscription->getSubscriberIdentifier(), $benefit->getId())
            ->andReturn(1.0);

        $this->assertFalse(
            $this->subscriptionManager->canConsume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0)
        );
    }

    public function testCanConsumeReturnsTrueEvenIfTheWantedAmountGetsAllRemainingCharges(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $benefit = BenefitFixture::create();
        $benefitPlan = BenefitPlanFixture::create([
            'charges' => 1.5,
        ]);

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('find')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturn($benefitPlan);

        $this->benefitUsageDecorator
            ->shouldReceive('getConsumed')
            ->with($subscription->getSubscriberIdentifier(), $benefit->getId())
            ->andReturn(0.5);

        $this->assertTrue(
            $this->subscriptionManager->canConsume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0)
        );
    }

    public function testCanConsumeReturnsTrueIfBenefitIsPositional(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $benefit = BenefitFixture::create([
            'isConsumable' => false,
        ]);

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldNotReceive('find');

        $this->benefitUsageDecorator
            ->shouldNotReceive('getConsumed');

        $this->assertTrue(
            $this->subscriptionManager->canConsume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0)
        );
    }

    public function testCanConsumeReturnsTrueIfBenefitPlanIsUnlimited(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $benefit = BenefitFixture::create();
        $benefitPlan = BenefitPlanFixture::create([
            'isUnlimited' => true,
        ]);

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('find')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturn($benefitPlan);

        $this->benefitUsageDecorator
            ->shouldNotReceive('getConsumed');

        $this->assertTrue(
            $this->subscriptionManager->canConsume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0)
        );
    }

    public function testConsumeIncraseUsageAmount(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
        ]);

        $benefit = BenefitFixture::create();
        $benefitPlan = BenefitPlanFixture::create([
            'charges' => 1.5,
        ]);

        $benefitUsage = BenefitUsageFixture::create([
            'amount' => 0.5,
            'expiredAt' => (new \DateTimeImmutable())->modify('+1 day'),
        ]);

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('find')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturn($benefitPlan);

        $this->benefitUsageDecorator
            ->shouldReceive('getConsumed')
            ->with($subscription->getSubscriberIdentifier(), $benefit->getId())
            ->andReturn(0.5);

        $this->benefitUsageDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier(), $benefit->getId())
            ->andReturn($benefitUsage);

        $this->benefitUsageDecorator
            ->shouldReceive('save')
            ->withArgs(fn (BenefitUsage $savedBenefitUsage): bool => 1.5 === $savedBenefitUsage->getAmount());

        $this->subscriptionManager->consume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0);

        $this->assertTrue(true);
    }

    public function testConsumeCreatesUsageIfThereIsNone(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
            'startedAt' => (new \DateTimeImmutable())->modify('-1 day'),
        ]);

        $benefit = BenefitFixture::create([
            'periodicity' => \DateInterval::createFromDateString('1 month'),
        ]);

        $benefitPlan = BenefitPlanFixture::create([
            'charges' => 1.5,
        ]);

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('find')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturn($benefitPlan);

        $this->benefitUsageDecorator
            ->shouldReceive('getConsumed')
            ->with($subscription->getSubscriberIdentifier(), $benefit->getId())
            ->andReturn(0.5);

        $this->benefitUsageDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier(), $benefit->getId())
            ->andReturnNull();

        $this->benefitUsageDecorator
            ->shouldReceive('create')
            ->withArgs(
                fn (
                    string $subscriberIdentifier,
                    int $benefitId,
                    float $amount,
                    ?\DateTimeImmutable $expiration
                ): bool => $subscriberIdentifier === $subscription->getSubscriberIdentifier()
                    and $benefitId === $benefit->getId()
                    and 1.0 === $amount
                    and $expiration->getTimestamp() === $subscription->getStartedAt()->add($benefit->getPeriodicity())->getTimestamp()
            );

        $this->benefitUsageDecorator
            ->shouldNotReceive('save');

        $this->subscriptionManager->consume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0);

        $this->assertTrue(true);
    }

    public function testConsumeRenewsUsageIfItIsExpired(): void
    {
        $subscription = SubscriptionFixture::create([
            'expiredAt' => null,
            'startedAt' => (new \DateTimeImmutable())->modify('-1 day'),
        ]);

        $benefit = BenefitFixture::create([
            'periodicity' => \DateInterval::createFromDateString('1 month'),
        ]);

        $benefitPlan = BenefitPlanFixture::create([
            'charges' => 1.5,
        ]);

        $benefitUsage = BenefitUsageFixture::create([
            'amount' => 0.5,
            'expiredAt' => (new \DateTimeImmutable())->modify('-1 day'),
        ]);

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier())
            ->andReturn($subscription);

        $this->benefitDecorator
            ->shouldReceive('find')
            ->with($benefit->getName())
            ->andReturn($benefit);

        $this->benefitPlanDecorator
            ->shouldReceive('find')
            ->with($benefit->getId(), $subscription->getPlanId())
            ->andReturn($benefitPlan);

        $this->benefitUsageDecorator
            ->shouldReceive('getConsumed')
            ->with($subscription->getSubscriberIdentifier(), $benefit->getId())
            ->andReturn(0.5);

        $this->benefitUsageDecorator
            ->shouldReceive('find')
            ->with($subscription->getSubscriberIdentifier(), $benefit->getId())
            ->andReturn($benefitUsage);

        $this->benefitUsageDecorator
            ->shouldNotReceive('create');

        $this->benefitUsageDecorator
            ->shouldReceive('save')
            ->withArgs(fn (BenefitUsage $savedBenefitUsage): bool => 1.0 === $savedBenefitUsage->getAmount()
                and $savedBenefitUsage->getExpiredAt()->getTimestamp() === $subscription->getStartedAt()->add($benefit->getPeriodicity())->getTimestamp()
            );

        $this->subscriptionManager->consume($subscription->getSubscriberIdentifier(), $benefit->getName(), 1.0);

        $this->assertTrue(true);
    }

    public function testConsumeThrowsExceptionIfCantConsume(): void
    {
        $benefit = BenefitFixture::create();

        $this->subscriptionDecorator
            ->shouldReceive('find')
            ->with('subscriber-identifier')
            ->andReturnNull();

        $this->benefitDecorator
            ->shouldNotReceive('find');

        $this->benefitPlanDecorator
            ->shouldNotReceive('find');

        $this->benefitUsageDecorator
            ->shouldNotReceive('getConsumed');

        $this->benefitUsageDecorator
            ->shouldNotReceive('find');

        $this->benefitUsageDecorator
            ->shouldNotReceive('create');

        $this->benefitUsageDecorator
            ->shouldNotReceive('save');

        $this->expectException(CantConsumeException::class);

        $this->subscriptionManager->consume('subscriber-identifier', $benefit->getName(), 1.0);
    }

    public function testFlushContextCallsAllDecorators(): void
    {
        $this->subscriptionDecorator
            ->shouldReceive('flushContext');

        $this->benefitDecorator
            ->shouldReceive('flushContext');

        $this->benefitPlanDecorator
            ->shouldReceive('flushContext');

        $this->benefitUsageDecorator
            ->shouldReceive('flushContext');

        $this->subscriptionManager->flushContext();

        $this->assertTrue(true);
    }
}
