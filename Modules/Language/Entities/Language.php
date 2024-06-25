<?php

namespace Modules\Language\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'icon', 'direction'];

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            forgetCache('default_language');
            forgetCache('languages');
        });

        self::updated(function ($model) {
            forgetCache('default_language');
            forgetCache('languages');
        });

        self::deleted(function ($model) {
            forgetCache('default_language');
            forgetCache('languages');
        });
    }
}
