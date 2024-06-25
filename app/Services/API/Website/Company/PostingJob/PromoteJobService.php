<?php

namespace App\Services\API\Website\Company\PostingJob;

use App\Models\Job;
use App\Models\Setting;
use F9Web\ApiResponseHelpers;

class PromoteJobService
{
    use ApiResponseHelpers;

    public function execute($request){
        $jobCreated = Job::whereSlug($request->slug)->first();

        if (!$jobCreated) {
            return $this->respondNotFound(__('job_not_found'));
        }

        $user_plan = auth('sanctum')->user()->company->userplan;

        if (!auth('sanctum')->check() || auth('sanctum')->user()->role != 'company' || !$user_plan) {
            return $this->respondError(__('you_are_not_authorized_to_perform_this_action'));
        }

        $setting = Setting::first();

        if ($request->badge == 'featured') {
            if ($user_plan->featured_job_limit) {
                $user_plan->featured_job_limit = $user_plan->featured_job_limit - 1;
                $user_plan->save();
            } else {
                return $this->respondError(__('you_have_no_featured_job_limit'));
            }

            $featured_days = $setting->featured_job_days > 0 ? now()->addDays($setting->featured_job_days)->format('Y-m-d'):null;

            $jobCreated->update([
                'featured' => 1,
                'highlight' => 0,
                'featured_until' => $featured_days,
                'highlight_until' => null,
            ]);
        } else {
            if ($user_plan->highlight_job_limit) {
                $user_plan->highlight_job_limit = $user_plan->highlight_job_limit - 1;
                $user_plan->save();
            } else {
                return $this->respondError(__('you_have_no_highlight_job_limit'));
            }

            $highlight_days = $setting->highlight_job_days > 0 ? now()->addDays($setting->highlight_job_days)->format('Y-m-d'):null;

            $jobCreated->update([
                'featured' => 0,
                'highlight' => 1,
                'highlight_until' => $highlight_days,
                'featured_until' => null,
            ]);
        }

        return $this->respondOk(__('job_promote_successfully'));
    }
}
