<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $hidden = ['pivot'];

    protected $guarded = [];

    public $translatedAttributes = ['name'];

    protected $with = ['translations'];

    /**
     * Get the jobs for the tag.
     *
     * @return void
     */
    public function tags()
    {
        return $this->belongsToMany(Job::class, 'job_tag');
    }

    /**
     * Show popular list in job page Scope Define
     *
     * @return void
     */
    public function scopePopular($query)
    {
        return $query->where('show_popular_list', 1);
    }

    /**
     * Get the jobs for the tag.
     *
     * @return void
     */
    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'job_tag');
    }
}
