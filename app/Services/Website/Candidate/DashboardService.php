<?php

namespace App\Services\Website\Candidate;

use App\Http\Resources\Job\JobListResource;
// use App\Http\Resources\JobListResource;
use App\Models\Candidate;

class DashboardService
{
    public function execute($is_api = false)
    {
        $candidate = Candidate::where('user_id', auth()->id())->first();

        if (empty($candidate)) {
            $candidate = new Candidate();
            $candidate->user_id = auth()->id();
            $candidate->save();
        }

        $appliedJobs = $candidate->appliedJobs->count();
        $favoriteJobs = $candidate->bookmarkJobs->count();
        $jobs = $candidate->appliedJobs()->withCount(['bookmarkJobs as bookmarked' => function ($q) use ($candidate) {
            $q->where('candidate_id',  $candidate->id);
        }])
            ->latest()
            ->limit(4)
            ->get(['id', 'company_id', 'title', 'slug', 'role_id', 'job_type_id', 'country','salary_mode','min_salary','max_salary','custom_salary','deadline_active']);
        $notifications = auth($is_api ? 'api':'user')->user()->notifications()->count();

        return [
            'appliedJobs' => $appliedJobs,
            'favoriteJobs' => $favoriteJobs,
            'notifications' => $notifications,
            'jobs' => JobListResource::collection($jobs),
            'candidate' => $is_api ? '':$candidate,
        ];
    }
}
