<?php

namespace App\Services\Admin\Job;

use App\Http\Traits\JobAble;
use App\Models\Company;
use App\Models\Job;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Services\API\EssAPI\EssApiService;

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
        if ($request->ispost_carejobs === 'true') {
            $this->sendJobToCareWorkerJobs($jobCreated, $request->categories);
        }

        if ($request->ispost_govjobs === 'true') {
            $this->sendJobToGovJobs($jobCreated, $request->categories);
        }


        if ($request->ispost_facebook === 'true') {
            $this->sendJobToFacebook($jobCreated);
        }

        if ($request->ispost_linkedin === 'true') {
            $this->sendJobToLinkedIn($jobCreated);
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

    protected function sendJobToCareWorkerJobs($job,$categories)
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
        $companyData = Company::findOrFail($job->company_id);

        $characterLimit = env('ESS_API_JOB_DESCRIPTION_CHAR_LIMIT', 50);
        $description = strip_tags($job->description); // Remove HTML tags
        $descriptionWords = explode(' ', $description);
        if (count($descriptionWords) > $characterLimit) {
            $description = implode(' ', array_slice($descriptionWords, 0, $characterLimit)) . '...';
        }

        $seeMoreLink = "<a href='" . url('/job/' . $job->slug) . "'>See More</a>";
        $description .= ' ' . $seeMoreLink;


            // Map regions to state codes
            $regionMapping = [
                "South Australia" => ["city" => "Adelaide", "postcode" => "5000"],
                "Victoria" => ["city" => "Melbourne", "postcode" => "3000"],
                "New South Wales" => ["city" => "Sydney", "postcode" => "2000"],
                "Queensland" => ["city" => "Brisbane", "postcode" => "4000"],
                "Tasmania" => ["city" => "Hobart", "postcode" => "7000"],
                "Northern Territory" => ["city" => "Darwin", "postcode" => "0800"],
                "Western Australia" => ["city" => "Perth", "postcode" => "6000"],
                "Australian Capital Territory" => ["city" => "Canberra", "postcode" => "2600"],
                "New Zealand" => ["city" => "Auckland", "postcode" => "1010"], // New Zealand mapping
            ];

            // Determine the city and postcode based on the region
            $region = $job->region ?? '';
            $city = $regionMapping[$region]['city'];

            $postcode =  $regionMapping[$region]['postcode'];

            $stateMapping = [
                "New South Wales" => "NSW",
                "Victoria" => "VIC",
                "Queensland" => "QLD",
                "Tasmania" => "TAS",
                "Northern Territory" => "NT",
                "South Australia" => "SA",
                "Australian Capital Territory" => "ACT",
                "Western Australia" => "WA",
                "New Zealand" => "NZ",
            ];

           $stateCode = $stateMapping[$region] ?? 'ACT';


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
                "EmployerId" => 0,
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
            // dd($vacancyId, $response);
            return $response;
        } catch (\Exception $e) {
            \Log::error('Error sending job to GovJobs: ' . $e->getMessage());
            return null;
        }
    }


    protected function sendJobToFacebook($job){

        $characterLimit = env('ESS_API_JOB_DESCRIPTION_CHAR_LIMIT', 50);
        $description = strip_tags($job->description); // Remove HTML tags

        // Split the description into words
        $descriptionWords = explode(' ', $description);

        // Truncate the description if it exceeds the character limit
        if (count($descriptionWords) > $characterLimit) {
            $description = implode(' ', array_slice($descriptionWords, 0, $characterLimit)) . '...';
        }

        // Generate the "see more" link
        $seeMoreLink = url('/job/' . $job->slug);

        // Append the "Click here to see more details" text to the description
        // $description .= " Click blow button to see more details";
        $description .= " Click here to see more details: " . $seeMoreLink;
        $message = $description;

        $accessToken = $this->getLongLivedToken();

        $url = "https://graph.facebook.com/v20.0/103121261078671/feed";


<<<<<<< Updated upstream
        // Initialize cURL session
=======
        if ($logoUrl) {
            $response = $this->uploadImageToFacebook($accessToken, $logoUrl, $message);
        } else {
            $response = $this->postTextToFacebook($accessToken, $message);
        }

    }

    protected function uploadImageToFacebook($accessToken, $imageUrl, $message)
    {

        $url = "https://graph.facebook.com/v20.0/103121261078671/photos";
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

        curl_close($ch);
        return json_decode($response, true);
    }

    protected function postTextToFacebook($accessToken, $message)
    {
        $url = "https://graph.facebook.com/v20.0/103121261078671/feed";
>>>>>>> Stashed changes
        $ch = curl_init();

        // Set the URL and other options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'message' => $message,
            'access_token' => $accessToken,
            // 'link' => $link
        ]));

        // Execute cURL request
        $response = curl_exec($ch);
        curl_close($ch);
    }


<<<<<<< Updated upstream
=======
        protected function sendJobToLinkedIn($job)
    {
        $characterLimit = env('LINKEDIN_JOB_DESCRIPTION_CHAR_LIMIT', 1300); // LinkedIn allows longer posts
        $description = strip_tags($job->description); // Remove HTML tags

        // Truncate the description if it exceeds the character limit
        if (strlen($description) > $characterLimit) {
            $description = substr($description, 0, $characterLimit) . '...';
        }

        $seeMoreLink = url('/job/' . $job->slug);

        // Format the message
        $message = $job->title . "\n\n"; // Job title on the first line
        $message .= $description . "\n\n"; // Truncated description
        $message .= "Click here to see more: " . $seeMoreLink; // Add the link

        $accessToken = $this->getLinkedInAccessToken();

        $vanityName = 'council-direct';

        $organizationURN = $this->getOrganizationURN($accessToken,$vanityName);
        dd($organizationURN);

        // Post the job to LinkedIn
        $response = $this->postJobToLinkedIn($accessToken, $message, $job->company->linkedin_urn);

        return $response;
    }

    protected function postJobToLinkedIn($accessToken, $message, $organizationURN)
    {
        $url = "https://api.linkedin.com/v2/ugcPosts";

        $postData = [
            'author' => 'urn:li:organization:' . $organizationURN, // Replace with your organization's URN
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $message
                    ],
                    'shareMediaCategory' => 'NONE'
                ]
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $accessToken,
            "Content-Type: application/json",
            "X-Restli-Protocol-Version: 2.0.0"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    protected function getLinkedInAccessToken()
    {
        $accesToken = env('LINKEDIN_ACCESS_TOKEN');
        return $accesToken;
    }

    protected function getOrganizationURN($accessToken, $vanityName)
        {
            $url = "https://api.linkedin.com/v2/organizations?q=vanityName&vanityName=" . urlencode($vanityName);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer " . $accessToken,
                "X-Restli-Protocol-Version: 2.0.0"
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);
            dd($data);
            if (isset($data['elements'][0]['id'])) {
                return $data['elements'][0]['id']; // Return the organization URN
            }

            return null; // Handle cases where the URN is not found
        }





>>>>>>> Stashed changes
    public function getLongLivedToken()
    {
        $appId = env('FACEBOOK_APP_ID');
        $appSecret = env('FACEBOOK_APP_SECRET');
        $shortLivedToken = env('FACEBOOK_ACCESS_TOKEN');

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

        return $longLivedToken;
    }
}
