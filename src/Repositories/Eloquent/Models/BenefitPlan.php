<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OpenSaaS\Subify\Database\Factories\BenefitPlanFactory;

/**
 * @property int                        $id
 * @property int                        $benefit_id
 * @property int                        $plan_id
 * @property float                      $charges
 * @property bool                       $is_unlimited
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 */
class BenefitPlan extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'benefit_id',
        'plan_id',
        'charges',
        'is_unlimited',
    ];

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function getTable(): string
    {
        return config('subify.repositories.eloquent.benefit_plan.table');
    }

    protected static function newFactory(): BenefitPlanFactory
    {
        return BenefitPlanFactory::new();
    }
}
