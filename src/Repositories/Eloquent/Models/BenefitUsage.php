<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\BenefitUsageFactory;

/**
 * @property int                        $id
 * @property int                        $benefit_id
 * @property int                        $subscriber_id
 * @property string                     $subscriber_type
 * @property float                      $amount
 * @property \Illuminate\Support\Carbon $expired_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 */
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

    public function getTable(): string
    {
        return config('subify.repositories.eloquent.benefit_usage.table');
    }

    protected static function booted()
    {
        static::addGlobalScope('expirable', function (Builder $builder) {
            $builder->where(
                fn (Builder $query) => $query->whereNull('expired_at')->orWhere('expired_at', '>', now())
            );
        });
    }

    protected static function newFactory(): BenefitUsageFactory
    {
        return BenefitUsageFactory::new();
    }
}
