<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\BenefitFactory;
use OpenSaaS\Subify\Enums\PeriodicityUnit;

/**
 * @property int $id
 * @property string $name
 * @property bool $is_consumable
 * @property bool $is_quota
 * @property int $periodicity
 * @property PeriodicityUnit $periodicity_unit
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 */
class Benefit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'periodicity_unit' => PeriodicityUnit::class,
    ];

    protected $fillable = [
        'name',
        'is_consumable',
        'is_quota',
        'periodicity',
        'periodicity_unit',
    ];

    public function getTable(): string
    {
        return config('subify.repositories.eloquent.benefit.table');
    }

    protected static function newFactory(): BenefitFactory
    {
        return BenefitFactory::new();
    }
}
