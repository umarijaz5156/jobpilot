<?php

namespace App\Services\Website\Job;

use App\Http\Traits\JobAble;
use App\Models\Admin;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobCategoryTranslation;
use App\Models\JobRole;
use App\Models\JobRoleTranslation;
use App\Notifications\Admin\NewJobAvailableNotification;
use App\Notifications\Website\Company\JobCreatedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class PayPerJobService
{
    use JobAble;

    /**
     * Store payper job
     *
     * @param  $request
     * @return Job $jobCreated
     */
    public function execute(): Job
    {
        $request = (object) session('job_request');
        // Highlight & featured
        $highlight = isset($request->badge) && $request->badge == 'highlight' ? 1 : 0;
        $featured = isset($request->badge) && $request->badge == 'featured' ? 1 : 0;

        $setting = loadSetting();
        $featured_days = isset($request->badge) && $setting->featured_job_days > 0 ? now()->addDays($setting->featured_job_days)->format('Y-m-d') : null;
        $highlight_days = isset($request->badge) && $setting->highlight_job_days > 0 ? now()->addDays($setting->highlight_job_days)->format('Y-m-d') : null;

        // Job Category
        $job_category_request = $request->category_id;

        $job_category = JobCategoryTranslation::where('job_category_id', $job_category_request)->orWhere('name', $job_category_request)->first();
        if (! $job_category) {
            $new_job_category = JobCategory::create(['name' => $job_category_request]);

            $languages = loadLanguage();
            foreach ($languages as $language) {
                $new_job_category->translateOrNew($language->code)->name = $job_category_request;
            }
            $new_job_category->save();

            $job_category_id = $new_job_category->id;
        } else {
            $job_category_id = $job_category->job_category_id;
        }

        // Job Role
        $job_role_request = $request->role_id;

        $job_category = JobRoleTranslation::where('job_role_id', $job_role_request)->orWhere('name', $job_role_request)->first();

        if (! $job_category) {
            $new_job_role = JobRole::create(['name' => $job_role_request]);

            $languages = loadLanguage();
            foreach ($languages as $language) {
                $new_job_role->translateOrNew($language->code)->name = $job_role_request;
            }
            $new_job_role->save();

            $job_role_id = $new_job_role->id;
        } else {
            $job_role_id = $job_category->job_role_id;
        }

        // Experience
        $education_request = $request->education;
        $education = Education::where('id', $education_request)->orWhere('name', $education_request)->first();
        if (! $education) {
            $education = Education::where('name', $education_request)->first();

            if (! $education) {
                $education = Education::create(['name' => $education_request]);
            }
        }

        // Education
        $experience_request = $request->experience;
        $experience = Experience::where('id', $experience_request)->orWhere('name', $experience_request)->first();
        if (! $experience) {
            $experience = Experience::where('name', $experience_request)->first();

            if (! $experience) {
                $experience = Experience::create(['name' => $experience_request]);
            }
        }

        $jobCreated = Job::create([
            'title' => $request->title,
            'company_id' => currentCompany()->id,
            'category_id' => $job_category_id,
            'role_id' => $job_role_id,
            'education_id' => $education->id,
            'experience_id' => $experience->id,
            'salary_mode' => $request->salary_mode,
            'custom_salary' => $request->custom_salary,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'salary_type_id' => $request->salary_type,
            'deadline' => Carbon::parse($request->deadline)->format('Y-m-d'),
            'job_type_id' => $request->job_type,
            'vacancies' => $request->vacancies,
            'apply_on' => $request->apply_on,
            'apply_email' => $request->apply_email ?? null,
            'apply_url' => $request->apply_url ?? null,
            'description' => $request->description,
            'featured' => $featured,
            'highlight' => $highlight,
            'featured_until' => $featured_days,
            'highlight_until' => $highlight_days,
            'is_remote' => $request->is_remote ?? 0,
            'status' => setting('job_auto_approved') ? 'active' : 'pending',
        ]);

        // Location
        updateMap($jobCreated);

        // Benefits
        $benefits = $request->benefits ?? null;
        if ($benefits) {
            $this->jobBenefitsInsert($request->benefits, $jobCreated);
        }

        //questions
        if (isset($request->companyQuestions)) {
            $jobCreated->questions()->attach($request->companyQuestions);
        }

        // Tags
        $tags = $request->tags ?? null;
        if ($tags) {
            $this->jobTagsInsert($request->tags, $jobCreated);
        }

        if ($jobCreated) {
            if (session('job_payment_type') != 'per_job') {
                $user_plan = currentCompany()->userPlan()->first();
                $user_plan->job_limit = $user_plan->job_limit - 1;

                if ($featured) {
                    $user_plan->featured_job_limit = $user_plan->featured_job_limit - 1;
                }
                if ($highlight) {
                    $user_plan->highlight_job_limit = $user_plan->highlight_job_limit - 1;
                }
                $user_plan->save();

                storePlanInformation();
            }

            Notification::send(authUser(), new JobCreatedNotification($jobCreated));

            if (checkMailConfig()) {
                // make notification to admins for approved
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    Notification::send($admin, new NewJobAvailableNotification($admin, $jobCreated));
                }
            }
        }

        return $jobCreated;
    }
}
