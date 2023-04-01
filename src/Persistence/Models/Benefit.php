<?php

namespace OpenSaaS\Subify\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenSaaS\Subify\Database\Factories\BenefitFactory;

class Benefit extends Model
{
    use HasFactory;
    use SoftDeletes;

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
