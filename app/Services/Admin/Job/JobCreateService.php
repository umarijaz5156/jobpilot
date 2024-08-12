<?php

namespace App\Services\Admin\Job;

use App\Http\Traits\JobAble;
use App\Models\Company;
use App\Models\Job;
use Carbon\Carbon;
use GuzzleHttp\Client;


class JobCreateService
{
    use JobAble;

    /**
     * Create job
     *
     * @return Job $jobCreated
     */
    public function execute($request): Job
    {
        // Highlight & featured
        $highlight = $request->badge == 'highlight' ? 1 : 0;
        $featured = $request->badge == 'featured' ? 1 : 0;

        $setting = loadSetting();
        $featured_days = $setting->featured_job_days > 0 ? now()->addDays($setting->featured_job_days)->format('Y-m-d') : null;
        $highlight_days = $setting->highlight_job_days > 0 ? now()->addDays($setting->highlight_job_days)->format('Y-m-d') : null;

        if ($request->get('company_id')) {
            $companyId = $request->get('company_id');
            $companyName = null;
        } else {
            $companyId = null;
            $companyName = $request->get('company_name');
        }

        $categoryId = $request->categories[0] ?? 3;

        // Job create
        $jobCreated = Job::create([
            'title' => $request->title,
            'company_id' => $companyId,
            'company_name' => $companyName,
            'category_id' => $categoryId,
            'state_id' => $request->state_id,
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
            'featured_until' => $featured_days,
            'highlight_until' => $highlight_days,
            'is_remote' => $request->is_remote ?? 0,
            'status' => 'active',
            'ongoing' => $request->is_ongoing ?? 0

        ]);

        // Benefits insert
        $benefits = $request->benefits ?? null;
        if ($benefits) {
            $this->jobBenefitsInsert($request->benefits, $jobCreated);
        }

        // Tags insert
        $tags = $request->tags ?? null;
        if ($tags) {
            $this->jobTagsInsert($request->tags, $jobCreated);
        }

        // skills insert
        $skills = $request->skills ?? null;
        if ($skills) {
            $this->jobSkillsInsert($request->skills, $jobCreated);
        }

        // location insert
        updateMap($jobCreated);
        $jobCreated->selectedCategories()->sync($request->categories);

        if ($request->ispost_waterland === 'true') {
            $this->sendJobToSecondWebsite($jobCreated, $request->categories);
        }
        if ($request->ispost_engineeringjobshub === 'true') {
            $this->sendJobToEngineeringJobsHub($jobCreated, $request->categories);
        }
        if ($request->ispost_planningjobs === 'true') {
            $this->sendJobToPlanningJobs($jobCreated, $request->categories);
        }


        return $jobCreated;
    }

