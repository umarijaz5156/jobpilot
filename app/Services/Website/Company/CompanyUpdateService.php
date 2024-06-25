<?php

namespace App\Services\Website\Company;

use App\Http\Traits\JobAble;
use App\Models\Admin;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobCategoryTranslation;
use App\Models\JobRole;
use App\Models\JobRoleTranslation;
use App\Notifications\Admin\NewEditedJobAvailableNotification;
use App\Notifications\Website\Company\EditApproveNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class CompanyUpdateService
{
    use JobAble;

    /**
     * Company update job
     *
     * @return Job $jobCreated
     */
    public function execute($request, $job): Job
    {
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

        $main_job = '';
        if (setting('edited_job_auto_approved') || $job->status == 'pending') {
            // Job title update
            $job->title = $request->title;
            $title_changed = $job->isDirty('title');
            if ($title_changed) {
                $job->update(['title' => $request->title]);
            }

            $job->update([
                'category_id' => $job_category_id,
                'role_id' => $job_role_id,
                'education_id' => $request->education,
                'experience_id' => $request->experience,
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
                'is_remote' => $request->is_remote ?? 0,
            ]);
            $main_job = $job;
        } else {
            $edited_exist = Job::where('parent_job_id', $job->id)->where('company_id', auth()->user()->company->id)->first();
            if ($edited_exist) {
                // Job title update
                $job->title = $request->title;
                $title_changed = $job->isDirty('title');
                if ($title_changed) {
                    $job->update(['title' => $request->title]);
                }

                $edited_exist->update([
                    'category_id' => $job_category_id,
                    'role_id' => $job_role_id,
                    'education_id' => $request->education,
                    'experience_id' => $request->experience,
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
                    'is_remote' => $request->is_remote ?? 0,
                    'waiting_for_edit_approval' => 1,
                    'status' => 'pending',
                ]);
                $main_job = $edited_exist;
            } else {
                $main_job = Job::create([
                    'title' => $request->title,
                    'category_id' => $job_category_id,
                    'role_id' => $job_role_id,
                    'education_id' => $request->education,
                    'experience_id' => $request->experience,
                    'salary_mode' => 'required',
                    'custom_salary' => 'required_if:salary_mode,==,custom',
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
                    'is_remote' => $request->is_remote ?? 0,
                    'company_id' => currentCompany()->id,
                    'parent_job_id' => $job->id,
                    'waiting_for_edit_approval' => 1,
                    'status' => 'pending',
                    // map deatils
                    'address' => $job->address,
                    'neighborhood' => $job->neighborhood,
                    'locality' => $job->locality,
                    'place' => $job->place,
                    'district' => $job->district,
                    'postcode' => $job->postcode,
                    'region' => $job->region,
                    'country' => $job->country,
                    'long' => $job->long,
                    'lat' => $job->lat,
                ]);
            }
        }

        // Benefits
        $this->jobBenefitsSync($request->benefits, $main_job);

        // Tags
        $this->jobTagsSync($request->tags, $main_job);

        // skills
        $skills = $request->skills ?? null;
        if ($skills) {
            $this->jobSkillsSync($request->skills, $main_job);
        }

        // Location
        $location = session()->get('location');
        if ($location) {
            updateMap($main_job);
        }

        // Question
        if (isset($request->companyQuestions) && $request->has('companyQuestions')) {
            $job->questions()->sync($request->get('companyQuestions'));
        }

        if (setting('edited_job_auto_approved')) {
            flashSuccess(__('job_updated_successfully'));
        } else {
            if ($main_job->waiting_for_edit_approval) {
                Notification::send(authUser(), new EditApproveNotification($main_job));

                if (checkMailConfig()) {
                    // make notification to admins for approved
                    $admins = Admin::all();
                    foreach ($admins as $admin) {
                        Notification::send($admin, new NewEditedJobAvailableNotification($admin, $main_job));
                    }
                }
                flashSuccess(__('your_job_successfully_updated_please_wait_for_approve_changes'));
            } else {
                flashSuccess(__('job_updated_successfully'));
            }
        }

        return $main_job;
    }
}
