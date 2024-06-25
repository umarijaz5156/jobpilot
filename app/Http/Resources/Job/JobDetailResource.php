<?php

namespace App\Http\Resources\Job;

use Illuminate\Http\Resources\Json\JsonResource;

class JobDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $job = $request->job;
        $salary = $this->salary_mode == 'range' ? currencyAmountShort($this->min_salary) . ' - ' . currencyAmountShort($this->max_salary) . ' ' . currentCurrencyCode() : $this->custom_salary;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'vacancies' => $this->vacancies,
            'salary' => $salary,
            'min_salary' => $this->min_salary,
            'max_salary' => $this->max_salary,
            'salary_mode' => $this->salary_mode,
            'custom_salary' => $this->custom_salary,
            'job_type' => $this->job_type->name,
            'deadline' => $this->deadline,
            'description' => $this->description,
            'status' => $this->status,
            'apply_on' => $this->apply_on,
            'apply_email' => $this->apply_email,
            'apply_url' => $this->apply_url,
            'featured' => $this->featured,
            'featured_until' => $this->featured_until,
            'highlight' => $this->highlight,
            'highlight_until' => $this->highlight_until,
            'is_remote' => $this->is_remote,
            'bookmarked' => $this->bookmarked,
            'applied' => $this->applied,
            'days_remaining' => $this->days_remaining,
            'deadline_active' => $this->deadline_active,
            'posted_at' => $this->created_at,
            'can_apply' => $this->can_apply,
            'location' => $this->full_address,
            'benefits' => $this->benefits->pluck('name'),
            'tags' => $this->tags->pluck('name'),
            'role' => $this->role->name ?? null,
            'experience' => $this->experience->name ?? null,
            'education' => $this->education->name ?? null,
            'company' => [
                'name' => $this->company->user->name ?? null,
                'slug' => $this->company->user->username ?? null,
                'logo' => asset($this->company->logo),
                'website' => $this->company->website ?? null,
                'address' => $this->company->address ?? null,
                'contact_info' => $this->company->contact_info ?? null,
                'social_links' => $this->company->social_links ?? null,
            ]





            // 'job' => [
            //     'title' => $job->title,
            //     'slug' => $job->slug,
            // ],
            // 'related_jobs' => $this,
            // 'releated_jobs' => JobListResource::collection($request->related_jobs),
        ];
    }
}
