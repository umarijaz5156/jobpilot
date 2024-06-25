<?php

namespace Modules\Location\Entities;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\Location\Database\factories\CountryFactory::new();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
        $this->attributes['slug'] = \Str::slug($value);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'country_id', 'id');
    }
}
