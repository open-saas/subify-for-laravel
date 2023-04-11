<?php

namespace Tests\Feature\Repositories\Eloquent\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSaaS\Subify\Entities\Subscription as SubscriptionEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Plan;
use OpenSaaS\Subify\Repositories\Eloquent\Models\PlanRegime;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Subscription;
use Tests\Feature\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created(): void
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

    public function test_it_soft_deletes(): void
    {
        $subscription = Subscription::factory()->create();

        $subscription->delete();

        $this->assertSoftDeleted($subscription);
    }

    public function test_it_belongs_to_a_plan(): void
    {
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()
            ->for($plan)
            ->create();

        $this->assertEquals($plan->id, $subscription->plan->id);
    }

    public function test_it_belongs_to_a_plan_regime(): void
    {
        $planRegime = PlanRegime::factory()->create();

        $subscription = Subscription::factory()
            ->for($planRegime)
            ->create();

        $this->assertEquals($planRegime->id, $subscription->planRegime->id);
    }

    public function test_it_has_a_global_scope_to_only_return_not_expired(): void
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

    public function test_it_has_a_scope_to_only_return_expired(): void
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

    public function test_it_has_a_scope_to_return_with_expired(): void
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

    public function test_it_has_a_scope_to_only_return_in_grace(): void
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

    public function test_it_has_a_scope_to_only_return_in_trial(): void
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

    public function test_it_has_a_global_scope_to_only_started_subscriptions(): void
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

    public function test_it_has_a_scope_to_return_only_unstarted_subscriptions(): void
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

    public function test_it_has_a_method_to_convert_to_entity(): void
    {
        $subscription = Subscription::factory()->create();
        $subscriptionEntity = $subscription->toEntity();

        $expectedSubscriberIdentifier = $subscription->subscriber_id . ':' . $subscription->subscriber_type;

        $this->assertInstanceOf(SubscriptionEntity::class, $subscriptionEntity);
        $this->assertEquals($subscription->id, $subscriptionEntity->getId());
        $this->assertEquals($expectedSubscriberIdentifier, $subscriptionEntity->getSubscriberIdentifier());
        $this->assertEquals($subscription->plan_id, $subscriptionEntity->getPlanId());
        $this->assertEquals($subscription->plan_regime_id, $subscriptionEntity->getPlanRegimeId());
        $this->assertEquals($subscription->grace_ended_at, $subscriptionEntity->getGraceEndedAt());
        $this->assertEquals($subscription->trial_ended_at, $subscriptionEntity->getTrialEndedAt());
        $this->assertEquals($subscription->renewed_at, $subscriptionEntity->getRenewedAt());
        $this->assertEquals($subscription->expired_at, $subscriptionEntity->getExpiredAt());
        $this->assertEquals($subscription->created_at, $subscriptionEntity->getCreatedAt());
        $this->assertEquals($subscription->updated_at, $subscriptionEntity->getUpdatedAt());
    }
}
