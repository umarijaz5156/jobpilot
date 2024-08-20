<?php

namespace App\Services\Admin\Job;

use App\Http\Traits\JobAble;
use App\Models\Job;
use Carbon\Carbon;
use App\Services\API\EssAPI\EssApiService;
use App\Models\Company;

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

        $categoryId = $request->categories[0] ?? 3;

        $job->update([
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
            'is_remote' => $request->is_remote ?? 0,
            'status' => $status,
            'ongoing' => $request->is_ongoing ?? 0

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
        $job->selectedCategories()->sync($request->categories);

        // Check if this is a government job and update it
        if ($job->essapi_job_id) {
            $this->sendUpdatedJobToGov($job);
        }

        return $job;
    }

    /**
     * Send the updated job to the government API
     *
     * @param Job $job
     */
    protected function sendUpdatedJobToGov($job)
    {
        $companyData = Company::findOrFail($job->company_id);

        $characterLimit = env('ESS_API_JOB_DESCRIPTION_CHAR_LIMIT', 50);
        $description = strip_tags($job->description);
        $descriptionWords = explode(' ', $description);
        if (count($descriptionWords) > $characterLimit) {
            $description = implode(' ', array_slice($descriptionWords, 0, $characterLimit)) . '...';
        }

        $seeMoreLink = "<a href='" . url('/job/' . $job->slug) . "'>See More</a>";
        $description .= ' ' . $seeMoreLink;

        $data = [
            "VacancyId" => $job->essapi_job_id,
            "OrganisationCode" => "YYEC",
            "SiteCode" => "QG38",
            "EmployerId" => env('ESS_API_JOB_EMPLOYER_ID', 0),
            "EmployerContactId" => $companyData->employer_contact_id,
            "VacancyTitle" => $job->title,
            "OccupationCategoryCode" => "542112",
            "VacancyDescription" => $description,
            "PositionLimitCount" => $job->vacancies,
            "WorkTypeCode" => "P",
            "TenureCode" => "P",
            "ApplicationStyleCode" => "PSD",
            "ClientTypeCode" => "98",
            "PlacementTypeCode" => "N",
            "HoursDescription" => $job->hours_description,
            "IndgenousJobFlag" => true,
            "JobJeopardyFlag" => false,
            "SalaryDescription" => "SLNS",
            "ContractTypeCode" => null,
            "RegionCode" => "4ACQ",
            "ExpiryDate" => Carbon::parse($job->deadline)->format('Y-m-d\TH:i:sO'),
            "UpdatedBy" => auth()->user()->username,
            "UpdatedOn" => Carbon::now()->format('Y-m-d\TH:i:sO'),
            "VacancyType" => "H",
            "CreatedOn" => $job->created_at->format('Y-m-d\TH:i:sO'),
            "CreatedBy" => $job->created_by,
        ];

        try {
            $response = (new EssapiService())->callApi('Live/Vacancy/api/v1/public/vacancies/' . $job->essapi_job_id, 'GET');
            if (isset($response['Code']) && $response['Code'] === 200) {
                $OptimisticConcurrencyCheckValue = $response['Data']['OptimisticConcurrencyCheckValue'];
            } else {
                flashError('Failed to retrieve vacancy data from the API.');
            }
            $response = null;
            $headers = [
                'If-Match' => $OptimisticConcurrencyCheckValue
            ];
            $response = (new EssapiService())->callApi('Live/Vacancy/api/v1/public/vacancies', 'PUT', $data, $headers);
            // dd($response);
            \Log::info('Success updating job on GovJobs: ');
            \Log::info($response);

            return $response;
        } catch (\Exception $e) {
            \Log::error('Error updating job on GovJobs: ' . $e->getMessage());
            return null;
        }
    }

}
