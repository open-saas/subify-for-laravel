<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use OpenSaaS\Subify\Repositories\Concerns\SerializesIntervals;

class Interval implements CastsAttributes
{
    use SerializesIntervals;

    public function get(Model $model, string $key, mixed $value, array $attributes): ?\DateInterval
    {
        return $value ? $this->unserializeInterval($value) : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value ? $this->serializeInterval($value) : null;
    }
}
