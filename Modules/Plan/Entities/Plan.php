<?php

namespace Modules\Plan\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Plan\Database\factories\PriceplanFactory;

class Plan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'frontend_show' => 'boolean',
        'recommended' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('frontend_show', true);
    }

    protected static function newFactory()
    {
        return PriceplanFactory::new();
    }

    public function descriptions()
    {
        return $this->hasMany(PlanDescription::class, 'plan_id');
    }
}
