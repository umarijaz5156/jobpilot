<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['example_image_url'];

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            forgetCache('advertisements');
        });

        self::updated(function ($model) {
            forgetCache('advertisements');
        });

        self::deleted(function ($model) {
            forgetCache('advertisements');
        });

    }

    public function getExampleImageUrlAttribute()
    {
        $image = $this->place_example_image;

        return asset($image);
    }
}
