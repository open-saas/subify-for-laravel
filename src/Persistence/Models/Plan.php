<?php

namespace OpenSaaS\Subify\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function regimes(): HasMany
    {
        return $this->hasMany(PlanRegime::class);
    }
}
