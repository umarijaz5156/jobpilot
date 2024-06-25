<?php

namespace App\Services\API\Website\Company\PostingJob;

use App\Models\Job;
use App\Models\Setting;
use F9Web\ApiResponseHelpers;

class CloneJobService
{
    use ApiResponseHelpers;

    public function execute($request){
        $job = Job::whereSlug($request->slug)->first();

        if (!$job) {
            return $this->respondNotFound(__('job_not_found'));
        }

        $user = auth('sanctum')->user();
        $user_plan = $user->company->userPlan;

        if (!$user_plan->job_limit) {
            session()->flash('error', __('you_have_reached_your_plan_limit_please_upgrade_your_plan'));
            return redirect()->route('company.plan');
        }

        $newJob = $job->replicate();
        $newJob->created_at = now();

        if ($job->featured && $user_plan->featured_job_limit) {
            $newJob->featured = 1;
            $user_plan->featured_job_limit = $user_plan->featured_job_limit - 1;
        } else {
            $newJob->featured = 0;
        }

        if ($job->highlight && $user_plan->highlight_job_limit) {
            $newJob->highlight = 1;
            $user_plan->highlight_job_limit = $user_plan->highlight_job_limit - 1;
        } else {
            $newJob->highlight = 0;
        }

        $newJob->save();
        $user_plan->job_limit = $user_plan->job_limit - 1;
        $user_plan->save();

        storePlanInformation();

        return $this->respondWithSuccess([
            'data' => [
                'data' => $newJob,
                'message' => __('job_cloned_successfully'),
            ]
        ]);
    }
}
