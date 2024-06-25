<?php

namespace App\Services\API\Website\Candidate;

use App\Models\Skill;
use App\Models\JobRole;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profession;
use F9Web\ApiResponseHelpers;
use App\Enums\SocialMediaEnum;
use App\Models\CandidateLanguage;
use Carbon\Carbon;

class FetchCandidateSettingService
{
    use ApiResponseHelpers;

    public function execute($request){
        $candidate_user = auth('sanctum')->user();
        $candidate = $candidate_user->candidate;

        if ($request->type == 'personal') {
            return $this->getPersonalInfo($candidate_user, $candidate);
        }elseif ($request->type == 'profile') {
            return $this->getProfileInfo($candidate);
        }elseif ($request->type == 'social') {
            return $this->getSocialInfo($candidate_user);
        }elseif ($request->type == 'contact') {
            return $this->getContactInfo($candidate_user, $candidate);
        }
    }

    protected function getPersonalInfo($candidate_user, $candidate){
        return $this->respondWithSuccess([
            'data' => [
                'image_url' => $candidate_user->image_url,
                'name' => $candidate_user->name,
                'title' => $candidate->title,
                'education_id' => (int) $candidate->education_id,
                'experience_id' => (int) $candidate->experience_id,
                'website' => $candidate->website,
                'date_of_birth' => formatTime($candidate->birth_date, 'Y-m-d'),
                'experience_list' => Experience::all()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                }),
                'education_list' => Education::all()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                }),
            ]
        ]);
    }

    protected function getProfileInfo($candidate){
        return $this->respondWithSuccess([
           'data' => [
                'gender' => $candidate->gender,
                'marital_status' => $candidate->marital_status,
                'profession_id' => (int) $candidate->profession_id,
                'bio' => $candidate->bio,
                'availability' => $candidate->status,
                'available_in' => $candidate->available_in,
                'skills' => $candidate->skills->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                }),
                'languages' => $candidate->languages->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                }),
                'profession_list' => Profession::all()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                }),
                'skill_list' => Skill::all()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                }),
                'language_list' => CandidateLanguage::all()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                }),
           ]
        ]);
    }

    protected function getSocialInfo($candidate_user){
        return $this->respondWithSuccess([
            'data' => [
                'social_media' => $candidate_user->socialInfo->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'social_media' => $item->social_media,
                        'url' => $item->url,
                    ];
                }),
                'social_media_list' => SocialMediaEnum::toArray(),
            ],
        ]);
    }

    protected function getContactInfo($candidate_user, $candidate){
        return $this->respondWithSuccess([
            'data' => [
                'contact_info' => [
                    'phone' => $candidate_user->contactInfo->phone,
                    'secondary_phone' => $candidate_user->contactInfo->secondary_phone,
                    'whatsapp_no' => $candidate->whatsapp_number,
                    'secondary_email' => $candidate_user->contactInfo->email,
                ],
                'location' => [
                    'country' => $candidate->country,
                    'city' => $candidate->city,
                    'address' => $candidate->address,
                    'latitude' => $candidate->lat,
                    'longitude' => $candidate->long,
                ],
                'job_alerts' => $candidate->jobRoleAlerts,
                'job_alert_role_list' => JobRole::all()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                }),
            ],
        ]);
    }
}
