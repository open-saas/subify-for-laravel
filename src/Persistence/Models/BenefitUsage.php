<?php

namespace OpenSaaS\Subify\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\BenefitFactory;

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

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class);
    }

    protected static function newFactory(): BenefitFactory
    {
        return BenefitFactory::new();
    }
}
