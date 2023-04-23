<?php

namespace Tests\Unit\Repositories\Array;

use OpenSaaS\Subify\Repositories\Context\SubscriptionRepository;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\SubscriptionFixture;

/**
 * @internal
 */
class SubscriptionRepositoryTest extends TestCase
{
    private SubscriptionRepository $repository;

    private \ReflectionProperty $subscriptionsProperty;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new SubscriptionRepository();
        $this->subscriptionsProperty = new \ReflectionProperty($this->repository, 'subscriptionsByIdentifier');
    }

    public function testFindReturnsSubscription(): void
    {
        $expectedSubscription = SubscriptionFixture::create();
        $subscriberIdentifier = $expectedSubscription->getSubscriberIdentifier();

        $this->subscriptionsProperty->setValue($this->repository, [$subscriberIdentifier => $expectedSubscription]);

        $actualSubscription = $this->repository->find($subscriberIdentifier);

        $this->assertEquals($expectedSubscription, $actualSubscription);
    }

    public function testFindReturnsNull(): void
    {
        $this->subscriptionsProperty->setValue($this->repository, []);

        $actualSubscription = $this->repository->find('non-existing-subscriber-identifier');

        $this->assertNull($actualSubscription);
    }

    public function testSaveAddsSubscriptionToArray(): void
    {
        $expectedSubscription = SubscriptionFixture::create();
        $subscriberIdentifier = $expectedSubscription->getSubscriberIdentifier();

        $this->subscriptionsProperty->setValue($this->repository, []);

        $this->repository->save($expectedSubscription);

        $actualSubscription = $this->subscriptionsProperty->getValue($this->repository);

        $this->assertEquals([$subscriberIdentifier => $expectedSubscription], $actualSubscription);
    }

    public function testFlushContextClearsArray(): void
    {
        $this->subscriptionsProperty->setValue($this->repository, [SubscriptionFixture::create()->getSubscriberIdentifier() => SubscriptionFixture::create()]);

        $this->repository->flush();

        $actualSubscription = $this->subscriptionsProperty->getValue($this->repository);

        $this->assertEquals([], $actualSubscription);
    }

    public function testHasReturnsTrue(): void
    {
        $expectedSubscription = SubscriptionFixture::create();
        $subscriberIdentifier = $expectedSubscription->getSubscriberIdentifier();

        $this->subscriptionsProperty->setValue($this->repository, [$subscriberIdentifier => $expectedSubscription]);

        $actualSubscription = $this->repository->has($subscriberIdentifier);

        $this->assertTrue($actualSubscription);
    }

    public function testHasReturnsFalse(): void
    {
        $this->subscriptionsProperty->setValue($this->repository, []);

        $actualSubscription = $this->repository->has('non-existing-subscriber-identifier');

        $this->assertFalse($actualSubscription);
    }
}
