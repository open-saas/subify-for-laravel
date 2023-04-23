<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Concerns;

trait QueriesBySubscriberIdentifier
{
    private function subscriberIs(string $subscriberIdentifier): array
    {
        [$subscriberType, $subscriberId] = explode(':', $subscriberIdentifier);

        return [
            'subscriber_id' => $subscriberId,
            'subscriber_type' => $subscriberType,
        ];
    }
}
