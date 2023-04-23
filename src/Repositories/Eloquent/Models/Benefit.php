<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenSaaS\Subify\Database\Factories\BenefitFactory;
use OpenSaaS\Subify\Entities\Benefit as BenefitEntity;
use OpenSaaS\Subify\Repositories\Eloquent\Models\Casts\Interval;

/**
 * @property int                        $id
 * @property string                     $name
 * @property bool                       $is_consumable
 * @property bool                       $is_quota
 * @property ?\DateInterval             $periodicity
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 */
class Benefit extends Model
{
    use HasFactory;

    protected $casts = [
        'periodicity' => Interval::class,
    ];

    protected $fillable = [
        'name',
        'is_consumable',
        'is_quota',
        'periodicity',
    ];

    public function getTable(): string
    {
        return config('subify.repositories.eloquent.benefit.table');
    }

    /**
     * @internal
     */
    public function toEntity(): BenefitEntity
    {
        return new BenefitEntity(
            $this->id,
            $this->name,
            $this->is_consumable,
            $this->is_quota,
            $this->periodicity,
        );
    }

    protected static function newFactory(): BenefitFactory
    {
        return BenefitFactory::new();
    }
}
