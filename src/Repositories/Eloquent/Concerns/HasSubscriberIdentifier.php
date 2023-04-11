<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Concerns;

trait HasSubscriberIdentifier
{
    private function subscriberIs(string $subscriberIdentifier): array
    {
        [$subscriberType, $subscriberId] = explode(':', $subscriberIdentifier);

        return [
            'subscriber_id' => $subscriberId,
            'subscriber_type' => $subscriberType,
        ];
    }

    private function toSubscriberIdentifier(string $subscriberId, string $subscriberType): string
    {
        return $subscriberType.':'.$subscriberId;
    }
}
