<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OpenSaaS\Subify\Database\Factories\BenefitPlanFactory;

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

    protected static function newFactory(): BenefitPlanFactory
    {
        return BenefitPlanFactory::new();
    }
}
