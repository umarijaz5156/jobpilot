<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'search_engine_indexing' => 'boolean',
        'google_analytics' => 'boolean',
    ];

    protected $appends = ['dark_logo_url', 'light_logo_url', 'favicon_image_url', 'app_pwa_icon_url'];

    public static function boot()
    {
        parent::boot();

        self::updated(function ($model) {
            forgetCache('setting');
        });
    }

    /**
     * Get the dark logo url.
     *
     * @return string
     */
    public function getDarkLogoUrlAttribute()
    {
        if (is_null($this->dark_logo)) {
            return asset('frontend/assets/images/logo/logo.svg');
        }

        return asset($this->dark_logo);
    }

    /**
     * Get the light logo url.
     *
     * @return string
     */
    public function getLightLogoUrlAttribute()
    {
        if (is_null($this->light_logo)) {
            return asset('frontend/assets/images/logo/light_logo.svg');
        }

        return asset($this->light_logo);
    }

    /**
     * Get the PWA logo url.
     *
     * @return string
     */
    public function getAppPwaIconUrlAttribute()
    {
        if (is_null($this->app_pwa_icon)) {
            return asset('/logo.png');
        }

        return asset($this->app_pwa_icon);
    }

    /**
     * Get the favicon image url.
     *
     * @return string
     */
    public function getFaviconImageUrlAttribute()
    {
        if (is_null($this->favicon_image)) {
            return asset('frontend/assets/images/logo/fav.png');
        }

        return asset($this->favicon_image);
    }
}
