<?php

namespace Tests\Feature\Repositories\Eloquent\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Entities\Subscription as SubscriptionEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Plan;
use OpenSaaS\Subify\Repositories\Eloquent\Models\PlanRegime;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Subscription;
use Tests\Feature\TestCase;

/**
 * @internal
 */
class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function testItCanBeCreated(): void
    {
        $plan = Plan::factory()->create();
        $planRegime = PlanRegime::factory()->create();

        $subscription = Subscription::create([
            'plan_id' => $plan->id,
            'plan_regime_id' => $planRegime->id,
            'subscriber_id' => 1,
            'subscriber_type' => 'user',
            'grace_ended_at' => now()->addDays(30),
            'trial_ended_at' => now()->addDays(30),
            'renewed_at' => now()->addDays(30),
            'expired_at' => now()->addDays(30),
            'started_at' => now()->subDays(30),
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'plan_id' => $plan->id,
            'plan_regime_id' => $planRegime->id,
            'subscriber_id' => $subscription->subscriber_id,
            'subscriber_type' => $subscription->subscriber_type,
            'grace_ended_at' => $subscription->grace_ended_at,
            'trial_ended_at' => $subscription->trial_ended_at,
            'renewed_at' => $subscription->renewed_at,
            'expired_at' => $subscription->expired_at,
            'started_at' => $subscription->started_at,
        ]);
    }

    public function testItDoesNotSoftDeletes(): void
    {
        $subscription = Subscription::factory()->create();

        $subscription->delete();

        $this->assertDatabaseMissing('subscriptions', [
            'id' => $subscription->id,
        ]);
    }

    public function testItBelongsToAPlan(): void
    {
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()
            ->for($plan)
            ->create();

        $this->assertEquals($plan->id, $subscription->plan->id);
    }

    public function testItBelongsToAPlanRegime(): void
    {
        $planRegime = PlanRegime::factory()->create();

        $subscription = Subscription::factory()
            ->for($planRegime)
            ->create();

        $this->assertEquals($planRegime->id, $subscription->planRegime->id);
    }

    public function testItHasAGlobalScopeToOnlyReturnNotExpired(): void
    {
        $expiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $unexpiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->addDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithGrace = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => now()->addDays(30),
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithPastGrace = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => now()->subDays(30),
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithTrial = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => now()->addDays(30),
            ]);

        $expiredSubscriptionWithPastTrial = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => now()->subDays(30),
            ]);

        $this->assertEmpty(Subscription::find($expiredSubscription->id));
        $this->assertNotEmpty(Subscription::find($unexpiredSubscription->id));
        $this->assertNotEmpty(Subscription::find($expiredSubscriptionWithGrace->id));
        $this->assertEmpty(Subscription::find($expiredSubscriptionWithPastGrace->id));
        $this->assertNotEmpty(Subscription::find($expiredSubscriptionWithTrial->id));
        $this->assertEmpty(Subscription::find($expiredSubscriptionWithPastTrial->id));
    }

    public function testItHasAScopeToOnlyReturnExpired(): void
    {
        $expiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $unexpiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->addDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithGrace = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => now()->addDays(30),
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithPastGrace = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => now()->subDays(30),
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithTrial = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => now()->addDays(30),
            ]);

        $expiredSubscriptionWithPastTrial = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => now()->subDays(30),
            ]);

        $this->assertNotEmpty(Subscription::onlyExpired()->find($expiredSubscription->id));
        $this->assertEmpty(Subscription::onlyExpired()->find($unexpiredSubscription->id));
        $this->assertNotEmpty(Subscription::onlyExpired()->find($expiredSubscriptionWithGrace->id));
        $this->assertNotEmpty(Subscription::onlyExpired()->find($expiredSubscriptionWithPastGrace->id));
        $this->assertNotEmpty(Subscription::onlyExpired()->find($expiredSubscriptionWithTrial->id));
        $this->assertNotEmpty(Subscription::onlyExpired()->find($expiredSubscriptionWithPastTrial->id));
    }

    public function testItHasAScopeToReturnWithExpired(): void
    {
        $expiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $unexpiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->addDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithGrace = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => now()->addDays(30),
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithPastGrace = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => now()->subDays(30),
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithTrial = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => now()->addDays(30),
            ]);

        $expiredSubscriptionWithPastTrial = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => now()->subDays(30),
            ]);

        $this->assertNotEmpty(Subscription::withExpired()->find($expiredSubscription->id));
        $this->assertNotEmpty(Subscription::withExpired()->find($unexpiredSubscription->id));
        $this->assertNotEmpty(Subscription::withExpired()->find($expiredSubscriptionWithGrace->id));
        $this->assertNotEmpty(Subscription::withExpired()->find($expiredSubscriptionWithPastGrace->id));
        $this->assertNotEmpty(Subscription::withExpired()->find($expiredSubscriptionWithTrial->id));
        $this->assertNotEmpty(Subscription::withExpired()->find($expiredSubscriptionWithPastTrial->id));
    }

    public function testItHasAScopeToOnlyReturnInGrace(): void
    {
        $expiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $unexpiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->addDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithGrace = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => now()->addDays(30),
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithPastGrace = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => now()->subDays(30),
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithTrial = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => now()->addDays(30),
            ]);

        $expiredSubscriptionWithPastTrial = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => now()->subDays(30),
            ]);

        $this->assertEmpty(Subscription::inGrace()->find($expiredSubscription->id));
        $this->assertEmpty(Subscription::inGrace()->find($unexpiredSubscription->id));
        $this->assertNotEmpty(Subscription::inGrace()->find($expiredSubscriptionWithGrace->id));
        $this->assertEmpty(Subscription::inGrace()->find($expiredSubscriptionWithPastGrace->id));
        $this->assertEmpty(Subscription::inGrace()->find($expiredSubscriptionWithTrial->id));
        $this->assertEmpty(Subscription::inGrace()->find($expiredSubscriptionWithPastTrial->id));
    }

    public function testItHasAScopeToOnlyReturnInTrial(): void
    {
        $expiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $unexpiredSubscription = Subscription::factory()
            ->create([
                'expired_at' => now()->addDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithGrace = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => now()->addDays(30),
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithPastGrace = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => now()->subDays(30),
                'trial_ended_at' => null,
            ]);

        $expiredSubscriptionWithTrial = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => now()->addDays(30),
            ]);

        $expiredSubscriptionWithPastTrial = Subscription::factory()
            ->create([
                'expired_at' => now()->subDays(30),
                'grace_ended_at' => null,
                'trial_ended_at' => now()->subDays(30),
            ]);

        $this->assertEmpty(Subscription::inTrial()->find($expiredSubscription->id));
        $this->assertEmpty(Subscription::inTrial()->find($unexpiredSubscription->id));
        $this->assertEmpty(Subscription::inTrial()->find($expiredSubscriptionWithGrace->id));
        $this->assertEmpty(Subscription::inTrial()->find($expiredSubscriptionWithPastGrace->id));
        $this->assertNotEmpty(Subscription::inTrial()->find($expiredSubscriptionWithTrial->id));
        $this->assertEmpty(Subscription::inTrial()->find($expiredSubscriptionWithPastTrial->id));
    }

    public function testItHasAGlobalScopeToOnlyStartedSubscriptions(): void
    {
        $startedSubscription = Subscription::factory()
            ->create([
                'started_at' => now()->subDays(30),
                'expired_at' => null,
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $unstartedSubscription = Subscription::factory()
            ->create([
                'started_at' => now()->addDays(30),
                'expired_at' => null,
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $unstartedSubscriptionWithGrace = Subscription::factory()
            ->create([
                'started_at' => now()->addDays(30),
                'expired_at' => null,
                'grace_ended_at' => now()->addDays(30),
                'trial_ended_at' => null,
            ]);

        $unstartedSubscriptionWithTrial = Subscription::factory()
            ->create([
                'started_at' => now()->addDays(30),
                'expired_at' => null,
                'grace_ended_at' => null,
                'trial_ended_at' => now()->addDays(30),
            ]);

        $this->assertNotEmpty(Subscription::find($startedSubscription->id));
        $this->assertEmpty(Subscription::find($unstartedSubscription->id));
        $this->assertEmpty(Subscription::find($unstartedSubscriptionWithGrace->id));
        $this->assertEmpty(Subscription::find($unstartedSubscriptionWithTrial->id));
    }

    public function testItHasAScopeToReturnOnlyUnstartedSubscriptions(): void
    {
        $startedSubscription = Subscription::factory()
            ->create([
                'started_at' => now()->subDays(30),
                'expired_at' => null,
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $unstartedSubscription = Subscription::factory()
            ->create([
                'started_at' => now()->addDays(30),
                'expired_at' => null,
                'grace_ended_at' => null,
                'trial_ended_at' => null,
            ]);

        $unstartedSubscriptionWithGrace = Subscription::factory()
            ->create([
                'started_at' => now()->addDays(30),
                'expired_at' => null,
                'grace_ended_at' => now()->addDays(30),
                'trial_ended_at' => null,
            ]);

        $unstartedSubscriptionWithTrial = Subscription::factory()
            ->create([
                'started_at' => now()->addDays(30),
                'expired_at' => null,
                'grace_ended_at' => null,
                'trial_ended_at' => now()->addDays(30),
            ]);

        $this->assertEmpty(Subscription::unstarted()->find($startedSubscription->id));
        $this->assertNotEmpty(Subscription::unstarted()->find($unstartedSubscription->id));
        $this->assertNotEmpty(Subscription::unstarted()->find($unstartedSubscriptionWithGrace->id));
        $this->assertNotEmpty(Subscription::unstarted()->find($unstartedSubscriptionWithTrial->id));
    }

    public function testItHasAMethodToConvertToEntity(): void
    {
        $subscription = Subscription::factory()->create();
        $subscriptionEntity = $subscription->toEntity();

        $expectedSubscriberIdentifier = $subscription->subscriber_type.':'.$subscription->subscriber_id;

        $this->assertInstanceOf(SubscriptionEntity::class, $subscriptionEntity);
        $this->assertEquals($subscription->id, $subscriptionEntity->getId());
        $this->assertEquals($expectedSubscriberIdentifier, $subscriptionEntity->getSubscriberIdentifier());
        $this->assertEquals($subscription->plan_id, $subscriptionEntity->getPlanId());
        $this->assertEquals($subscription->plan_regime_id, $subscriptionEntity->getPlanRegimeId());
        $this->assertEquals($subscription->grace_ended_at, $subscriptionEntity->getGraceEndedAt());
        $this->assertEquals($subscription->trial_ended_at, $subscriptionEntity->getTrialEndedAt());
        $this->assertEquals($subscription->renewed_at, $subscriptionEntity->getRenewedAt());
        $this->assertEquals($subscription->expired_at, $subscriptionEntity->getExpiredAt());
    }
}
