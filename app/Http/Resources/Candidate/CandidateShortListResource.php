<?php

namespace App\Http\Resources\Candidate;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidateShortListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "experience" => $this->experience?->name,
            "profession" => $this->profession?->name,
            "username" => $this->user?->username,
            "email" => $this->user?->email,
            "role" => $this->user?->role,
            "name" => $this->user?->name,
            "title" => $this->title,
            "country" => $this->country,
            "photo_url" => $this->photo,
            'bookmarked' => $this->bookmarked ? true : false,
        ];
    }
}
