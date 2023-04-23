<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\BenefitUsageFactory;
use OpenSaaS\Subify\Entities\BenefitUsage as BenefitUsageEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Concerns\HasSubscriberIdentifier;

/**
 * @property int                         $id
 * @property int                         $benefit_id
 * @property string                      $subscriber_id
 * @property string                      $subscriber_type
 * @property float                       $amount
 * @property ?\Illuminate\Support\Carbon $expired_at
 * @property \Illuminate\Support\Carbon  $created_at
 * @property \Illuminate\Support\Carbon  $updated_at
 * @property \Illuminate\Support\Carbon  $deleted_at
 */
class BenefitUsage extends Model
{
    use HasFactory;
    use HasSubscriberIdentifier;
    use SoftDeletes;

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    protected $fillable = [
        'benefit_id',
        'amount',
        'subscriber_id',
        'subscriber_type',
        'expired_at',
    ];

    public function scopeOnlyExpired(Builder $query): Builder
    {
        return $query->where('expired_at', '<=', now());
    }

    public function scopeWithoutExpired(Builder $query): Builder
    {
        return $query->where(
            fn (Builder $query) => $query
                ->whereNull('expired_at')
                ->orWhere('expired_at', '>', now())
        );
    }

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class);
    }

    public function getTable(): string
    {
        return config('subify.repositories.eloquent.benefit_usage.table');
    }

    public function toEntity(): BenefitUsageEntity
    {
        return new BenefitUsageEntity(
            $this->id,
            $this->getSubscriberIdentifier(),
            $this->benefit_id,
            $this->amount,
            $this->expired_at?->toDateTimeImmutable(),
        );
    }

    protected static function newFactory(): BenefitUsageFactory
    {
        return BenefitUsageFactory::new();
    }
}
