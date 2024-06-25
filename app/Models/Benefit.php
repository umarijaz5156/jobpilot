<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benefit extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $hidden = ['pivot'];

    public $translatedAttributes = ['name'];

    protected $with = ['translations'];

    /**
     * Get the jobs for the benefit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function job_benefit()
    {
        return $this->belongsToMany(Job::class, 'job_benefit');
    }
}
