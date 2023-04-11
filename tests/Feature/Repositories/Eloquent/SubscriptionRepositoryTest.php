<?php

namespace Tests\Feature\Repositories\Eloquent;

use OpenSaaS\Subify\Repositories\Eloquent\Models\Subscription;
use OpenSaaS\Subify\Repositories\Eloquent\SubscriptionRepository;
use Tests\Feature\TestCase;

/**
 * @internal
 *
 * @coversNothing
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
}
