<?php

namespace App\Services\Admin\Job;

use App\Http\Traits\JobAble;
use App\Models\Job;
use Carbon\Carbon;
use App\Services\API\EssAPI\EssApiService;
use App\Models\Company;
use App\Models\City;
use App\Models\Setting;
use App\Models\State;
use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

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
            'ongoing' => $request->is_ongoing ?? 0,
            'city_id' => $request->city_id,


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


        if ($request->ispost_waterland === 'true') {
            $this->sendJobToSecondWebsite($job, $request->categories);
        }
        if ($request->ispost_engineeringjobshub === 'true') {
            $this->sendJobToEngineeringJobsHub($job, $request->categories);
        }
        if ($request->ispost_planningjobs === 'true') {
            $this->sendJobToPlanningJobs($job, $request->categories);
        }
        if ($request->ispost_carejobs === 'true') {
            $this->sendJobToCareWorkerJobs($job, $request->categories);
        }

        if ($request->ispost_facebook === 'true') {
            $this->sendJobToFacebook($job);
        }


        if ($request->ispost_facebook_WL === 'true') {
            $this->sendJobToFacebookWL($job);
        }


        if ($request->ispost_facebook_EH === 'true') {
            $this->sendJobToFacebookEH($job);
        }

        if ($request->ispost_facebook_PJ === 'true') {
            $this->ispost_facebook_PJ($job);
        }

        if ($request->ispost_facebook_CR === 'true') {
            $this->ispost_facebook_CR($job);
        }


        if ($request->ispost_linkedin_cd === 'true') {
            $this->sendJobToLinkedInCD($job);
        }
        if ($request->ispost_linkedin_wl === 'true') {
            $this->sendJobToLinkedInWL($job);
        }
        if ($request->ispost_linkedin_cw === 'true') {
            $this->sendJobToLinkedInCW($job);
        }

        if ($request->ispost_linkedin_PJ === 'true') {
            $this->sendJobToLinkedInPJ($job);
        }
        if ($request->ispost_linkedin_EH === 'true') {
            $this->sendJobToLinkedInEJH($job);
        }

        if ($request->ispost_govjobs === 'true') {

                $this->sendJobToGovJobs($job, $request->categories);
        }

        return $job;
    }

    protected function sendJobToSecondWebsite($job, $categories)
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

    protected function sendJobToEngineeringJobsHub($job, $categories)
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

    protected function sendJobToPlanningJobs($job, $categories)
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

    protected function sendJobToCareWorkerJobs($job, $categories)
    {
        $client = new Client();
        $categoryId = $categories[0] ?? 3;
        $websiteUrl = env('WEBSITE_URL_JOB_CareJobs');

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



    protected function sendJobToGovJobs($job, $categories)
    {

        $selectCity = City::where('id',$job->city_id)->first();
        $companyData = Company::findOrFail($job->company_id);

        $characterLimit = env('ESS_API_JOB_DESCRIPTION_CHAR_LIMIT', 50);
        $description = strip_tags($job->description);
        $descriptionWords = explode(' ', $description);
        if (count($descriptionWords) > $characterLimit) {
            $description = implode(' ', array_slice($descriptionWords, 0, $characterLimit)) . '...';
        }

        $seeMoreLink = "<a href='" . url('/job/' . $job->slug) . "'>See More</a>";
        $description .= ' ' . $seeMoreLink;


        $state = State::findOrfail($job->state_id);
        // Determine the city and postcode based on the region
        $region = $job->region ?? '';
        $city = $selectCity->name;

        $postcode =  $selectCity->postCode;

        $stateMapping = [
            "NSW (New South Wales)" => "NSW",
            "VIC (Victoria)" => "VIC",
            "QLD (Queensland)" => "QLD",
            "TAS (Tasmania)" => "TAS",
            "NT (Northern Territory)" => "NT",
            "SA (South Australia)" => "SA",
            "Australian Capital Territory" => "ACT",
            "WA (Western Australia)" => "WA",
            "New Zealand" => "NZ",
        ];


        $stateCode = $stateMapping[$state->name] ?? 'ACT';



        $data = [
            "Vacancy" => [
                "VacancyId" => 0,
                "VacancyStatusCode" => "O",
                "VacancyTitle" => $job->title,
                "VacancyDescription" => $description,
                "PositionLimitCount" => $job->vacancies,
                "PositionAvailableCount" => $job->vacancies,
                "PositionFilledCount" => 0,
                "ExpiryDate" => Carbon::parse($job->deadline)->format('Y-m-d\TH:i:sO'),
                "EmployerId" => env('ESS_API_JOB_EMPLOYER_ID', 0),
                "EmployerContactId" => null,
                "UserDefinedIdentifier" => "",
                "HoursDescription" => null,
                "RegionCode" => "4ACQ",
                "AreaDisplayCode" => "",
                "WorkTypeCode" => "P",
                "TenureCode" => "P",
                "ApplicationStyleCode" => "PSD",
                "OccupationCategoryCode" => "542112",
                "PlacementTypeCode" => "N",
                "ClientTypeCode" => "98",
                "IndgenousJobFlag" => true,
                "JobJeopardyFlag" => false,
                "AnticipateStartDate" => null,
                "NetDisplayType" => "E",
                "ContractTypeCode" => null,
                "OrganisationCode" => "YYEC",
                "SiteCode" => "QG38",
                "VacancyType" => "H",
                "VacancySourceCode" => null,
                "SalaryDescription" => "SLNS",
                "OpenStatusDate" => Carbon::now()->format('Y-m-d\TH:i:sO'),
                "InactiveStatusDate" => null,
                "SourceVacancyId" => null,
                "SourceEmployerId" => null,
                "WebUrl" => null,
            ],
            "VacancyAgent" => [
                "AgentName" => $companyData->user->name,
                "ContactName" => $companyData->user->name,
                "EmailAddress" => $companyData->user->email,
                "FaxNumber" => null,
                "MobileNumber" => $companyData->user->mobile ?? "0000000000",
                "OptimisticConcurrencyCheckValue" => null,
                "PhoneNumber" => $companyData->user->phone ?? "0000000000",
                "VacancyAgentId" => 0,
                "VacancyId" => 0,
            ],
            "VacancyAddress" => [
                "VacancyId" => 0,
                "VacancyAddressId" => 0,
                "AddressLine1" => $job->exact_location,
                "AddressLine2" => $job->address,
                "AddressLine3" => $job->region,
                "Suburb" => $city ?? 'BRADDON',
                "StateCode" => $stateCode ?? "ACT",
                "PostCode" => $postcode ?? "2612",
            ],
            "VacancyLicence" => [],
            "VacancySpecialGroup" => [],
        ];

        try {
            $response = (new EssapiService())->callApi('Live/Vacancy/api/v1/public/vacancies', 'POST', $data);
            \Log::info('Success posting job to GovJobs: ');
            \Log::info($response);
            $vacancyId = $response['Data']['Vacancy']['VacancyId'];
            $job->essapi_job_id = $vacancyId;
            $job->save();

            return $response;

        } catch (Exception $e) {


            flashError('Error on Work Force Australia. The selected city does not have a valid postcode or Job expiry date must be at least 31 days from today.');

            return redirect()->route('job.edit', $job->id)->withErrors(['error' => 'Error on Work Force Australia. The selected city does not have a valid postcode or Job expiry date must be at least 31 days from today.']);

            }

    }



    // facebook post on council direct
    protected function sendJobToFacebook($job)
    {
        $characterLimit = env('ESS_API_JOB_DESCRIPTION_CHAR_LIMIT', 50);
        $description = strip_tags($job->description); // Remove HTML tags

        // Split the description into words
        $descriptionWords = explode(' ', $description);

        // Truncate the description if it exceeds the character limit
        if (count($descriptionWords) > $characterLimit) {
            $description = implode(' ', array_slice($descriptionWords, 0, $characterLimit)) . '...';
        }

        $logoUrl = url($job->company->logo);
        $seeMoreLink = url('/job/' . $job->slug);

        // Format the message
        $message = $job->title . "\n\n"; // Job title on the first line
        $message .= $description . "\n\n"; // Truncated description
        $message .= "Click here to see more: " . $seeMoreLink; // Add the link

        $accessToken = $this->getLongLivedToken();



        if ($logoUrl) {
            $url = "https://graph.facebook.com/v20.0/103121261078671/photos";
            $response = $this->uploadImageToFacebook($accessToken, $logoUrl, $message,$url);
            // dd($response );
        } else {
            $url = "https://graph.facebook.com/v20.0/103121261078671/feed";
            $response = $this->postTextToFacebook($accessToken, $message,$url);
            // dd($response );

        }


    }

    // end council direct facebook


    // start water land facebook

    protected function sendJobToFacebookWL($job)
    {
        $characterLimit = env('ESS_API_JOB_DESCRIPTION_CHAR_LIMIT', 50);
        $description = strip_tags($job->description); // Remove HTML tags

        // Split the description into words
        $descriptionWords = explode(' ', $description);

        // Truncate the description if it exceeds the character limit
        if (count($descriptionWords) > $characterLimit) {
            $description = implode(' ', array_slice($descriptionWords, 0, $characterLimit)) . '...';
        }

        $logoUrl = url($job->company->logo);
        $seeMoreLink = url('/job/' . $job->slug);

        // Format the message
        $message = $job->title . "\n\n"; // Job title on the first line
        $message .= $description . "\n\n"; // Truncated description
        $message .= "Click here to see more: " . $seeMoreLink; // Add the link

        $accessToken = $this->getLongLivedTokenWL();
        if ($logoUrl) {
            $url = "https://graph.facebook.com/v20.0/276526412210237/photos";
            $response = $this->uploadImageToFacebook($accessToken, $logoUrl, $message,$url);
            // dd($response );
        } else {
            $url = "https://graph.facebook.com/v20.0/276526412210237/feed";
            $response = $this->postTextToFacebook($accessToken, $message,$url);
            // dd($response );
        }


    }
    // end water land facebook

    // engineering jobs hub

    protected function sendJobToFacebookEH($job)
    {
        $characterLimit = env('ESS_API_JOB_DESCRIPTION_CHAR_LIMIT', 50);
        $description = strip_tags($job->description); // Remove HTML tags

        // Split the description into words
        $descriptionWords = explode(' ', $description);

        // Truncate the description if it exceeds the character limit
        if (count($descriptionWords) > $characterLimit) {
            $description = implode(' ', array_slice($descriptionWords, 0, $characterLimit)) . '...';
        }

        $logoUrl = url($job->company->logo);
        $seeMoreLink = url('/job/' . $job->slug);

        // Format the message
        $message = $job->title . "\n\n"; // Job title on the first line
        $message .= $description . "\n\n"; // Truncated description
        $message .= "Click here to see more: " . $seeMoreLink; // Add the link

        $accessToken = $this->getLongLivedTokenEH();
        if ($logoUrl) {
            $url = "https://graph.facebook.com/v20.0/449251164931014/photos";
            $response = $this->uploadImageToFacebook($accessToken, $logoUrl, $message,$url);
            // dd($response );
        } else {
            $url = "https://graph.facebook.com/v20.0/449251164931014/feed";
            $response = $this->postTextToFacebook($accessToken, $message,$url);
            // dd($response );
        }


    }

    // planning jobs
    protected function ispost_facebook_PJ($job)
    {
        $characterLimit = env('ESS_API_JOB_DESCRIPTION_CHAR_LIMIT', 50);
        $description = strip_tags($job->description); // Remove HTML tags

        // Split the description into words
        $descriptionWords = explode(' ', $description);

        // Truncate the description if it exceeds the character limit
        if (count($descriptionWords) > $characterLimit) {
            $description = implode(' ', array_slice($descriptionWords, 0, $characterLimit)) . '...';
        }

        $logoUrl = url($job->company->logo);
        $seeMoreLink = url('/job/' . $job->slug);

        // Format the message
        $message = $job->title . "\n\n"; // Job title on the first line
        $message .= $description . "\n\n"; // Truncated description
        $message .= "Click here to see more: " . $seeMoreLink; // Add the link

        $accessToken = $this->getLongLivedTokenPJ();
        if ($logoUrl) {
            $url = "https://graph.facebook.com/v20.0/347093105163707/photos";
            $response = $this->uploadImageToFacebook($accessToken, $logoUrl, $message,$url);
            // dd($response );
        } else {
            $url = "https://graph.facebook.com/v20.0/347093105163707/feed";
            $response = $this->postTextToFacebook($accessToken, $message,$url);
            // dd($response );
        }


    }


    // Care worker jobs

    protected function ispost_facebook_CR($job)
    {
        $characterLimit = env('ESS_API_JOB_DESCRIPTION_CHAR_LIMIT', 50);
        $description = strip_tags($job->description); // Remove HTML tags

        // Split the description into words
        $descriptionWords = explode(' ', $description);

        // Truncate the description if it exceeds the character limit
        if (count($descriptionWords) > $characterLimit) {
            $description = implode(' ', array_slice($descriptionWords, 0, $characterLimit)) . '...';
        }

        $logoUrl = url($job->company->logo);
        $seeMoreLink = url('/job/' . $job->slug);

        // Format the message
        $message = $job->title . "\n\n"; // Job title on the first line
        $message .= $description . "\n\n"; // Truncated description
        $message .= "Click here to see more: " . $seeMoreLink; // Add the link

        $accessToken = $this->getLongLivedTokenCR();
        if ($logoUrl) {
            $url = "https://graph.facebook.com/v20.0/428016797058161/photos";
            $response = $this->uploadImageToFacebook($accessToken, $logoUrl, $message,$url);
            // dd($response );
        } else {
            $url = "https://graph.facebook.com/v20.0/428016797058161/feed";
            $response = $this->postTextToFacebook($accessToken, $message,$url);
            // dd($response );
        }


    }





    // facebook api call for all
    protected function uploadImageToFacebook($accessToken, $imageUrl, $message, $url)
    {

        // $imageUrl = "https://landandwaterjobs.com.au/uploads/app/logo/VTeIziZeWfM2w63Hgfdn6qhWHdd3pALUf4K96xfk.png";
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'url' => $imageUrl,
                'caption' => $message, // Add the message as a caption
                'access_token' => $accessToken,
                'published' => true // Post immediately
            ]);


            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                // Handle cURL error
                $errorMessage = curl_error($ch);
                curl_close($ch);
                dd('cURL Error: ' . $errorMessage);
            }

            curl_close($ch);

            $decodedResponse = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle JSON decoding error
                dd('JSON Error: ' . json_last_error_msg());
            }

            return $decodedResponse;
        } catch (\Exception $e) {
            // Handle any other exceptions
            dd($e->getMessage());
        }
    }

    protected function postTextToFacebook($accessToken, $message, $url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'message' => $message,
                'access_token' => $accessToken
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                // Handle cURL error
                $errorMessage = curl_error($ch);
                curl_close($ch);
                dd('cURL Error: ' . $errorMessage);
            }

            curl_close($ch);

            $decodedResponse = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle JSON decoding error
                dd('JSON Error: ' . json_last_error_msg());
            }

            return $decodedResponse;
        } catch (\Exception $e) {
            // Handle any other exceptions
            dd('General Error: ' . $e->getMessage());
        }
    }



    // tokens

    public function getLongLivedToken()
    {

        try {
            $setting = Setting::first();


            $appId = $setting->facebook_app_id;
            $appSecret = $setting->facebook_app_secret;
            $shortLivedToken = $setting->facebook_access_token;

            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://graph.facebook.com/v20.0/oauth/access_token', [
                'query' => [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => $appId,
                    'client_secret' => $appSecret,
                    'fb_exchange_token' => $shortLivedToken,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            $longLivedToken = $responseBody['access_token'];

            $setting->facebook_access_token  = $longLivedToken;
            $setting->save();
            // $this->updateEnvFile('FACEBOOK_ACCESS_TOKEN', $longLivedToken);

            return $longLivedToken;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle the request exception and display the error message
            dd('Request Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle any other exceptions
            dd('General Error: ' . $e->getMessage());
        }
    }

    public function getLongLivedTokenWL()
    {

        try {

            $setting = Setting::first();
            $appId = $setting->facebook_app_id_wl;
            $appSecret = $setting->facebook_app_secret_wl;
            $shortLivedToken = $setting->facebook_access_token_wl;

            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://graph.facebook.com/v20.0/oauth/access_token', [
                'query' => [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => $appId,
                    'client_secret' => $appSecret,
                    'fb_exchange_token' => $shortLivedToken,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            $longLivedToken = $responseBody['access_token'];
            // $this->updateEnvFile('FACEBOOK_ACCESS_TOKEN_WL', $longLivedToken);
            $setting->facebook_access_token_wl  = $longLivedToken;
            $setting->save();

            return $longLivedToken;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle the request exception and display the error message
            dd('Request Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle any other exceptions
            dd('General Error: ' . $e->getMessage());
        }
    }

    public function getLongLivedTokenEH()
    {

        try {

            $setting = Setting::first();
            $appId = $setting->facebook_app_id_eh;
            $appSecret = $setting->facebook_app_secret_eh;
            $shortLivedToken = $setting->facebook_access_token_eh;

            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://graph.facebook.com/v20.0/oauth/access_token', [
                'query' => [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => $appId,
                    'client_secret' => $appSecret,
                    'fb_exchange_token' => $shortLivedToken,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            $longLivedToken = $responseBody['access_token'];
            $setting->facebook_access_token_eh  = $longLivedToken;
            $setting->save();

            return $longLivedToken;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle the request exception and display the error message
            dd('Request Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle any other exceptions
            dd('General Error: ' . $e->getMessage());
        }
    }


    // planning jobs

    public function getLongLivedTokenPJ()
    {

        try {

            $setting = Setting::first();
            $appId = $setting->facebook_app_id_pj;
            $appSecret = $setting->facebook_app_secret_pj;
            $shortLivedToken = $setting->facebook_access_token_pj;

            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://graph.facebook.com/v20.0/oauth/access_token', [
                'query' => [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => $appId,
                    'client_secret' => $appSecret,
                    'fb_exchange_token' => $shortLivedToken,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            $longLivedToken = $responseBody['access_token'];
            $setting->facebook_access_token_pj  = $longLivedToken;
            $setting->save();

            return $longLivedToken;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle the request exception and display the error message
            dd('Request Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle any other exceptions
            dd('General Error: ' . $e->getMessage());
        }
    }


    // care worker jobs



    public function getLongLivedTokenCR()
    {

        try {

            $setting = Setting::first();
            $appId = $setting->facebook_app_id_cw;
            $appSecret = $setting->facebook_app_secret_cw;
            $shortLivedToken = $setting->facebook_access_token_cw;

            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://graph.facebook.com/v20.0/oauth/access_token', [
                'query' => [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => $appId,
                    'client_secret' => $appSecret,
                    'fb_exchange_token' => $shortLivedToken,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            $longLivedToken = $responseBody['access_token'];
            $setting->facebook_access_token_cw  = $longLivedToken;
            $setting->save();

            return $longLivedToken;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle the request exception and display the error message
            dd('Request Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle any other exceptions
            dd('General Error: ' . $e->getMessage());
        }
    }


     // linkined
     protected function sendJobToLinkedInCD($job)
     {
         try {
             $characterLimit = env('LINKEDIN_JOB_DESCRIPTION_CHAR_LIMIT', 300); // LinkedIn allows longer posts
             $description = strip_tags($job->description); // Remove HTML tags
             $description = trim($description); // Trim leading and trailing whitespace

             // Truncate the description if it exceeds the character limit
             if (strlen($description) > $characterLimit) {
                 $description = substr($description, 0, $characterLimit) . '...';
             }
             $seeMoreLink = 'https://councildirect.com.au/job/' . $job->slug;

             // Format the message
             $message = $job->title . "\n\n"; // Job title on the first line
             $message .= $description . "\n\n"; // Truncated description
             $message .= "Click here to see more: " . $seeMoreLink; // Add the link

             $setting = Setting::first();
             $accessToken = $setting->linkedin_access_token;
             $organizationId = $setting->linkedin_council_direct_id;

             $company = Company::find($job->company_id);
             $imagePath = public_path($company->logo);

             $company_id = "urn:li:organization:$organizationId";
             $post_title = trim($message); // Ensure no excess whitespace

             $register_image_request = [
                 "registerUploadRequest" => [
                     "recipes" => [
                         "urn:li:digitalmediaRecipe:feedshare-image"
                     ],
                     "owner" => "$company_id",
                     "serviceRelationships" => [
                         [
                             "relationshipType" => "OWNER",
                             "identifier" => "urn:li:userGeneratedContent"
                         ]
                     ]
                 ]
             ];

             $register_post = Http::post("https://api.linkedin.com/v2/assets?action=registerUpload&oauth2_access_token=$accessToken", $register_image_request);
             $register_post = json_decode($register_post, true);

             if (!isset($register_post['value']['uploadMechanism'])) {
                 throw new \Exception("Failed to register upload with LinkedIn.");
             }

             $upload_url = $register_post['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
             $upload_assets = $register_post['value']['asset'];

             $response = Http::withHeaders(['Authorization' => "Bearer $accessToken"])->withBody(file_get_contents($imagePath), '')->put($upload_url);

             if ($response->failed()) {
                 throw new \Exception("Failed to upload image to LinkedIn.");
             }

             $request = [
                 "author" => "$company_id",
                 "lifecycleState" => "PUBLISHED",
                 "specificContent" => [
                     "com.linkedin.ugc.ShareContent" => [
                         "shareCommentary" => [
                             "text" => $post_title
                         ],
                         "shareMediaCategory" => "IMAGE",
                         "media" => [
                             [
                                 "status" => "READY",
                                 "media" => $upload_assets,
                             ]
                         ]
                     ],
                 ],
                 "visibility" => [
                     "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC",
                 ]
             ];

             $post_url = "https://api.linkedin.com/v2/ugcPosts?oauth2_access_token=" . $accessToken;
             $post = Http::post($post_url, $request);

             if ($post->failed()) {
                 throw new \Exception("Failed to post content to LinkedIn.");
             }

             return true;

         } catch (\Exception $e) {
             // Log the error for debugging
             \Log::error('Error sending job to LinkedIn: ' . $e->getMessage());

             // Return or throw the error for handling in the calling code
             return response()->json(['error' => $e->getMessage()], 500);
         }
     }


     protected function sendJobToLinkedInWL($job)
     {

        //  try {
             $characterLimit = env('LINKEDIN_JOB_DESCRIPTION_CHAR_LIMIT', 300); // LinkedIn allows longer posts

             $description = strip_tags($job->description); // Remove HTML tags
             $description = trim($description); // Trim leading and trailing whitespace

             // Truncate the description if it exceeds the character limit
             if (strlen($description) > $characterLimit) {
                 $description = substr($description, 0, $characterLimit) . '...';
             }
             $seeMoreLink = 'https://councildirect.com.au/job/' . $job->slug;


             $message = $job->title . "\n\n"; // Job title on the first line
            $message .= $description . "\n\n"; // Truncated description
            $message .= "Click here to see more: " . $seeMoreLink; // Add the link

             $setting = Setting::first();
             $accessToken = $setting->linkedin_access_token;
             $organizationId = $setting->linkedin_land_water_id;

             $company = Company::find($job->company_id);
             $imagePath = public_path($company->logo);



             $company_id = "urn:li:organization:$organizationId";
             $post_title = trim($message); // Ensure no excess whitespace
            //  dd($post_title);
            //  $post_title = "hello this is text post";
             //  dd($imagePath);
             $register_image_request = [
                 "registerUploadRequest" => [
                     "recipes" => [
                         "urn:li:digitalmediaRecipe:feedshare-image"
                     ],
                     "owner" => "$company_id",
                     "serviceRelationships" => [
                         [
                             "relationshipType" => "OWNER",
                             "identifier" => "urn:li:userGeneratedContent"
                         ]
                     ]
                 ]
             ];

             $register_post = Http::post("https://api.linkedin.com/v2/assets?action=registerUpload&oauth2_access_token=$accessToken", $register_image_request);
             $register_post = json_decode($register_post, true);
             $upload_url = $register_post['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
             $upload_assets = $register_post['value']['asset'];

             $response = Http::withHeaders(['Authorization' => "Bearer $accessToken"])->withBody(file_get_contents($imagePath), '')->put($upload_url);
             $request = [
                 "author" => "$company_id",
                 "lifecycleState" => "PUBLISHED",
                 "specificContent" => [
                     "com.linkedin.ugc.ShareContent" => [
                         "shareCommentary" => [
                             "text" => $post_title
                         ],
                         "shareMediaCategory" => "IMAGE",
                         "media" => [
                             [
                                 "status" => "READY",
                                 "media" => $upload_assets,
                             ]
                         ]
                     ],
                 ],
                 "visibility" => [
                     "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC",
                 ]
             ];
             $post_url = "https://api.linkedin.com/v2/ugcPosts?oauth2_access_token=" . $accessToken;
             $post = Http::post($post_url, $request);

             return true;

             //  if ($post->successful()) {
            //     // Display the successful response
            //     $responseData = $post->json(); // Convert response to array
            //     dd('Post successful:', $responseData);
            // } else {
            //     // Display the error message
            //     $errorMessage = $post->body(); // Get the body of the response
            //     $errorStatus = $post->status(); // Get the status code
            //     dd('Post failed with status ' . $errorStatus . ':', $errorMessage);
            // }


        //  } catch (\GuzzleHttp\Exception\RequestException $e) {
        //     dd($e->getMessage());
        //      // Handle Guzzle-specific request exceptions
        //      return 'Request Error: ' . $e->getMessage();
        //  } catch (\Exception $e) {
        //     dd($e->getMessage());
        //      // Handle any other general exceptions
        //      return 'General Error: ' . $e->getMessage();
        //  }

        //  dd('none');
     }

     protected function sendJobToLinkedInCW($job)
     {

        //  try {
             $characterLimit = env('LINKEDIN_JOB_DESCRIPTION_CHAR_LIMIT', 300); // LinkedIn allows longer posts

             $description = strip_tags($job->description); // Remove HTML tags
             $description = trim($description); // Trim leading and trailing whitespace

             // Truncate the description if it exceeds the character limit
             if (strlen($description) > $characterLimit) {
                 $description = substr($description, 0, $characterLimit) . '...';
             }
             $seeMoreLink = 'https://councildirect.com.au/job/' . $job->slug;


             $message = $job->title . "\n\n"; // Job title on the first line
            $message .= $description . "\n\n"; // Truncated description
            $message .= "Click here to see more: " . $seeMoreLink; // Add the link

             $setting = Setting::first();
             $accessToken = $setting->linkedin_access_token;
             $organizationId = $setting->linkedin_care_worker_id;

             $company = Company::find($job->company_id);
             $imagePath = public_path($company->logo);



             $company_id = "urn:li:organization:$organizationId";
             $post_title = trim($message); // Ensure no excess whitespace
            //  dd($post_title);
            //  $post_title = "hello this is text post";
             //  dd($imagePath);
             $register_image_request = [
                 "registerUploadRequest" => [
                     "recipes" => [
                         "urn:li:digitalmediaRecipe:feedshare-image"
                     ],
                     "owner" => "$company_id",
                     "serviceRelationships" => [
                         [
                             "relationshipType" => "OWNER",
                             "identifier" => "urn:li:userGeneratedContent"
                         ]
                     ]
                 ]
             ];

             $register_post = Http::post("https://api.linkedin.com/v2/assets?action=registerUpload&oauth2_access_token=$accessToken", $register_image_request);
             $register_post = json_decode($register_post, true);
             $upload_url = $register_post['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
             $upload_assets = $register_post['value']['asset'];

             $response = Http::withHeaders(['Authorization' => "Bearer $accessToken"])->withBody(file_get_contents($imagePath), '')->put($upload_url);
             $request = [
                 "author" => "$company_id",
                 "lifecycleState" => "PUBLISHED",
                 "specificContent" => [
                     "com.linkedin.ugc.ShareContent" => [
                         "shareCommentary" => [
                             "text" => $post_title
                         ],
                         "shareMediaCategory" => "IMAGE",
                         "media" => [
                             [
                                 "status" => "READY",
                                 "media" => $upload_assets,
                             ]
                         ]
                     ],
                 ],
                 "visibility" => [
                     "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC",
                 ]
             ];
             $post_url = "https://api.linkedin.com/v2/ugcPosts?oauth2_access_token=" . $accessToken;
             $post = Http::post($post_url, $request);

             return true;

             //  if ($post->successful()) {
            //     // Display the successful response
            //     $responseData = $post->json(); // Convert response to array
            //     dd('Post successful:', $responseData);
            // } else {
            //     // Display the error message
            //     $errorMessage = $post->body(); // Get the body of the response
            //     $errorStatus = $post->status(); // Get the status code
            //     dd('Post failed with status ' . $errorStatus . ':', $errorMessage);
            // }


        //  } catch (\GuzzleHttp\Exception\RequestException $e) {
        //     dd($e->getMessage());
        //      // Handle Guzzle-specific request exceptions
        //      return 'Request Error: ' . $e->getMessage();
        //  } catch (\Exception $e) {
        //     dd($e->getMessage());
        //      // Handle any other general exceptions
        //      return 'General Error: ' . $e->getMessage();
        //  }

        //  dd('none');
     }

     protected function sendJobToLinkedInPJ($job)
     {

        //  try {
             $characterLimit = env('LINKEDIN_JOB_DESCRIPTION_CHAR_LIMIT', 300); // LinkedIn allows longer posts

             $description = strip_tags($job->description); // Remove HTML tags
             $description = trim($description); // Trim leading and trailing whitespace

             // Truncate the description if it exceeds the character limit
             if (strlen($description) > $characterLimit) {
                 $description = substr($description, 0, $characterLimit) . '...';
             }
             $seeMoreLink = 'https://councildirect.com.au/job/' . $job->slug;


             $message = $job->title . "\n\n"; // Job title on the first line
            $message .= $description . "\n\n"; // Truncated description
            $message .= "Click here to see more: " . $seeMoreLink; // Add the link

             $setting = Setting::first();
             $accessToken = $setting->linkedin_access_token;
             $organizationId = $setting->linkedin_planningjobs_id;

             $company = Company::find($job->company_id);
             $imagePath = public_path($company->logo);



             $company_id = "urn:li:organization:$organizationId";
             $post_title = trim($message); // Ensure no excess whitespace
            //  dd($post_title);
            //  $post_title = "hello this is text post";
             //  dd($imagePath);
             $register_image_request = [
                 "registerUploadRequest" => [
                     "recipes" => [
                         "urn:li:digitalmediaRecipe:feedshare-image"
                     ],
                     "owner" => "$company_id",
                     "serviceRelationships" => [
                         [
                             "relationshipType" => "OWNER",
                             "identifier" => "urn:li:userGeneratedContent"
                         ]
                     ]
                 ]
             ];

             $register_post = Http::post("https://api.linkedin.com/v2/assets?action=registerUpload&oauth2_access_token=$accessToken", $register_image_request);
             $register_post = json_decode($register_post, true);
             $upload_url = $register_post['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
             $upload_assets = $register_post['value']['asset'];

             $response = Http::withHeaders(['Authorization' => "Bearer $accessToken"])->withBody(file_get_contents($imagePath), '')->put($upload_url);
             $request = [
                 "author" => "$company_id",
                 "lifecycleState" => "PUBLISHED",
                 "specificContent" => [
                     "com.linkedin.ugc.ShareContent" => [
                         "shareCommentary" => [
                             "text" => $post_title
                         ],
                         "shareMediaCategory" => "IMAGE",
                         "media" => [
                             [
                                 "status" => "READY",
                                 "media" => $upload_assets,
                             ]
                         ]
                     ],
                 ],
                 "visibility" => [
                     "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC",
                 ]
             ];
             $post_url = "https://api.linkedin.com/v2/ugcPosts?oauth2_access_token=" . $accessToken;
             $post = Http::post($post_url, $request);

             return true;

     }

     protected function sendJobToLinkedInEJH($job)
     {

        //  try {
             $characterLimit = env('LINKEDIN_JOB_DESCRIPTION_CHAR_LIMIT', 300); // LinkedIn allows longer posts

             $description = strip_tags($job->description); // Remove HTML tags
             $description = trim($description); // Trim leading and trailing whitespace

             // Truncate the description if it exceeds the character limit
             if (strlen($description) > $characterLimit) {
                 $description = substr($description, 0, $characterLimit) . '...';
             }
             $seeMoreLink = 'https://councildirect.com.au/job/' . $job->slug;


             $message = $job->title . "\n\n"; // Job title on the first line
            $message .= $description . "\n\n"; // Truncated description
            $message .= "Click here to see more: " . $seeMoreLink; // Add the link

             $setting = Setting::first();
             $accessToken = $setting->linkedin_access_token;
             $organizationId = $setting->linkedin_EngineeringJobsHub_id;

             $company = Company::find($job->company_id);
             $imagePath = public_path($company->logo);



             $company_id = "urn:li:organization:$organizationId";
             $post_title = trim($message); // Ensure no excess whitespace
            //  dd($post_title);
            //  $post_title = "hello this is text post";
             //  dd($imagePath);
             $register_image_request = [
                 "registerUploadRequest" => [
                     "recipes" => [
                         "urn:li:digitalmediaRecipe:feedshare-image"
                     ],
                     "owner" => "$company_id",
                     "serviceRelationships" => [
                         [
                             "relationshipType" => "OWNER",
                             "identifier" => "urn:li:userGeneratedContent"
                         ]
                     ]
                 ]
             ];

             $register_post = Http::post("https://api.linkedin.com/v2/assets?action=registerUpload&oauth2_access_token=$accessToken", $register_image_request);
             $register_post = json_decode($register_post, true);
             $upload_url = $register_post['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
             $upload_assets = $register_post['value']['asset'];

             $response = Http::withHeaders(['Authorization' => "Bearer $accessToken"])->withBody(file_get_contents($imagePath), '')->put($upload_url);
             $request = [
                 "author" => "$company_id",
                 "lifecycleState" => "PUBLISHED",
                 "specificContent" => [
                     "com.linkedin.ugc.ShareContent" => [
                         "shareCommentary" => [
                             "text" => $post_title
                         ],
                         "shareMediaCategory" => "IMAGE",
                         "media" => [
                             [
                                 "status" => "READY",
                                 "media" => $upload_assets,
                             ]
                         ]
                     ],
                 ],
                 "visibility" => [
                     "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC",
                 ]
             ];
             $post_url = "https://api.linkedin.com/v2/ugcPosts?oauth2_access_token=" . $accessToken;
             $post = Http::post($post_url, $request);

             return true;

     }


}
