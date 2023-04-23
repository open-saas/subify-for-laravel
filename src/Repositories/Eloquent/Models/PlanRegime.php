<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\PlanRegimeFactory;
use OpenSaaS\Subify\Entities\PlanRegime as PlanRegimeEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Casts\Interval;

/**
 * @property int                        $id
 * @property int                        $plan_id
 * @property string                     $name
 * @property float                      $price
 * @property ?\DateInterval             $periodicity
 * @property ?\DateInterval             $grace
 * @property ?\DateInterval             $trial
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 */
class PlanRegime extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'periodicity' => Interval::class,
        'grace' => Interval::class,
        'trial' => Interval::class,
    ];

    protected $fillable = [
        'plan_id',
        'name',
        'price',
        'periodicity',
        'grace',
        'trial',
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
            $this->periodicity,
            $this->grace,
            $this->trial,
        );
    }

    protected static function newFactory(): PlanRegimeFactory
    {
        return PlanRegimeFactory::new();
    }
}
