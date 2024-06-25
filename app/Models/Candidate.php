<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['full_address'];

    protected $casts = [
        'date_of_birth' => 'datetime',
        'allow_in_search' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! app()->runningInConsole()) {
                // $model->photo = createAvatar($model->user->name, 'uploads/images/candidate');
            }
        });
    }

    /**
     * Get the candidate photo
     *
     * @param  string  $photo
     * @return string
     */
    public function getPhotoAttribute($photo)
    {
        if ($photo == null) {
            return asset('backend/image/default.png');
        } else {
            return asset($photo);
        }
    }

    /**
     * Get the full address of the candidate
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $country = $this->country;
        $region = $this->region;
        $extra = $region != null ? ' , ' : '';

        return $region.$extra.$country;
    }

    /**
     * Get the candidate cv
     *
     * @param  string  $cv
     * @return string
     */
    public function getCvUrlAttribute($photo)
    {
        if ($this->cv == null) {
            return '';
        } else {
            return route('website.candidate.download.cv', $this->id);
        }
    }

    /**
     * Get the active candidate scope
     *
     * @param  mixed  $query
     */
    public function scopeActive($query)
    {
        return $query->where('visibility', 1)->whereHas('user', function ($q) {
            $q->whereStatus(1);
        });
    }

    /**
     * Get the inactive candidate scope
     *
     * @param  mixed  $query
     */
    public function scopeInactive($query)
    {
        return $query->where('visibility', 0)->whereHas('user', function ($q) {
            $q->whereStatus(0);
        });
    }

    /**
     * Get the candidate user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the candidate bookmark jobs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bookmarkJobs()
    {
        return $this->belongsToMany(Job::class, 'bookmark_candidate_job')->with('company', 'category', 'job_type:id');
    }

    /**
     * Get the candidate bookmark companies
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bookmarkCompanies()
    {
        return $this->belongsToMany(Company::class, 'bookmark_candidate_company');
    }

    /**
     * Get the candidate bookmark companies
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bookmarkCandidates()
    {
        return $this->belongsToMany(Company::class, 'bookmark_company')->withTimestamps();
    }

    /**
     * Get the candidate applied jobs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function appliedJobs()
    {
        return $this->belongsToMany(Job::class, 'applied_jobs')->with('company', 'job_type:id')->withTimestamps();
    }

    /**
     * Get the candidate job alerts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobRole()
    {
        return $this->belongsTo(JobRole::class, 'role_id');
    }

    /**
     * Get the candidate experience
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function experience()
    {
        return $this->belongsTo(Experience::class, 'experience_id');
    }

    /**
     * Get the candidate education
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function education()
    {
        return $this->belongsTo(Education::class, 'education_id');
    }

    /**
     * Get the candidate profession
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession_id');
    }

    /**
     * Get the candidate resumes
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resumes()
    {
        return $this->hasMany(CandidateResume::class, 'candidate_id');
    }

    /**
     * Get the candidate skills
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'candidate_skill');
    }

    /**
     * Get the candidate languages
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages()
    {
        return $this->belongsToMany(CandidateLanguage::class, 'candidate_language');
    }

    /**
     * Get the candidate experiences
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function experiences()
    {
        return $this->hasMany(CandidateExperience::class, 'candidate_id');
    }

    /**
     * Get the candidate educations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function educations()
    {
        return $this->hasMany(CandidateEducation::class, 'candidate_id');
    }

    /**
     * Get the candidate cover letter
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coverLetter()
    {
        return $this->hasOne(AppliedJob::class);
    }

    /**
     * Get the candidate social information
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function socialInfo(): HasMany
    {
        return $this->hasMany(SocialLink::class, 'user_id');
    }

    /**
     * Get the candidate already viewed jobs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function already_views()
    {
        return $this->hasMany(CandidateCvView::class, 'candidate_id', 'id');
    }

    /**
     * Get the candidate job alerts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobRoleAlerts()
    {
        return $this->hasMany(CandidateJobAlert::class, 'candidate_id', 'id');
    }
}
