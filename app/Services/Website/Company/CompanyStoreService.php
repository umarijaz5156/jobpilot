<?php

namespace App\Services\Website\Company;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\Admin;
use App\Models\JobRole;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobCategory;
use App\Http\Traits\JobAble;
use App\Models\CandidateJobAlert;
use App\Models\JobRoleTranslation;
use App\Models\JobCategoryTranslation;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Admin\NewJobAvailableNotification;
use App\Notifications\Website\Company\JobCreatedNotification;
use App\Notifications\Website\Candidate\RelatedJobNotification;

class CompanyStoreService
{
    use JobAble;

    /**
     * Store job
     *
     * @return Job $jobCreated
     */
    public function execute($request): Job
    {
        // Check if user has reached the job limit
        storePlanInformation();
        $userPlan = session('user_plan');

        if ((int) $userPlan->job_limit < 1) {
            session()->flash('error', __('you_have_reached_your_plan_limit_please_upgrade_your_plan'));

            return redirect()->route('company.plan');
        }

        $min = $request->min_salary;
        $max = $request->max_salary;

        $request->validate([
            'min_salary' => 'nullable|numeric|between:0,'.$max,
            'max_salary' => 'nullable|numeric|min:'.$min,
        ]);

        if ($request->apply_on === 'custom_url') {
            $request->validate([
                'apply_url' => 'required|url',
            ]);
        }
        if ($request->apply_on === 'email') {
            $request->validate([
                'apply_email' => 'required|email',
            ]);
        }

        // Highlight & featured
        $highlight = $request->badge == 'highlight' ? 1 : 0;
        $featured = $request->badge == 'featured' ? 1 : 0;

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

        $deadline = Carbon::parse(now()->addDays(setting('job_deadline_expiration_limit')))->format('Y-m-d');

        $jobCreated = Job::create([
            'title' => $request->title,
            'company_id' => currentCompany()->id,
            'category_id' => $job_category_id,
            'role_id' => $job_role_id,
            'education_id' => $request->education,
            'experience_id' => $request->experience,
            'salary_mode' => $request->salary_mode,
            'custom_salary' => $request->custom_salary,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'salary_type_id' => $request->salary_type,
            'deadline' => $deadline,
            'job_type_id' => $request->job_type,
            'vacancies' => $request->vacancies,
            'apply_on' => $request->apply_on,
            'apply_email' => $request->apply_email ?? null,
            'apply_url' => $request->apply_url ?? null,
            'description' => $request->description,
            'featured' => $featured,
            'highlight' => $highlight,
            'is_remote' => $request->is_remote ?? 0,
            'status' => setting('job_auto_approved') ? 'active' : 'pending',
        ]);

        // Location
        updateMap($jobCreated);

        // Question
        if (isset($request->companyQuestions) && $request->has('companyQuestions')) {
            $jobCreated->questions()->attach($request->get('companyQuestions'));
        }

        // Benefits
        $benefits = $request->benefits ?? null;
        if ($benefits) {
            $this->jobBenefitsInsert($request->benefits, $jobCreated);
        }

        // Tags
        $tags = $request->tags ?? null;
        if ($tags) {
            $this->jobTagsInsert($request->tags, $jobCreated);
        }

        // skills
        $skills = $request->skills ?? null;
        if ($skills) {
            $this->jobSkillsInsert($request->skills, $jobCreated);
        }

        if ($jobCreated) {
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

            Notification::send(authUser(), new JobCreatedNotification($jobCreated));

            if ($jobCreated->status == 'active') {
                $candidates = CandidateJobAlert::where('job_role_id', $jobCreated->role_id)->get();

                foreach ($candidates as $candidate) {
                    if ($candidate->candidate->received_job_alert) {
                        $candidate->candidate->user->notify(new RelatedJobNotification($jobCreated));
                    }
                }
            }

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
