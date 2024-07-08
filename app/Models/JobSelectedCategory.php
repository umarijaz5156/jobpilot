<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSelectedCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'job_selected_categories');
    }
}
