<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateJobAlert extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function jobRole()
    {
        return $this->belongsTo(JobRole::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
