<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\PlanRegimeFactory;
use OpenSaaS\Subify\Entities\PlanRegime as PlanRegimeEntity;
use OpenSaaS\Subify\Enums\PeriodicityUnit;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Concerns\HasPeriodicityFields;

/**
 * @property int                        $id
 * @property int                        $plan_id
 * @property string                     $name
 * @property float                      $price
 * @property int                        $periodicity
 * @property PeriodicityUnit            $periodicity_unit
 * @property int                        $grace
 * @property PeriodicityUnit            $grace_unit
 * @property int                        $trial
 * @property PeriodicityUnit            $trial_unit
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 */
class PlanRegime extends Model
{
    use HasFactory;
    use HasPeriodicityFields;
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

    /**
     * @internal
     */
    public function toEntity(): PlanRegimeEntity
    {
        return new PlanRegimeEntity(
            $this->id,
            $this->plan_id,
            $this->periodicityToDateInterval($this->periodicity_unit, $this->periodicity),
            $this->periodicityToDateInterval($this->grace_unit, $this->grace),
            $this->periodicityToDateInterval($this->trial_unit, $this->trial),
        );
    }

    protected static function newFactory(): PlanRegimeFactory
    {
        return PlanRegimeFactory::new();
    }
}
