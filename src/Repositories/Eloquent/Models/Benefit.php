<?php

namespace OpenSaaS\Subify\Repositories\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\BenefitFactory;
use OpenSaaS\Subify\Enums\PeriodicityUnit;

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

    protected static function newFactory(): BenefitFactory
    {
        return BenefitFactory::new();
    }
}
