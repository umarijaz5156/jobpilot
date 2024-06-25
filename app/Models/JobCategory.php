<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $guarded = [];

    protected $appends = ['image_url', 'open_jobs_count'];

    public $translatedAttributes = ['name'];

    protected $with = ['translations'];

    /**
     * Get the image url for the job category.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (is_null($this->image)) {
            return asset('backend/image/default.png');
        }

        return asset($this->image);
    }

    /**
     * Get the open positions jobs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getOpenJobsCountAttribute()
    {
        return $this->jobs()
            ->where('status', 'active')
            ->where('deadline', '>=', Carbon::now()->toDateString())
            ->count();
    }

    /**
     * Get the jobs for the job category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'category_id');
    }
}
