<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use DateInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\PlanRegimeFactory;
use OpenSaaS\Subify\Entities\PlanRegime as PlanRegimeEntity;
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

    public function getTable(): string
    {
        return config('subify.repositories.eloquent.plan_regime.table');
    }

    protected static function newFactory(): PlanRegimeFactory
    {
        return PlanRegimeFactory::new();
    }

    public function toEntity(): PlanRegimeEntity
    {
        $periodicity = $this->periodicity
            ? DateInterval::createFromDateString($this->periodicity . ' ' . $this->periodicity_unit->value)
            : null;

        $grace = $this->grace
            ? DateInterval::createFromDateString($this->grace . ' ' . $this->grace_unit->value)
            : null;

        $trial = $this->trial
            ? DateInterval::createFromDateString($this->trial . ' ' . $this->trial_unit->value)
            : null;

        return new PlanRegimeEntity(
            $this->id,
            $this->plan_id,
            $this->name,
            $this->price,
            $periodicity,
            $grace,
            $trial,
            $this->created_at,
            $this->updated_at,
        );
    }
}
