<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenSaaS\Subify\Database\Factories\SubscriptionFactory;
use OpenSaaS\Subify\Entities\Subscription as SubscriptionEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Concerns\HasSubscriberIdentifier;

/**
 * @property int                         $id
 * @property int                         $plan_id
 * @property int                         $plan_regime_id
 * @property string                      $subscriber_id
 * @property string                      $subscriber_type
 * @property ?\Illuminate\Support\Carbon $grace_ended_at
 * @property ?\Illuminate\Support\Carbon $trial_ended_at
 * @property ?\Illuminate\Support\Carbon $renewed_at
 * @property ?\Illuminate\Support\Carbon $expired_at
 * @property \Illuminate\Support\Carbon  $started_at
 * @property \Illuminate\Support\Carbon  $created_at
 * @property \Illuminate\Support\Carbon  $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
class Subscription extends Model
{
    use HasFactory;
    use HasSubscriberIdentifier;

    protected $casts = [
        'grace_ended_at' => 'datetime',
        'trial_ended_at' => 'datetime',
        'renewed_at' => 'datetime',
        'expired_at' => 'datetime',
        'started_at' => 'datetime',
    ];

    protected $fillable = [
        'plan_id',
        'plan_regime_id',
        'subscriber_id',
        'subscriber_type',
        'grace_ended_at',
        'trial_ended_at',
        'renewed_at',
        'expired_at',
        'started_at',
    ];

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

    public function scopeUnstarted(Builder $query): Builder
    {
        return $query->withoutGlobalScope('expirableWithGraceAndTrial')
            ->where('started_at', '>', now());
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function planRegime(): BelongsTo
    {
        return $this->belongsTo(PlanRegime::class);
    }

    public function getTable(): string
    {
        return config('subify.repositories.eloquent.subscription.table');
    }

    /**
     * @internal
     */
    public function toEntity(): SubscriptionEntity
    {
        return new SubscriptionEntity(
            $this->id,
            $this->getSubscriberIdentifier(),
            $this->plan_id,
            $this->plan_regime_id,
            $this->started_at->toDateTimeImmutable(),
            $this->grace_ended_at?->toDateTimeImmutable(),
            $this->trial_ended_at?->toDateTimeImmutable(),
            $this->renewed_at?->toDateTimeImmutable(),
            $this->expired_at?->toDateTimeImmutable(),
        );
    }

    protected static function booted()
    {
        static::addGlobalScope('expirableWithGraceAndTrial', function (Builder $builder) {
            $builder->orderByDesc('started_at')
                ->where('started_at', '<=', now())
                ->where(
                    fn (Builder $query) => $query->whereNull('expired_at')
                        ->orWhere('expired_at', '>', now())
                        ->orWhere('grace_ended_at', '>', now())
                        ->orWhere('trial_ended_at', '>', now())
                );
        });
    }

    protected static function newFactory(): SubscriptionFactory
    {
        return SubscriptionFactory::new();
    }
}
