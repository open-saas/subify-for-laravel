<?php

namespace Tests\Feature\Repositories\Eloquent;

use OpenSaaS\Subify\Database\Factories\SubscriptionFactory;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Subscription;
use OpenSaaS\Subify\Repositories\Eloquent\SubscriptionRepository;
use Tests\Feature\TestCase;
use Tests\Fixtures\SubscriptionFixture;

/**
 * @internal
 */
class SubscriptionRepositoryTest extends TestCase
{
    private SubscriptionRepository $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(SubscriptionRepository::class);
    }

    public function testFindActiveReturnsActiveSubscription(): void
    {
        $activeSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->addDay(),
                'grace_ended_at' => now()->subDay(),
                'trial_ended_at' => now()->subDay(),
            ]);

        $activeSubscriptionIdentifier = $activeSubscription->subscriber_type
            .':'
            .$activeSubscription->subscriber_id;

        $this->assertEquals(
            $activeSubscription->toEntity(),
            $this->repository->findActive($activeSubscriptionIdentifier)
        );
    }

    public function testFindActiveReturnsNullWhenNoActiveSubscription(): void
    {
        $expiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->subDay(),
                'grace_ended_at' => now()->subDay(),
                'trial_ended_at' => now()->subDay(),
            ]);

        $expiredSubscriptionIdentifier = $expiredSubscription->subscriber_type
            .':'
            .$expiredSubscription->subscriber_id;

        $this->assertNull(
            $this->repository->findActive($expiredSubscriptionIdentifier)
        );
    }

    public function testFindActiveReturnsNullWhenNoSubscription(): void
    {
        $this->assertNull(
            $this->repository->findActive('App\\User:1')
        );
    }

    public function testFindActiveReturnsSubscriptionWithGrace(): void
    {
        $subscription = Subscription::factory()
            ->create([
                'expired_at' => now()->subDay(),
                'grace_ended_at' => now()->addDay(),
                'trial_ended_at' => now()->subDay(),
            ]);

        $subscriptionIdentifier = $subscription->subscriber_type
            .':'
            .$subscription->subscriber_id;

        $this->assertEquals(
            $subscription->toEntity(),
            $this->repository->findActive($subscriptionIdentifier)
        );
    }

    public function testFindActiveReturnsSubscriptionWithTrial(): void
    {
        $subscription = Subscription::factory()
            ->create([
                'expired_at' => now()->subDay(),
                'grace_ended_at' => now()->subDay(),
                'trial_ended_at' => now()->addDay(),
            ]);

        $subscriptionIdentifier = $subscription->subscriber_type
            .':'
            .$subscription->subscriber_id;

        $this->assertEquals(
            $subscription->toEntity(),
            $this->repository->findActive($subscriptionIdentifier)
        );
    }

    public function testFindActiveReturnsStartedSubscription(): void
    {
        $subscription = Subscription::factory()
            ->create([
                'expired_at' => now()->addDay(),
                'started_at' => now()->subDay(),
            ]);

        $subscriptionIdentifier = $subscription->subscriber_type
            .':'
            .$subscription->subscriber_id;

        $this->assertEquals(
            $subscription->toEntity(),
            $this->repository->findActive($subscriptionIdentifier)
        );
    }

    public function testFindActiveReturnsNullWhenThereAreNoStartedSubscriptions(): void
    {
        $subscription = Subscription::factory()
            ->create([
                'expired_at' => now()->addDay(),
                'started_at' => now()->addDay(),
            ]);

        $subscriptionIdentifier = $subscription->subscriber_type
            .':'
            .$subscription->subscriber_id;

        $this->assertNull($this->repository->findActive($subscriptionIdentifier));
    }

    public function testInsert(): void
    {
        $subscription = Subscription::factory()->make();

        $this->repository->insert(
            $subscription->subscriber_type . ':' . $subscription->subscriber_id,
            $subscription->plan_id,
            $subscription->plan_regime_id,
            $subscription->started_at,
            $subscription->expired_at,
            $subscription->grace_ended_at,
            $subscription->trial_ended_at,
        );

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_type' => $subscription->subscriber_type,
            'subscriber_id' => $subscription->subscriber_id,
            'plan_id' => $subscription->plan_id,
            'plan_regime_id' => $subscription->plan_regime_id,
            'started_at' => $subscription->started_at,
            'expired_at' => $subscription->expired_at,
            'grace_ended_at' => $subscription->grace_ended_at,
            'trial_ended_at' => $subscription->trial_ended_at,
        ]);
    }
}
