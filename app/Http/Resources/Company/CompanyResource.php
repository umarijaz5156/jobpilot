<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this->user;

        if (!$user) {
            return []; // or handle the null case as needed
        }
        return [
            "id" => $this->id,
            "username" => $this->user->username,
            "email" => $this->user->email,
            "role" => $this->user->role,
            "name" => $this->user->name,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "role_id" => $this->role_id,
            "profession_id" => $this->profession_id,
            "experience_id" => $this->experience_id,
            "education_id" => $this->education_id,
            "title" => $this->title,
            "gender" => $this->gender,
            "website" => $this->website,
            "cv" => $this->cv,
            "bio" => $this->bio,
            "marital_status" => $this->marital_status,
            "birth_date" => $this->birth_date,
            "birth_date" => $this->birth_date,
            "cv_visibility" => $this->cv_visibility,
            "profile_complete" => $this->profile_complete,
            "address" => $this->address,
            "neighborhood" => $this->neighborhood,
            "locality" => $this->locality,
            "place" => $this->place,
            "district" => $this->district,
            "postcode" => $this->postcode,
            "region" => $this->region,
            "country" => $this->country,
            "photo_url" => $this->logo_url,
            "profile_complete" => $this->profile_complete,
            "user_plan" => $this->userPlan,
            "social" => $user->socialInfo->map(function($item){
                return [
                    "social_media" => $item->social_media,
                    "url" => $item->url,
                ];
            }),
            "contactinfo" => $user->contactInfo->makeHidden('id', 'user_id', 'created_at', 'updated_at'),
            
        ];
    }
}
