<?php

namespace App\Services\Admin\Job;

use App\Models\Job;

class JobListService
{
    /**
     * Get job list
     */
    public function execute($request): mixed
    {
        $query = Job::query()->with('role', 'category', 'salary_type', 'company');

        // keyword
        if ($request->title && $request->title != null) {
            $query->where('title', 'LIKE', "%$request->title%");
        }

        // status
        if ($request->status && $request->status != null) {
            if ($request->status != 'all') {
                $query->where('status', $request->status);
            }
        }

        // job_category
        if ($request->job_category && $request->job_category != null) {
            $query->where('category_id', $request->job_category);
        }

        // experience
        if ($request->experience && $request->experience != null) {
            $query->whereHas('experience', function ($q) use ($request) {
                $q->where('slug', $request->experience);
            });
        }

        // job_type
        if ($request->job_type && $request->job_type != null) {
            $query->whereHas('job_type', function ($q) use ($request) {
                $q->where('slug', $request->job_type);
            });
        }

        // filter_by
        if ($request->filter_by && $request->filter_by != null) {
            $query->where('status', $request->filter_by);
        }

        $jobs = $query->withoutEdited()->with(['experience', 'job_type', 'category'])->latest()->paginate(15);
        $jobs->appends($request->all());

        return $jobs;
    }
}
