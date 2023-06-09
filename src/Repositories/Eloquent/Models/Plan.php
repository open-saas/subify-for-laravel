<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenSaaS\Subify\Database\Factories\PlanFactory;
use OpenSaaS\Subify\Entities\Plan as PlanEntity;

/**
 * @property int                                                  $id
 * @property string                                               $name
 * @property \Illuminate\Support\Carbon                           $created_at
 * @property \Illuminate\Support\Carbon                           $updated_at
 * @property \Illuminate\Support\Carbon                           $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection<PlanRegime> $regimes
 */
class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function regimes(): HasMany
    {
        return $this->hasMany(PlanRegime::class);
    }

    public function getTable(): string
    {
        return config('subify.repositories.eloquent.plan.table');
    }

    /**
     * @internal
     */
    public function toEntity(): PlanEntity
    {
        return new PlanEntity(
            $this->id,
            $this->name,
        );
    }

    protected static function newFactory(): PlanFactory
    {
        return PlanFactory::new();
    }
}
