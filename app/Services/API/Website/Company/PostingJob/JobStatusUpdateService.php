<?php

namespace App\Services\API\Website\Company\PostingJob;

use App\Models\Job;
use App\Models\Setting;
use F9Web\ApiResponseHelpers;

class JobStatusUpdateService
{
    use ApiResponseHelpers;

    public function execute($request){
        // return $request->all();
        $job = Job::whereSlug($request->slug)->first();

        if (!$job) {
            return $this->respondNotFound(__('job_not_found'));
        }

        // if ($job->status == 'active' || $job->status == 'expire') {
        // if ($job->status == 'expired') {
        //     return $this->respondForbidden(__('invalid_job_status'));
        // }

        $job->update(['status' => $request->status]);

        return $this->respondOk(__('job_status_updated_successfully'));
    }
}
