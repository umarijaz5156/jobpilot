<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuSettings extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $guarded = [];

    protected $appends = ['eligibility'];

    public $translatedAttributes = ['title'];

    protected $with = ['translations'];

    /**
     * Get the menu for the menu settings.
     *
     * @return void
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get the eligibility for the menu settings.
     *
     * @return string
     */
    public function getEligibilityAttribute()
    {
        $string = $this->for;
        $array = json_decode($string);
        $for = implode(', ', $array);

        return ucwords($for);
    }
}
