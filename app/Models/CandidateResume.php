<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateResume extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'file_size',
        'file_url',
    ];

    public function getFileSizeAttribute()
    {
        return get_file_size($this->file);
    }

    // make full url for resume view
    public function getFileUrlAttribute()
    {
        $file = $this->file;

        return asset($file);
    }
}
