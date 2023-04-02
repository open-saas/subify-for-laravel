<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\BenefitUsageFactory;

class BenefitUsage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'benefit_id',
        'amount',
        'subscriber_id',
        'subscriber_type',
        'expired_at',
    ];

    protected static function booted()
    {
        static::addGlobalScope('expirable', function (Builder $builder) {
            $builder->where(fn (Builder $query) =>
                $query->whereNull('expired_at')->orWhere('expired_at', '>', now())
            );
        });
    }

    public function scopeOnlyExpired(Builder $query): Builder
    {
        return $query->withoutGlobalScope('expirable')
            ->where('expired_at', '<=', now());
    }

    public function scopeWithExpired(Builder $query): Builder
    {
        return $query->withoutGlobalScope('expirable');
    }

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class);
    }

    protected static function newFactory(): BenefitUsageFactory
    {
        return BenefitUsageFactory::new();
    }
}
