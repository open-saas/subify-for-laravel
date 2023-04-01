<?php

namespace OpenSaaS\Subify\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\PlanRegimeFactory;
use OpenSaaS\Subify\Enums\PeriodicityUnit;

class PlanRegime extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'periodicity_unit' => PeriodicityUnit::class,
        'grace_unit' => PeriodicityUnit::class,
        'trial_unit' => PeriodicityUnit::class,
    ];

    protected $fillable = [
        'plan_id',
        'name',
        'price',
        'periodicity',
        'periodicity_unit',
        'grace',
        'grace_unit',
        'trial',
        'trial_unit',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    protected static function newFactory(): PlanRegimeFactory
    {
        return PlanRegimeFactory::new();
    }
}
