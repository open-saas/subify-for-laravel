<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models\Concerns;

/**
 * @property string $subscriber_id
 * @property string $subscriber_type
 */
trait HasSubscriberIdentifier
{
    public function getSubscriberIdentifier(): string
    {
        return $this->subscriber_type.':'.$this->subscriber_id;
    }

    public function setSubscriberIdentifier(string $subscriberIdentifier): void
    {
        [$subscriberType, $subscriberId] = explode(':', $subscriberIdentifier);

        $this->subscriber_type = $subscriberType;
        $this->subscriber_id = $subscriberId;
    }
}
