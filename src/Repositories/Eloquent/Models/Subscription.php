<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\SubscriptionFactory;

class Subscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'plan_id',
        'plan_regime_id',
        'subscriber_id',
        'subscriber_type',
        'grace_ended_at',
        'trial_ended_at',
        'renewed_at',
        'expired_at',
    ];

    protected static function booted()
    {
        static::addGlobalScope('expirableWithGraceAndTrial', function (Builder $builder) {
            $builder->where(fn (Builder $query) =>
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now())
                    ->orWhere('grace_ended_at', '>', now())
                    ->orWhere('trial_ended_at', '>', now())
            );
        });
    }

    public function scopeOnlyExpired(Builder $query): Builder
    {
        return $query->withoutGlobalScope('expirableWithGraceAndTrial')
            ->where('expired_at', '<=', now());
    }

    public function scopeWithExpired(Builder $query): Builder
    {
        return $query->withoutGlobalScope('expirableWithGraceAndTrial');
    }

    public function scopeInGrace(Builder $query): Builder
    {
        return $query->withoutGlobalScope('expirableWithGraceAndTrial')
            ->where('expired_at', '<=', now())
            ->where('grace_ended_at', '>', now());
    }

    public function scopeInTrial(Builder $query): Builder
    {
        return $query->withoutGlobalScope('expirableWithGraceAndTrial')
            ->where('expired_at', '<=', now())
            ->where('trial_ended_at', '>', now());
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function planRegime(): BelongsTo
    {
        return $this->belongsTo(PlanRegime::class);
    }

    protected static function newFactory(): SubscriptionFactory
    {
        return SubscriptionFactory::new();
    }
}
