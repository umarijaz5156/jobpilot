<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Plan\Entities\Plan;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Company extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $guarded = [];

    protected $appends = ['logo_url', 'banner_url', 'full_address'];

    protected $casts = [
        'establishment_date' => 'datetime',
        'profile_completion' => 'boolean',
        'is_profile_verified' => 'boolean',
    ];

    protected static function booted()
    {
        static::created(function ($company) {
            $company->applicationGroups()->createMany([
                [
                    'name' => 'No Group',
                    'order' => 1,
                    'is_deleteable' => false,
                ],
                [
                    'name' => 'All Applications',
                    'order' => 1,
                ],
                [
                    'name' => 'Shortlisted',
                    'order' => 2,
                ],
                [
                    'name' => 'Interview',
                    'order' => 3,
                ],
                [
                    'name' => 'Rejected',
                    'order' => 4,
                ],
            ]);
        });
    }

    /**
     * Get the company full address
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
     * Get the company logo
     *
     * @param  string  $logo
     * @return string
     */
    public function getLogoUrlAttribute()
    {
        if (! $this->logo) {
            return asset('backend/image/default.png');
        }

        return asset($this->logo);
    }

    /**
     * Get the company banner
     *
     * @param  string  $banner
     * @return string
     */
    public function getBannerUrlAttribute()
    {
        if (! $this->banner) {
            return asset('backend/image/default.png');
        }

        return asset($this->banner);
    }

    /**
     * Get the active company
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('visibility', 1)->whereHas('user', function ($q) {
            $q->whereStatus(1);
        });
    }

    /**
     * Get the inactive company
     *
     * @return mixed
     */
    public function scopeInactive($query)
    {
        return $query->where('visibility', 0)->whereHas('user', function ($q) {
            $q->whereStatus(0);
        });
    }

    /**
     * Get the company user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company jobs
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Get the company bookmarked candidates
     *
     * @return HasMany
     */
    public function bookmarkCandidates()
    {
        return $this->belongsToMany(Candidate::class, 'bookmark_company')->with('user')->withPivot('category_id')->withTimestamps();
    }

    /**
     * Get the company bookmarked categories
     *
     * @return HasMany
     */
    public function category(): HasOne
    {
        return $this->hasOne(CompanyBookmarkCategory::class, 'company_id');
    }

    /**
     * Get the company bookmarked candidates
     *
     * @return BelongsToMany
     */
    public function bookmarkCandidateCompany()
    {
        return $this->belongsToMany(Candidate::class, 'bookmark_candidate_company');
    }

    /**
     * Get the company organization
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class, 'organization_type_id');
    }

    /**
     * Get the company industry
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(IndustryType::class, 'industry_type_id');
    }

    /**
     * User Pricing Plan
     */
    public function userPlan(): HasOne
    {
        return $this->hasOne(UserPlan::class, 'company_id');
    }

    /**
     * User Transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Earning::class);
    }

    /**
     * Get the company team size
     *
     * @return BelongsTo
     */
    public function team_size()
    {
        return $this->belongsTo(TeamSize::class, 'team_size_id', 'id');
    }

    /**
     * Get the company application groups
     *
     * @return BelongsTo
     */
    public function applicationGroups()
    {
        return $this->hasMany(ApplicationGroup::class, 'company_id');
    }

    /**
     * Get the company cv views
     *
     * @return HasMany
     */
    public function cv_views()
    {
        return $this->hasMany(CandidateCvView::class, 'company_id');
    }

    public function questions()
    {
        return $this->hasMany(CompanyQuestion::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('document')->singleFile();

    }

    public function getLogoUrl()
    {
        if ($this->logo_url && file_exists(public_path($this->logo_url))) {
            return asset($this->logo_url);
        } else {
            return asset('backend/image/default.png');
        }
    }
}
