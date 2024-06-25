<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'company',
        'department',
        'start',
        'end',
        'designation',
        'responsibilities',
        'currently_working',
    ];

    protected $appends = ['formatted_start', 'formatted_end'];

    public function getFormattedStartAttribute()
    {
        return formatTime($this->start, 'd M Y');
    }

    public function getFormattedEndAttribute()
    {
        return formatTime($this->end, 'd M Y');
    }
}
