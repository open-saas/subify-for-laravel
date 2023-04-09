<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\PlanFactory;
use OpenSaaS\Subify\Entities\Plan as PlanEntity;

class Plan extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    protected static function newFactory(): PlanFactory
    {
        return PlanFactory::new();
    }

    public function toEntity(array $regimes = []): PlanEntity
    {
        $regimes = $this->relationLoaded('regimes')
            ? $this->regimes->map->toEntity()->toArray()
            : $regimes;

        return new PlanEntity(
            $this->id,
            $this->name,
            $regimes,
            $this->created_at,
            $this->updated_at,
        );
    }
}