    protected function sendJobToSecondWebsite($job,$categories)
    {
        $client = new Client();
        $categoryId = $categories[0] ?? 3;
         $websiteUrl = env('WEBSITE_URL');

         $companyData = Company::findOrfail($job->company_id);


        try {
            $response = $client->post($websiteUrl, [
                'json' => [
                    'title' => $job->title,
                    'company_email' => $companyData->user->email,
                    'company_name' => $job->company_name,
                    'category_id' => $categoryId,
                    'categories' => $categories,
                    'state_id' => $job->state_id,
                    'role_id' => $job->role_id,
                    'salary_mode' => $job->salary_mode,
                    'custom_salary' => $job->custom_salary,
                    'min_salary' => $job->min_salary,
                    'max_salary' => $job->max_salary,
                    'salary_type' => $job->salary_type_id,
                    'deadline' => $job->deadline,
                    'education' => $job->education_id,
                    'experience' => $job->experience_id,
                    'job_type' => $job->job_type_id,
                    'vacancies' => $job->vacancies,
                    'apply_on' => $job->apply_on,
                    'apply_email' => $job->apply_email,
                    'apply_url' => $job->apply_url,
                    'description' => $job->description,
                    'featured' => $job->featured,
                    'highlight' => $job->highlight,
                    'featured_until' => $job->featured_until,
                    'highlight_until' => $job->highlight_until,
                    'is_remote' => $job->is_remote,
                    'status' => 'active',

                     // Location-related fields using $job properties
                     'address' => $job->address,
                     'neighborhood' => $job->neighborhood ?? '',
                     'locality' => $job->locality ?? '',
                     'place' => $job->place ?? '',
                     'district' => $job->district ?? '',
                     'postcode' => $job->postcode ?? '',
                     'region' => $job->region ?? '',
                     'country' => $job->country ?? '',
                     'long' => $job->long ?? '',
                     'lat' => $job->lat ?? '',
                     'exact_location' => $job->exact_location ?? '',
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('Error sending job to second website: ' . $e->getMessage());
            return null;
        }
    }

    protected function sendJobToEngineeringJobsHub($job,$categories)
    {
        $client = new Client();
        $categoryId = $categories[0] ?? 3;
         $websiteUrl = env('WEBSITE_URL_JOB_EngineeringJobsHub');

         $companyData = Company::findOrfail($job->company_id);


        try {
            $response = $client->post($websiteUrl, [
                'json' => [
                    'title' => $job->title,
                    'company_email' => $companyData->user->email,
                    'company_name' => $job->company_name,
                    'category_id' => $categoryId,
                    'categories' => $categories,
                    'state_id' => $job->state_id,
                    'role_id' => $job->role_id,
                    'salary_mode' => $job->salary_mode,
                    'custom_salary' => $job->custom_salary,
                    'min_salary' => $job->min_salary,
                    'max_salary' => $job->max_salary,
                    'salary_type' => $job->salary_type_id,
                    'deadline' => $job->deadline,
                    'education' => $job->education_id,
                    'experience' => $job->experience_id,
                    'job_type' => $job->job_type_id,
                    'vacancies' => $job->vacancies,
                    'apply_on' => $job->apply_on,
                    'apply_email' => $job->apply_email,
                    'apply_url' => $job->apply_url,
                    'description' => $job->description,
                    'featured' => $job->featured,
                    'highlight' => $job->highlight,
                    'featured_until' => $job->featured_until,
                    'highlight_until' => $job->highlight_until,
                    'is_remote' => $job->is_remote,
                    'status' => 'active',

                     // Location-related fields using $job properties
                     'address' => $job->address,
                     'neighborhood' => $job->neighborhood ?? '',
                     'locality' => $job->locality ?? '',
                     'place' => $job->place ?? '',
                     'district' => $job->district ?? '',
                     'postcode' => $job->postcode ?? '',
                     'region' => $job->region ?? '',
                     'country' => $job->country ?? '',
                     'long' => $job->long ?? '',
                     'lat' => $job->lat ?? '',
                     'exact_location' => $job->exact_location ?? '',
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('Error sending job to second website: ' . $e->getMessage());
            return null;
        }
    }

    protected function sendJobToPlanningJobs($job,$categories)
    {
        $client = new Client();
        $categoryId = $categories[0] ?? 3;
         $websiteUrl = env('WEBSITE_URL_JOB_PlanningJob');

         $companyData = Company::findOrfail($job->company_id);


        try {
            $response = $client->post($websiteUrl, [
                'json' => [
                    'title' => $job->title,
                    'company_email' => $companyData->user->email,
                    'company_name' => $job->company_name,
                    'category_id' => $categoryId,
                    'categories' => $categories,
                    'state_id' => $job->state_id,
                    'role_id' => $job->role_id,
                    'salary_mode' => $job->salary_mode,
                    'custom_salary' => $job->custom_salary,
                    'min_salary' => $job->min_salary,
                    'max_salary' => $job->max_salary,
                    'salary_type' => $job->salary_type_id,
                    'deadline' => $job->deadline,
                    'education' => $job->education_id,
                    'experience' => $job->experience_id,
                    'job_type' => $job->job_type_id,
                    'vacancies' => $job->vacancies,
                    'apply_on' => $job->apply_on,
                    'apply_email' => $job->apply_email,
                    'apply_url' => $job->apply_url,
                    'description' => $job->description,
                    'featured' => $job->featured,
                    'highlight' => $job->highlight,
                    'featured_until' => $job->featured_until,
                    'highlight_until' => $job->highlight_until,
                    'is_remote' => $job->is_remote,
                    'status' => 'active',

                     // Location-related fields using $job properties
                     'address' => $job->address,
                     'neighborhood' => $job->neighborhood ?? '',
                     'locality' => $job->locality ?? '',
                     'place' => $job->place ?? '',
                     'district' => $job->district ?? '',
                     'postcode' => $job->postcode ?? '',
                     'region' => $job->region ?? '',
                     'country' => $job->country ?? '',
                     'long' => $job->long ?? '',
                     'lat' => $job->lat ?? '',
                     'exact_location' => $job->exact_location ?? '',
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('Error sending job to second website: ' . $e->getMessage());
            return null;
        }
    }
}
