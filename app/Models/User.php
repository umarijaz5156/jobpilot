<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Blog\Entities\PostComment;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $guarded = [];

    protected $appends = ['image_url'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($user) {
            if (! $user->is_demo_field) {
                if ($user->role == 'company') {
                    if (! setting('employer_auto_activation')) {
                        $user->update(['status' => 0]);
                    }

                    $user->company()->create([
                        'industry_type_id' => IndustryType::first()->id,
                        'organization_type_id' => OrganizationType::first()->id,
                        'team_size_id' => TeamSize::first()->id,
                    ]);
                } else {
                    if (! setting('candidate_account_auto_activation')) {
                        $user->update(['status' => 0]);
                    }

                    $user->candidate()->create([
                        'role_id' => JobRole::first()->id,
                        'profession_id' => Profession::first()->id,
                        'experience_id' => Experience::first()->id,
                        'education_id' => Education::first()->id,
                    ]);
                }
                $user->contactInfo()->create([
                    'phone' => '',
                    'secondary_phone' => '',
                    'email' => '',
                    'secondary_email' => '',
                ]);
            }
        });
    }

    public function getImageAttributes($image)
    {
        if ($image) {
            return $image;
        } else {
            return 'backend/image/default.png';
        }
    }

    public function getImageUrlAttribute()
    {
        if (! $this->image) {
            return asset('backend/image/default.png');
        }

        return asset($this->image);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function candidate(): HasOne
    {
        return $this->hasOne(Candidate::class)->withCount('bookmarkCandidates');
    }

    public function contactInfo(): HasOne
    {
        return $this->hasOne(ContactInfo::class, 'user_id');
    }

    public function socialInfo(): HasMany
    {
        return $this->hasMany(SocialLink::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'author_id', 'id');
    }

    public function companyId()
    {
        return $this->company->id;
    }

    public function candidateId()
    {
        return $this->candidate->id;
    }

    /**
     * User Pricing Plan
     */
    public function userPlan(): HasOne
    {
        return $this->hasOne(UserPlan::class, 'company_id');
    }

    /**
     * Get the user's application groups.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function application_groups()
    {
        return $this->belongsToMany(ApplicationGroup::class, 'application_group_user', 'application_group_id', 'user_id')->withTimestamps();
    }

    /**
     * Get the user's verification codes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function verificationCodes()
    {
        return $this->hasMany(VerificationCode::class, 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Messenger::class, 'from');
    }
}
