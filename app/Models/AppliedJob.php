<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppliedJob extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'answers' => 'array',
    ];

    /**
     * Get the job that owns the AppliedJob
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job()
    {

        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    /**
     * Get the candidate that owns the AppliedJob
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function candidate()
    {

        return $this->belongsTo(Candidate::class, 'candidate_id', 'id')->withCount(['bookmarkCandidates as bookmarked' => function ($q) {
            $q->where('company_id', currentCompany()->id);
        }])
            ->withCasts(['bookmarked' => 'boolean']);
    }

    public function appliedcandidate()
    {

        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    /**
     * Get the resume that owns the AppliedJob
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resume()
    {
        return $this->belongsTo(CandidateResume::class, 'candidate_resume_id');
    }

    /**
     * Get the company that owns the Application Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function applicationGroup()
    {
        return $this->belongsTo(ApplicationGroup::class, 'application_group_id');
    }
}
