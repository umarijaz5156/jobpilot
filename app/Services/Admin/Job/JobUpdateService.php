<?php

namespace App\Services\Admin\Job;

use App\Http\Traits\JobAble;
use App\Models\Job;
use Carbon\Carbon;

class JobUpdateService
{
    use JobAble;

    /**
     * Update job
     *
     * @return Job $job
     */
    public function execute($request, $job): Job
    {
        $highlight = $request->badge == 'highlight' ? 1 : 0;
        $featured = $request->badge == 'featured' ? 1 : 0;

        // Job title update
        $job->title = $request->title;
        $title_changed = $job->isDirty('title');
        if ($title_changed) {
            $job->update(['title' => $request->title]);
        }
        $companyId = null;
        $companyName = null;

        if ($request->has('is_just_name')) {
            // he wants to update just name
            $companyName = $request->get('company_name');
        } else {
            $companyId = $request->get('company_id');
        }

        //job status update
        if ($request->deadline !== now()->format('Y-m-d') || $job->where('status', 'expired')->first()) {
            $status = 'active';
        }
        if ($request->deadline == now()->format('Y-m-d')) {
            $status = 'expired';
        }

        $job->update([
            'company_id' => $companyId,
            'company_name' => $companyName,
            'category_id' => $request->category_id,
            'role_id' => $request->role_id,
            'salary_mode' => $request->salary_mode,
            'custom_salary' => $request->custom_salary,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'salary_type_id' => $request->salary_type,
            'deadline' => Carbon::parse($request->deadline)->format('Y-m-d'),
            'education_id' => $request->education,
            'experience_id' => $request->experience,
            'job_type_id' => $request->job_type,
            'vacancies' => $request->vacancies,
            'apply_on' => $request->apply_on,
            'apply_email' => $request->apply_email ?? null,
            'apply_url' => $request->apply_url ?? null,
            'description' => $request->description,
            'featured' => $featured,
            'highlight' => $highlight,
            'is_remote' => $request->is_remote ?? 0,
            'status' => $status,
        ]);

        // Benefits
        $this->jobBenefitsSync($request->benefits, $job);

        // Tags
        $this->jobTagsSync($request->tags, $job);

        // skills
        $skills = $request->skills ?? null;
        if ($skills) {
            $this->jobSkillsSync($request->skills, $job);
        }

        // location
        updateMap($job);

        return $job;
    }
}
