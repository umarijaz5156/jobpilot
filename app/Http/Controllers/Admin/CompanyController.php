<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyCreateFormRequest;
use App\Http\Requests\CompanyUpdateFormRequest;
use App\Models\Company;
use App\Models\IndustryType;
use App\Models\OrganizationType;
use App\Models\TeamSize;
use App\Models\User;
use App\Notifications\SendProfileVerifiedNotification;
use App\Services\Admin\Company\CompanyCreateService;
use App\Services\Admin\Company\CompanyListService;
use App\Services\Admin\Company\CompanyUpdateService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\Location\Entities\Country;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

use App\Mail\UserPdfMail;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\State;
use Exception;
use Illuminate\Support\Facades\Mail;
use PDF;
use GuzzleHttp\Client as ClientC;
use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;


class CompanyController extends Controller
{
    public function index(Request $request)
    {
        try {
            abort_if(! userCan('company.view'), 403);

            $companies = (new CompanyListService())->execute($request);
            $industry_types = IndustryType::all()->sortBy('name');
            $organization_types = OrganizationType::all()->sortBy('name');

            return view('backend.company.index', compact('companies', 'industry_types', 'organization_types'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function featureCompany(){

        $featured_companies = Company::where('featured', 1)->with('user')->get();
        $all_companies = Company::with('user')->get();

        return view('backend.company.feature', [
            'featured_companies' => $featured_companies,
            'all_companies' => $all_companies
        ]);
    }


    public function reportCompany(Request $request, $id)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

            // Convert the dates from DD/MM/YYYY to YYYY-MM-DD
        if ($startDate) {
            $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        }
        if ($endDate) {
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        }
        $company = Company::with([
            'jobs' => function ($query) use ($startDate, $endDate) {
                $query->with('category', 'role', 'job_type', 'salary_type')
                    ->ongoingFirst(); // Use the updated scope here

                if ($startDate && $endDate) {
                    $query->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate])
                        ->orWhereBetween('deadline', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate) {
                            $q->where('status', 'active')
                                ->where('created_at', '<', $startDate);
                        });
                    });
                }
            },
            'user.socialInfo',
            'user.contactInfo'
        ])->findOrFail($id);

        return view('backend.company.report', compact('company', 'startDate', 'endDate'));
    }

    public function sendEmail(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate =$request->input('endDate');
        $id =$request->input('userId');

        $user = Company::with([
            'jobs' => function ($query) use ($startDate, $endDate) {
                $query->with('category', 'role', 'job_type', 'salary_type')
                      ->ongoingFirst(); // Use the updated scope here

                if ($startDate && $endDate) {
                    $query->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate])
                          ->orWhereBetween('deadline', [$startDate, $endDate])
                          ->orWhere(function ($q) use ($startDate) {
                              $q->where('status', 'active')
                                ->where('created_at', '<', $startDate);
                          });
                    });
                }
            },
            'user.socialInfo',
            'user.contactInfo'
        ])->findOrFail($id);

        $totalJobs = count($user->jobs) ?? 0;
        //     $company = $user;
        // return view('pdf.user-report', compact('company', 'startDate', 'endDate'));

         // Generate the PDF
         $pdf = PDF::loadView('pdf.user-report', [
            'company' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalJobs' => $totalJobs,
        ])->setPaper('a3', 'landscape')->output(); // Set paper size to A3 and orientation to landscape


        // Send the email
        Mail::to($user->user->email)->send(new UserPdfMail($user, $pdf));

        return response()->json(['message' => 'Email sent successfully!']);
    }



    public function updateFeaturedC(Request $request)
    {
        // Validate the request
        $request->validate([
            'featured_Companies' => 'array'
        ]);

        Company::where('featured', 1)->update(['featured' => 0]);

        if ($request->has('featured_Companies')) {
            Company::whereIn('id', $request->featured_Companies)->update(['featured' => 1]);
        }

        return redirect()->back()->with('success', 'Featured Companies updated successfully!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            abort_if(! userCan('company.create'), 403);

            $data['countries'] = Country::all();
            $data['industry_types'] = IndustryType::all()->sortBy('name');
            $data['organization_types'] = OrganizationType::all()->sortBy('name');
            $data['team_sizes'] = TeamSize::all();

            return view('backend.company.create', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyCreateFormRequest $request)
    {
        try {
            abort_if(! userCan('company.create'), 403);

            (new CompanyCreateService())->execute($request);

            flashSuccess(__('company_created_successfully'));

            return redirect()->route('company.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            abort_if(! userCan('company.view'), 403);

            $company = Company::with([
                'jobs.appliedJobs',
                'user.socialInfo',
                'user.contactInfo',
                'jobs' => function ($job) {
                    return $job->latest()->with('category', 'role', 'job_type', 'salary_type');
                },
            ])->findOrFail($id);

            return view('backend.company.show', compact('company'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            abort_if(! userCan('company.update'), 403);

            $data['company'] = Company::findOrFail($id);
            $data['user'] = $data['company']->user->load('socialInfo');
            $data['industry_types'] = IndustryType::all()->sortBy('name');
            $data['organization_types'] = OrganizationType::all()->sortBy('name');
            $data['team_sizes'] = TeamSize::all();
            $data['socials'] = $data['company']->user->socialInfo;

            return view('backend.company.edit', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyUpdateFormRequest $request, Company $company)
    {
        try {
            abort_if(! userCan('company.update'), 403);

            (new CompanyUpdateService())->execute($request, $company);

            flashSuccess(__('company_updated_successfully'));

            return redirect()->route('company.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            abort_if(! userCan('company.delete'), 403);

            $company = Company::findOrFail($id);

            // company image delete
            deleteFile($company->logo);
            deleteFile($company->banner);
            deleteFile($company->user->image);

            // company cv view items delete
            $company->cv_views()->delete();
            $company->user->delete();
            $company->delete();

            flashSuccess(__('company_deleted_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function documents(Company $company)
    {
        try {
            $company = $company->load('media');

            return view('backend.company.document', [
                'company' => $company,
            ]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function downloadDocument(Request $request, Company $company)
    {
        try {
            $request->validate([
                'file_type' => 'required',
            ]);
            $media = $company->getFirstMedia($request->get('file_type'));

            return response()->download($media->getPath(), $media->file_name);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Change company status
     *
     * @return void
     */
    public function statusChange(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            $user->update(['status' => $request->status]);

            if ($request->status == 1) {
                return responseSuccess(__('company_activated_successfully'));
            } else {
                return responseSuccess(__('company_deactivated_successfully'));
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Change company verification status
     *
     * @return void
     */
    public function verificationChange(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            if ($request->status) {
                $user->update(['email_verified_at' => now()]);
                $message = __('email_verified_successfully');
            } else {
                $user->update(['email_verified_at' => null]);
                $message = __('email_unverified_successfully');
            }

            return responseSuccess($message);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Change company profile verification status
     *
     * @return void
     */
    public function profileVerificationChange(Request $request)
    {
        try {
            $company = Company::findOrFail($request->id);

            if ($request->status) {

                $company->document_verified_at = now();
                $company->update(['is_profile_verified' => true]);
                $company->user->notify(new SendProfileVerifiedNotification());
                $message = __('profile_verified_successfully');
            } else {

                $company->document_verified_at = null;
                $company->update(['is_profile_verified' => false]);
                $message = __('profile_unverified_successfully');
            }

            return responseSuccess($message);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Change company document verification status
     *
     * @param  Request  $request
     * @return void
     */
    public function toggle(Company $company)
    {
        try {
            if ($company->document_verified_at) {
                $company->update(['is_profile_verified' => false]);
                $company->document_verified_at = null;
                $message = __('unverified').' '.__('successfully');
            } else {
                $company->document_verified_at = now();
                $company->update(['is_profile_verified' => true]);
                $company->user->notify(new SendProfileVerifiedNotification());
                $message = __('verified').' '.__('successfully');
            }

            $company->save();

            return responseSuccess($message);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }


    public function UplaodVideo(){

        $filePath = public_path('company_video.xlsx');

        // Convert the Excel file to an array
        $dataArray = Excel::toArray([], $filePath);

        // The first sheet is typically at index 0
        $dataArray = $dataArray[0];

        // Remove the header row
        array_shift($dataArray);

        // Filter out rows where both columns are null
        $filteredData = array_filter($dataArray, function($row) {
            return !(is_null($row[0]) && is_null($row[1]));
        });

        // Remove the width="930" and height="466" attributes from the iframe tags and ensure space between <iframe and src
        $cleanedData = array_map(function($row) {
            $row[1] = preg_replace('/\s*width="930"\s*/', '', $row[1]);
            $row[1] = preg_replace('/\s*height="466"\s*/', '', $row[1]);
            $row[1] = preg_replace('/<iframe(?!\s)/', '<iframe ', $row[1]); // Add space after <iframe if missing
            return $row;
        }, $filteredData);

        // dd($cleanedData);

        // foreach ($cleanedData as $data) {
        //     $userName = $data[0];
        //     $videoUrl = $data[1];

        //     $user = User::where('name', $userName)->first();
        //     if ($user) {
        //         $company = Company::where('user_id', $user->id)->first();
        //         if ($company) {
        //             $company->video_url = $videoUrl;
        //             $company->save();
        //         }
        //     }
        // }

        dd('Company video URLs updated successfully.');

    }

    public function fileUploadProfiles(){

        $filePath = public_path('employers_export.csv');

         // Open the file
         $file = fopen($filePath, 'r');

         // Read the header
         $header = fgetcsv($file);

         // Initialize an array to store the parsed data
         $dataArray = [];

         // Loop through the file and parse each row
         while ($row = fgetcsv($file)) {
             $data = array_combine($header, $row);
             $dataArray[] = $data;
         }

         fclose($file);


         foreach ($dataArray as $data) {

            if (User::where('email', $data['email'])->exists()) {
                continue;
            }
            $name = $data['name'];
            $username = $name ? Str::slug($name) : Str::slug($name).'_'.time();

            while (User::where('username', $username)->exists()) {
                $username = Str::slug($name) . '_' . time();
            }

            $company = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $data['email'],
                'password' => bcrypt($data['email']),
                'role' => 'company',
            ]);

            $logo_url = $this->fetchAndSaveImage($data['logo_url'], 'uploads/images/company');

            $banner_url = $data['hero_url'] ? $this->fetchAndSaveImage($data['hero_url'], 'uploads/images/company') : null;

            $country = $data['country'] ? $data['country']  : 'Australia';
            $region = $data['state'] ? $data['state']  : null;
            $address = $data['address'] ? $data['address']  : null;
            $exact_location = $data['location'] ? $data['location']  : null;

            $organization_type_id = 7;
            $company->company()->update([
                'industry_type_id' => 10,
                'organization_type_id' => $organization_type_id,
                'team_size_id' => null,
                'establishment_date' => null,
                'logo' => $logo_url,
                'banner' => $banner_url,
                'website' => $data['url'],
                'bio' => $data['description'],
                'vision' => null,
                'country' => $country,
                'region' => $region,
                'address' => $address,
                'exact_location' => $exact_location
            ]);

            $company->contactInfo()->update([
                'phone' => $data['phone'],
                'email' => $data['email'],
            ]);


            updateMap($company->company());
        }

        dd('all done');

    }


    private function fetchAndSaveImage($url, $path)
    {
        try {#
            $response = Http::get($url);

            if ($response->successful()) {
                $imageName = basename($url);
                if (!File::isDirectory(public_path($path))) {
                    File::makeDirectory(public_path($path), 0777, true, true);
                }

                file_put_contents(public_path("$path/$imageName"), $response->body());
                return "$path/$imageName";
            }
        } catch (\Exception $e) {
            $this->error("Failed to fetch image from $url: " . $e->getMessage());
        }

        return null;
    }





    // central Coast council jobs functions

    public function centralCoast(){

        ini_set('max_execution_time', 300000000); // Set to 5 minutes

        $user = User::where('name', 'Central Coast Council')->first();

        $allJobs = [];
        $client = new Client();
        $iframeUrl = 'https://centralcoast.applynow.net.au';

            // Request the page with the iframe
            $crawler = $client->request('GET', $iframeUrl);

            // Extract job listings from the iframe page (replace selector with actual job container)
            $jobBlocks = $crawler->filter('.jobblock');  // Adjust the selector based on actual HTML


            $jobBlocks->each(function ($jobBlock) use (&$allJobs, $client, $user) {

            $url = $jobBlock->attr('data-url');

            $existingJob = Job::where('apply_url', $url)->first();
            if ($existingJob) {

            }else{

                $title = $jobBlock->filter('.job_title')->text();
                $location = $jobBlock->filter('.location')->text();
                $state = $jobBlock->attr('data-address_state') ?? 'New South Wales';
                $deadline = $jobBlock->attr('data-expires_at');

                $clientC = new ClientC();
                $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                $nominatimResponse = $clientC->get($nominatimUrl, [
                    'query' => [
                        'q' => $location,
                        'format' => 'json',
                        'limit' => 1
                    ],
                    'headers' => [
                        'User-Agent' => 'YourAppName/1.0'
                    ]
                ]);

                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $state . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }


                $jobCrawler = $client->request('GET', $url);
                // Get job description
                $description = $jobCrawler->filter('#job_description')->html() ?? 'No description available';

                // Format the deadline and postedAt to proper Carbon instances
                $formattedDeadline = Carbon::parse($deadline)->format('Y-m-d') ?? '2024-11-30';

                // Prepare the job data
                $jobRequest = [
                    'title' => $title,
                    'category_id' => 3,
                    'company_id' => $user->company->id,
                    'company_name' => 'Central Coast Council',
                    'apply_url' => $url,
                    'description' => $description,
                    'state_id' => $sId, // or dynamically get based on state
                    'vacancies' => 1,
                    'deadline' => $formattedDeadline,
                    'salary_mode' => 'custom',
                    'salary_type_id' => 1,
                    'apply_on' => 'custom_url',
                    'custom_salary' => 'Competitive',
                    'job_type_id' => 1, // Adjust this according to your mapping logic
                    'role_id' => 1,
                    'education_id' => 2,
                    'experience_id' => 4,
                    'featured' => 0,
                    'highlight' => 0,
                    'status' => 'active',
                    'ongoing' => 0
                ];

                // Save job into the database
                $done = $this->createJobFromScrape($jobRequest);

                $categories = [0 => "3"];
                $done->selectedCategories()->sync($categories);

                $done->update([
                    'address' => $exact_location,
                    'neighborhood' => $location,
                    'locality' => $location,
                    'place' =>  $location,
                    'country' => 'Australia',
                    'district' => $state ?? '',
                    'region' => $state ?? '',
                    'long' => $lng,
                    'lat' => $lat,
                    'exact_location' => $exact_location,
                ]);

                $allJobs[] = $jobRequest;
            }
        });

        // dd($allJobs); // If you want to inspect the final result after the loop
        return response()->json([
            'message' => count($allJobs) . ' job(s) scraped from Central Coast',
        ]);

            // Optionally return all the jobs or handle the data further
            // return $allJobs;
            // dd('all done');

    }

    // CanterburyBankstown

    public function CanterburyBankstown() {
        ini_set('max_execution_time', 300000000); // Set to 5 minutes

        $user = User::where('name', 'City of Canterbury Bankstown')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://careers.cbcity.nsw.gov.au/search'; // Main job search page URL

        // Request the page with the job listings
        $crawler = $client->request('GET', $mainUrl);

        // Extract job rows from the search results table
        $jobRows = $crawler->filter('#searchresults .data-row');  // Loop through each job row

        $jobRows->each(function ($jobRow) use (&$allJobs, $client, $user) {

            $jobUrl = $jobRow->filter('.jobTitle-link')->attr('href');  // Get the relative job URL
            $title = $jobRow->filter('.jobTitle-link')->text(); // Get the job title
            $location = $jobRow->filter('.jobLocation')->text(); // Get the job location
            $stateMap = [
                'QLD' => 'Queensland',
                'ACT' => 'Australian Capital Territory',
                'NSW' => 'New South Wales',
                'SA'  => 'South Australia',
                'TAS' => 'Tasmania',
                'VIC' => 'Victoria',
                'WA'  => 'Western Australia',
                'NT'  => 'Northern Territory',
            ];


            // Extract state abbreviation (2-3 uppercase letters)
            preg_match('/\b([A-Z]{2,3})\b/', $location, $matches);

            // Check if we found a match
            if (isset($matches[1]) && array_key_exists($matches[1], $stateMap)) {
                $stateFullName = $stateMap[$matches[1]];
            } else {
                // Default or fallback state if no match found
                $stateFullName = 'New South Wales';
            }


            // If the job URL exists, process the job
            if ($jobUrl) {
                $fullUrl = 'https://careers.cbcity.nsw.gov.au' . $jobUrl; // Make the URL absolute

                // Check if the job already exists
                $existingJob = Job::where('apply_url', $fullUrl)->first();
                if (!$existingJob) {

                    // Fetch job details page to get description and other information
                    $jobCrawler = $client->request('GET', $fullUrl);

                    // Extract job description, location, and expiry date
                    $description = $jobCrawler->filter('.jobdescription')->html() ?? 'No description available'; // Get job description HTML

                    $expiryDate = $jobCrawler->filter('#job-date')->text(); // Extract expiry date
                    $expiryDateCleaned = str_replace('Date: ', '', $expiryDate); // Clean up the date string

                    // Parse the date and format it to 'Y-m-d'
                    $formattedExpiryDate = Carbon::parse($expiryDateCleaned)->format('Y-m-d');

                    // Check if the parsed expiry date is in the past
                    if (Carbon::parse($formattedExpiryDate)->isBefore(Carbon::today())) {
                        // If the expiry date is in the past, set it to two weeks from today
                        $formattedExpiryDate = Carbon::today()->addWeeks(2)->format('Y-m-d');
                    }

                    // You can now use $formattedExpiryDate which is either the original date or updated

                    // Parse and format the expiry date
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);

                    $nominatimData = json_decode($nominatimResponse->getBody(), true);

                    if (!empty($nominatimData)) {
                        $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                        $lng = $nominatimData[0]['lon'] ?? '145.372664';
                        $exact_location = $nominatimData[0]['display_name'] ?? $location;

                    } else {
                        $lat = '-16.4614455' ;
                        $lng =  '145.372664';
                        $exact_location = $location;

                    }


                    $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                    if($stateId){
                        $sId = $stateId->id;
                    }else{
                        $sId = 3909;
                    }


                    // Prepare the job data
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => 3,
                        'company_id' => $user->company->id,
                        'company_name' => 'City of Canterbury Bankstown',
                        'apply_url' => $fullUrl,
                        'description' => $description,
                        'state_id' => $sId, // Use a default state ID if state not found
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'apply_on' => 'custom_url',
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1, // Adjust according to your mapping logic
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0
                    ];

                    // Save job into the database
                    $done = $this->createJobFromScrape($jobRequest);

                    $categories = [0 => "3"];
                    $done->selectedCategories()->sync($categories);

                    // Update location and other fields
                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to the allJobs array
                    $allJobs[] = $jobRequest;
                }
            }
        });

        // Return the number of jobs found

        return response()->json([
            'message' => count($allJobs) . ' job(s) scraped from Canterbury Bankstown',
        ]);

    }



    // ByronShire Council

    public function ByronShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Byron Shire Council')->first();
        $allJobs = [];
        $client = new Client();

        // Step 1: Load the page that contains the iframe
        $mainUrl = 'https://www.byron.nsw.gov.au/Council/Jobs/Current-vacancies';
        $crawler = $client->request('GET', $mainUrl);

        // Step 2: Get the iframe URL (where the job list is located)
        $iframeUrl = $crawler->filter('iframe')->attr('src');
        if (!$iframeUrl) {
            return response()->json(['message' => 'Iframe not found.']);
        }

        // Step 3: Load the iframe content
        $crawler = $client->request('GET', $iframeUrl);

        // Step 4: Extract job listings
        $jobCards = $crawler->filter('.jobs-list .jobblock');
          // Debugging step to check if job cards are correctly extracted

        // Step 5: Iterate over each job card to extract job details
        $jobCards->each(function (Crawler $node) use ($client, &$allJobs,$user) {
                // Extract job data from the node's attributes
                $title = $node->filter('.job_title')->text();

                $location = $node->filter('.location')->text();
                $jobUrl = $node->attr('data-url');
                $existingJob = Job::where('apply_url', $jobUrl)->first();
                if (!$existingJob) {
                    $closingDate = $node->attr('data-expires_at');
                    $vacancies = $node->attr('data-vacancies');

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $description = $jobCrawler->filter('#description')->html() ?? 'No description available';

                    $stateMap = [
                        'QLD' => 'Queensland',
                        'ACT' => 'Australian Capital Territory',
                        'NSW' => 'New South Wales',
                        'SA'  => 'South Australia',
                        'TAS' => 'Tasmania',
                        'VIC' => 'Victoria',
                        'WA'  => 'Western Australia',
                        'NT'  => 'Northern Territory',
                    ];

                    // Extract state abbreviation (2-3 uppercase letters)
                    preg_match('/\b([A-Z]{2,3})\b/', $location, $matches);

                    // Check if we found a match
                    if (isset($matches[1]) && array_key_exists($matches[1], $stateMap)) {
                        $stateFullName = $stateMap[$matches[1]];
                    } else {
                        // Default or fallback state if no match found
                        $stateFullName = 'New South Wales';
                    }


                    // Clean up and format closing date
                    $formattedExpiryDate = Carbon::createFromFormat('Y-m-d H:i:s O', $closingDate)->format('Y-m-d');
                    // Check if the parsed expiry date is in the past
                    if (Carbon::parse($formattedExpiryDate)->isBefore(Carbon::today())) {
                        // If the expiry date is in the past, set it to two weeks from today
                        $formattedExpiryDate = Carbon::today()->addWeeks(2)->format('Y-m-d');
                    }

                    // You can now use $formattedExpiryDate which is either the original date or updated

                    // Parse and format the expiry date
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);

                    $nominatimData = json_decode($nominatimResponse->getBody(), true);
                    if (!empty($nominatimData)) {
                        $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                        $lng = $nominatimData[0]['lon'] ?? '145.372664';
                        $exact_location = $nominatimData[0]['display_name'] ?? $location;

                    } else {
                        $lat = '-16.4614455' ;
                        $lng =  '145.372664';
                        $exact_location = $location;

                    }


                    $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                    if($stateId){
                        $sId = $stateId->id;
                    }else{
                        $sId = 3909;
                    }


                    // Prepare the job data
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => 3,
                        'company_id' => $user->company->id,
                        'company_name' => 'Byron Shire Council',
                        'apply_url' => $jobUrl,
                        'description' => $description,
                        'state_id' => $sId, // Use a default state ID if state not found
                        'vacancies' => $vacancies ?? 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'apply_on' => 'custom_url',
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1, // Adjust according to your mapping logic
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0
                    ];

                    // Save job into the database
                    $done = $this->createJobFromScrape($jobRequest);

                    $categories = [0 => "3"];
                    $done->selectedCategories()->sync($categories);

                    // Update location and other fields
                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to the allJobs array
                    $allJobs[] = $jobRequest;
                }

        });

        // Return the number of jobs found
        return response()->json([
            'message' => count($allJobs) . ' job(s) scraped from Byron Shire Council',
        ]);
    }


    // BulokeShire

    public function BulokeShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Buloke Shire Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.buloke.vic.gov.au/employment'; // Main job listing page
        $crawler = $client->request('GET', $mainUrl);

        // Step 2: Extract job listings from the page
        // Adjust the selector based on the new structure
        $jobCards = $crawler->filter('.ArticleList li'); // 'li' inside '.ArticleList' contains the job links

        // Step 3: Iterate over each job listing
        $jobCards->each(function ($node) use ($client, &$allJobs,$user) {
            // Extract job title and URL
            $title = $node->filter('.ArticleName')->text();
            $jobUrl = $node->filter('.ArticleName')->attr('href');
            $jobUrl = 'https://www.buloke.vic.gov.au' . $jobUrl; // Make sure to use the full URL


        $existingJob = Job::where('apply_url', $jobUrl)->first();
         if (!$existingJob) {


                    $jobCrawler = $client->request('GET', $jobUrl);

                    // Extract the job description using the correct selector (use your 'page_content' class or another appropriate class)
                    $description = $jobCrawler->filter('.page_content .content_holder')->html(); // Adjust based on the actual structure of the job detail page
                    $description = $description ?: 'No description available'; // If no description is found


                    $formattedExpiryDate = Carbon::today()->addWeeks(4)->format('Y-m-d');
                    $vacancies = 1;
                    $location = 'Broadway, Wycheproof VIC';

                    $stateMap = [
                        'QLD' => 'Queensland',
                        'ACT' => 'Australian Capital Territory',
                        'NSW' => 'New South Wales',
                        'SA'  => 'South Australia',
                        'TAS' => 'Tasmania',
                        'VIC' => 'Victoria',
                        'WA'  => 'Western Australia',
                        'NT'  => 'Northern Territory',
                    ];

                    // Extract state abbreviation (2-3 uppercase letters)
                    preg_match('/\b([A-Z]{2,3})\b/', $location, $matches);

                    // Check if we found a match
                    if (isset($matches[1]) && array_key_exists($matches[1], $stateMap)) {
                        $stateFullName = $stateMap[$matches[1]];
                    } else {
                        // Default or fallback state if no match found
                        $stateFullName = 'Victoria';
                    }

                    // You can now use $formattedExpiryDate which is either the original date or updated

                    // Parse and format the expiry date
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);

                    $nominatimData = json_decode($nominatimResponse->getBody(), true);


                    if (!empty($nominatimData)) {
                        $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                        $lng = $nominatimData[0]['lon'] ?? '145.372664';
                        $exact_location = $nominatimData[0]['display_name'] ?? $location;

                    } else {
                        $lat = '-16.4614455' ;
                        $lng =  '145.372664';
                        $exact_location = $location;

                    }


                    $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                    if($stateId){
                        $sId = $stateId->id;
                    }else{
                        $sId = 3909;
                    }


                    // Prepare the job data
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => 3,
                        'company_id' => $user->company->id,
                        'company_name' => 'Buloke Shire Council',
                        'apply_url' => $jobUrl,
                        'description' => $description,
                        'state_id' => $sId, // Use a default state ID if state not found
                        'vacancies' => $vacancies ?? 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'apply_on' => 'custom_url',
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1, // Adjust according to your mapping logic
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0
                    ];

                    // Save job into the database
                    $done = $this->createJobFromScrape($jobRequest);

                    $categories = [0 => "3"];
                    $done->selectedCategories()->sync($categories);

                    // Update location and other fields
                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to the allJobs array
                    $allJobs[] = $jobRequest;
            }

        });

        // Return the number of jobs found
        return response()->json([
            'message' => count($allJobs) . ' job(s) scraped from Buloke Shire Council',
        ]);
    }


    // BouliaShire

    public function BouliaShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Boulia Shire Council')->first();

        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.boulia.qld.gov.au/Council/Employment-Opportunities'; // Updated job listing page
        $crawler = $client->request('GET', $mainUrl);
        // Step 1: Extract job listings from the table with the given class
        $jobRows = $crawler->filter('.sc-responsive-table tbody tr'); // Select table rows


        // Step 2: Iterate over each row to extract position title and PDF link
        $jobRows->each(function ($node) use ($client, &$allJobs, $user) {
            // Extract the position title and PDF link from the table row
            $title = $node->filter('td')->eq(0)->text();
            $title = preg_replace('/\xA0/', ' ', $title); // Replace non-breaking space with regular space
            $title = utf8_encode($title); // Ensure the text is UTF-8 encoded

            $pdfLink = $node->filter('td')->eq(1)->filter('a')->attr('href'); // Assuming PDF link is in the second <td> and inside <a> tag
            if (strpos($pdfLink, 'http') === false) {
                $pdfLink = 'https://www.boulia.qld.gov.au' . $pdfLink;
            }

            $existingJob = Job::where('apply_url', $pdfLink)->first();
            if (!$existingJob) {
                        if ($pdfLink) {
                // Complete the PDF URL if it's relative

                // Step 3: Download and extract text from the PDF
                $pdfContent = $this->extractTextFromPdf($pdfLink);

                if ($pdfContent) {


                        $stateFullName = 'Queensland';
                        $location = 'boulia shire council';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);

                    $nominatimData = json_decode($nominatimResponse->getBody(), true);


                    if (!empty($nominatimData)) {
                        $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                        $lng = $nominatimData[0]['lon'] ?? '145.372664';
                        $exact_location = $nominatimData[0]['display_name'] ?? $location;

                    } else {
                        $lat = '-16.4614455' ;
                        $lng =  '145.372664';
                        $exact_location = $location;

                    }


                    $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                    if($stateId){
                        $sId = $stateId->id;
                    }else{
                        $sId = 3909;
                    }



                    // Prepare job data for saving
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => 3,
                        'company_id' => $user->company->id,
                        'company_name' => 'Boulia Shire Council',
                        'apply_url' => $pdfLink,
                        'description' => $pdfContent, // Use PDF content as job description
                        'state_id' => $sId, // Default state ID for Queensland (QLD)
                        'vacancies' => 1,
                        'deadline' => Carbon::today()->addWeeks(4)->format('Y-m-d'),
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'apply_on' => 'custom_url',
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1, // Adjust as necessary
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 1
                    ];


                        // Save job into the database
                        $done = $this->createJobFromScrape($jobRequest);


                    // Sync categories or other relations if necessary
                    $categories = [0 => "3"];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to the allJobs array
                    $allJobs[] = $jobRequest;
                }
            }
        }

        });

        // Return the number of jobs found
        return response()->json([
            'message' => count($allJobs) . ' job(s) scraped from Boulia Shire Council',
        ]);
    }


    // BrokenHillCity

    public function BrokenHillCity()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Broken Hill City Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.brokenhill.nsw.gov.au/Council/Careers/Positions-Vacant'; // Main job listing page
        $crawler = $client->request('GET', $mainUrl);

        // Extract job listings from the page
        $jobCards = $crawler->filter('.list-item-container'); // Assuming your class for job containers is .list-item-container

        $jobCards->each(function ($node) use ($client, &$allJobs, $user) {
            // Extract job title
            $title = $node->filter('.list-item-title')->text();
            // Extract closing date, job type, and location
            // $closingDate = $node->filter('.applications-closing')->text();
            $closingDate = Carbon::today()->addWeeks(4)->format('Y-m-d');


            $jobUrl = $node->filter('a')->attr('href');

            $jobCrawler = $client->request('GET', $jobUrl);

            // Extract the apply link from the hyperlink-button-container class
            $jobUrl = $jobCrawler->filter('.hyperlink-button-container a')->attr('href');

            // If job doesn't already exist, proceed
            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                // Go to job details page
                $jobCrawler = $client->request('GET', $jobUrl);

                // Extract the job details
                $jobDescription = $jobCrawler->filter('.job-ad-description')->html();
                $salary = $jobCrawler->filter('.job-ad-salary')->text();
                $formattedExpiryDate = Carbon::parse($closingDate)->format('Y-m-d');


                $stateFullName = 'New South Wales';
                $location = 'Broken Hill';
                $clientC = new ClientC();
                $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                $nominatimResponse = $clientC->get($nominatimUrl, [
                    'query' => [
                        'q' => $location,
                        'format' => 'json',
                        'limit' => 1
                    ],
                    'headers' => [
                        'User-Agent' => 'YourAppName/1.0'
                    ]
                ]);

            $nominatimData = json_decode($nominatimResponse->getBody(), true);

            if (!empty($nominatimData)) {
                $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                $lng = $nominatimData[0]['lon'] ?? '145.372664';
                $exact_location = $nominatimData[0]['display_name'] ?? $location;

            } else {
                $lat = '-16.4614455' ;
                $lng =  '145.372664';
                $exact_location = $location;

            }


            $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
            if($stateId){
                $sId = $stateId->id;
            }else{
                $sId = 3909;
            }





                // Prepare job data for insertion
                $jobRequest = [
                    'title' => $title,
                    'category_id' => 3,
                    'company_id' => $user->company->id,
                    'company_name' => 'Broken Hill City Council',
                    'apply_url' => $jobUrl,
                    'description' => $jobDescription,
                    'state_id' => $sId, // Default state (Victoria)
                    'vacancies' => 1,
                    'deadline' => $formattedExpiryDate,
                    'salary_mode' => 'custom',
                    'salary_type_id' => 1,
                    'custom_salary' => $salary ?: 'Competitive', // Fallback if salary is not available
                    'job_type_id' => 1,
                    'role_id' => 1,
                    'education_id' => 2,
                    'experience_id' => 4,
                    'featured' => 0,
                    'highlight' => 0,
                    'status' => 'active',
                    'ongoing' => 0,
                ];

                // Save the job to the database
                $done = $this->createJobFromScrape($jobRequest);

                // Update categories
                $categories = [0 => "3"];
                $done->selectedCategories()->sync($categories);

                $done->update([
                    'address' => $exact_location,
                    'neighborhood' => $exact_location,
                    'locality' => $exact_location,
                    'place' => $exact_location,
                    'country' => 'Australia',
                    'district' => $stateFullName, // Assuming state is NSW
                    'region' => $stateFullName, // Assuming state is NSW
                    'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                    'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                    'exact_location' => $exact_location,
                ]);

                // Add to allJobs array
                $allJobs[] = $jobRequest;
            }
        });

        // Return the number of jobs scraped
        return response()->json([
            'message' => count($allJobs) . ' job(s) scraped from Broken Hill City Council',
        ]);
    }


    // BlueMountainsCity

    public function BlueMountainsCity()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Blue Mountains City Council')->first();

        $allJobs = [];

        $client = new ClientC([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
                'Referer' => 'https://www.bmcc.nsw.gov.au'
            ]
        ]);

        $mainUrl = 'https://www.bmcc.nsw.gov.au/jobs';
        $response = $client->request('GET', $mainUrl);

        // Get the HTML content from the response body
        $htmlContent = (string) $response->getBody();

        // Pass the raw HTML to the Crawler
        $crawler = new Crawler($htmlContent);

        // Initialize an empty array to store job titles and URLs
        $jobs = [];

        // Filter the content by the .field-middle-body .field class
        $crawler->filter('.field-middle-body .field')->each(function ($node) use (&$jobs) {
            // For each .field section, find the <h3> tag
            $node->filter('h3')->each(function ($h3Node) use ($node, &$jobs) {
                // Get the job title (text inside the <h3> tag)
                $title = $h3Node->text();

                // Find the first <p> tag following this <h3> tag
                $pTag = $h3Node->nextAll()->filter('p')->first();

                // Get the anchor (<a>) tag inside the <p> tag and extract the href (URL)
                $link = $pTag->filter('a')->attr('href');
                $link = 'https://www.bmcc.nsw.gov.au' . $link;
                // Store the title and link in the $jobs array
                $jobs[] = [
                    'title' => $title,
                    'link' => $link
                ];
            });
        });


        // Step 2: Iterate over each row to extract position title and PDF link
        foreach($jobs as $job){
            $link = $job['link'];
            $title = $job['title'];
            $link = trim($link);


            $response = $client->request('GET', $link);



            // Get the HTML content from the response body
            $htmlContent = (string) $response->getBody();
            $crawler = new Crawler($htmlContent);


            $jobUrl = $crawler->filter('.related-download-link a')->count()
            ? $crawler->filter('.related-download-link a')->attr('href')
            : 'No URL Available'; // Fallback value


            $existingJob = Job::where('apply_url', $jobUrl)->first();
                if (!$existingJob) {


                    $pdfContent = $this->extractTextFromPdfForBlueMountain($jobUrl);


                        $stateFullName = 'New South Wales';
                        $location = 'Blue Mountains City Council';
                        $clientC = new ClientC();
                        $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                        $nominatimResponse = $clientC->get($nominatimUrl, [
                            'query' => [
                                'q' => $location,
                                'format' => 'json',
                                'limit' => 1
                            ],
                            'headers' => [
                                'User-Agent' => 'YourAppName/1.0'
                            ]
                        ]);

                        $nominatimData = json_decode($nominatimResponse->getBody(), true);

                        if (!empty($nominatimData)) {
                            $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                            $lng = $nominatimData[0]['lon'] ?? '145.372664';
                            $exact_location = $nominatimData[0]['display_name'] ?? $location;

                        } else {
                            $lat = '-16.4614455' ;
                            $lng =  '145.372664';
                            $exact_location = $location;

                        }


                        $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                        if($stateId){
                            $sId = $stateId->id;
                        }else{
                            $sId = 3909;
                        }



                        // Prepare job data for saving
                        $jobRequest = [
                            'title' => $title,
                            'category_id' => 3,
                            'company_id' => $user->company->id,
                            'company_name' => 'Blue Mountains City Council',
                            'apply_url' => $jobUrl,
                            'description' => $pdfContent, // Use PDF content as job description
                            'state_id' => $sId, // Default state ID for Queensland (QLD)
                            'vacancies' => 1,
                            'deadline' => Carbon::today()->addWeeks(4)->format('Y-m-d'),
                            'salary_mode' => 'custom',
                            'salary_type_id' => 1,
                            'apply_on' => 'custom_url',
                            'custom_salary' => 'Competitive',
                            'job_type_id' => 1, // Adjust as necessary
                            'role_id' => 1,
                            'education_id' => 2,
                            'experience_id' => 4,
                            'featured' => 0,
                            'highlight' => 0,
                            'status' => 'active',
                            'ongoing' => 0
                        ];


                            // Save job into the database
                            $done = $this->createJobFromScrape($jobRequest);


                        // Sync categories or other relations if necessary
                        $categories = [0 => "3"];
                        $done->selectedCategories()->sync($categories);

                        $done->update([
                            'address' => $exact_location,
                            'neighborhood' => $exact_location,
                            'locality' => $exact_location,
                            'place' => $exact_location,
                            'country' => 'Australia',
                            'district' => $stateFullName, // Assuming state is NSW
                            'region' => $stateFullName, // Assuming state is NSW
                            'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                            'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                            'exact_location' => $exact_location,
                        ]);

                        // Add to the allJobs array
                        $allJobs[] = $jobRequest;


                 }

        }

        // Return the number of jobs found
        return response()->json([
            'message' => count($allJobs) . ' job(s) scraped from Blue Mountains City Council',
        ]);
    }


    // BlacktownCity


    public function BarklyRegional()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Barkly Regional Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://www.barkly.nt.gov.au/careers/current-vacancies'; // Main job listing page
        $crawler = $client->request('GET', $mainUrl);

        // Extract job listings from the page
        $jobListings = $crawler->filter('.small-listing'); // Assuming job listings are inside .small-listing

        $jobListings->each(function ($node) use ($client, &$allJobs, $user) {

            $title = $node->filter('a')->text();
            $location = trim($node->filter('.medium-4.large-3.columns')->eq(1)->text());

            $closingDate = trim($node->filter('.medium-4.large-4.columns.files')->text());
            $closingDate = preg_replace('/^Closing\s+[A-Za-z]+\s+/', '', $closingDate);
            $formattedExpiryDate = Carbon::parse($closingDate)->format('Y-m-d');


            $jobUrl = $node->filter('a')->attr('href');

            // Check if the job already exists in the database
            $existingJob = Job::where('apply_url', $jobUrl)->first();

            if (!$existingJob) {

                // Go to the job details page
                $jobCrawler = $client->request('GET', $jobUrl);

                // Extract the job description (from large-9 columns)
                $jobDescription = $jobCrawler->filter('.large-9.columns')->html(); // Get the HTML content of the job description


                $stateFullName = 'Northern Territory';
                $clientC = new ClientC();
                $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                $nominatimResponse = $clientC->get($nominatimUrl, [
                    'query' => [
                        'q' => $location,
                        'format' => 'json',
                        'limit' => 1
                    ],
                    'headers' => [
                        'User-Agent' => 'YourAppName/1.0'
                    ]
                ]);

            $nominatimData = json_decode($nominatimResponse->getBody(), true);
            if (!empty($nominatimData)) {
                $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                $lng = $nominatimData[0]['lon'] ?? '145.372664';
                $exact_location = $nominatimData[0]['display_name'] ?? $location;

            } else {
                $lat = '-16.4614455' ;
                $lng =  '145.372664';
                $exact_location = $location;

            }


            $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();

            if($stateId){
                $sId = $stateId->id;
            }else{
                $sId = 3909;
            }


                // Prepare job data for insertion
                $jobRequest = [
                    'title' => $title,
                    'category_id' => 3,
                    'company_id' => $user->company->id,
                    'company_name' => 'Barkly Regional Council',
                    'apply_url' => $jobUrl,
                    'description' => $jobDescription,
                    'state_id' => $sId, // Default state (Victoria)
                    'vacancies' => 1,
                    'deadline' => $formattedExpiryDate,
                    'salary_mode' => 'custom',
                    'salary_type_id' => 1,
                    'custom_salary' => 'Competitive', // Fallback if salary is not available
                    'job_type_id' => 1,
                    'role_id' => 1,
                    'education_id' => 2,
                    'experience_id' => 4,
                    'featured' => 0,
                    'highlight' => 0,
                    'status' => 'active',
                    'ongoing' => 0,
                ];

                // Save the job to the database
                $done = $this->createJobFromScrape($jobRequest);

                // Update categories
                $categories = [0 => "3"];
                $done->selectedCategories()->sync($categories);

                $done->update([
                    'address' => $exact_location,
                    'neighborhood' => $exact_location,
                    'locality' => $exact_location,
                    'place' => $exact_location,
                    'country' => 'Australia',
                    'district' => $stateFullName, // Assuming state is NSW
                    'region' => $stateFullName, // Assuming state is NSW
                    'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                    'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                    'exact_location' => $exact_location,
                ]);

                // Add to allJobs array
                $allJobs[] = $jobRequest;
            }
        });

        // Return the number of jobs scraped
        return response()->json([
            'message' => count($allJobs) . ' job(s) scraped from Barkly Regional Council',
        ]);
    }


    // BananaShire

    public function BananaShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Banana Shire Council')->first();

        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.banana.qld.gov.au/jobs-council/job-vacancies-1'; // Job listing page URL
        $crawler = $client->request('GET', $mainUrl);

        // Step 1: Extract job listings from the table with the given class
        $jobRows = $crawler->filter('.editor table tbody tr'); // Select rows in the table

        // Step 2: Iterate over each row to extract position title, location, job type, closing date, and PDF link
        $jobRows->each(function ($node) use ($client, &$allJobs, $user) {


               // Extract the PDF link (assuming it's inside a link in the first column)
               $pdfLink = $node->filter('td')->eq(0)->filter('a')->attr('href'); // Assuming PDF link is in the first <td> and inside <a> tag
               if (strpos($pdfLink, 'http') === false) {
                   $pdfLink = 'https://www.banana.qld.gov.au' . $pdfLink; // Make the URL absolute if it's relative
               }

               $existingJob = Job::where('apply_url', $pdfLink)->first();
          if (!$existingJob)  {
                    $title = $node->filter('td')->eq(0)->text();
                    $title = preg_replace('/\xA0/', ' ', $title); // Replace non-breaking space with regular space
                    $title = utf8_encode($title); // Ensure the text is UTF-8 encoded

                    // Extract location (2nd column)
                    $location = $node->filter('td')->eq(1)->text();
                    $location = trim($location); // Remove any extra spaces

                    $closingDate = $node->filter('td')->eq(3)->text();
                    $closingDate = trim($closingDate); // Remove extra spaces

                if($closingDate == 'Open'){
                    $formattedExpiryDate = Carbon::today()->addWeeks(4)->format('Y-m-d');
                }else{
                    $formattedExpiryDate = Carbon::parse($closingDate)->format('Y-m-d');
                }

                     $stateFullName = 'Queensland';
                    $location = 'boulia shire council';

                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);

                    $nominatimData = json_decode($nominatimResponse->getBody(), true);

                    if (!empty($nominatimData)) {
                        $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                        $lng = $nominatimData[0]['lon'] ?? '145.372664';
                        $exact_location = $nominatimData[0]['display_name'] ?? $location;

                    } else {
                        $lat = '-16.4614455' ;
                        $lng =  '145.372664';
                        $exact_location = $location;

                    }


                    $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                    if($stateId){
                        $sId = $stateId->id;
                    }else{
                        $sId = 3909;
                    }

                    $description = '<div class="job-desc">
                        <p><strong>ABOUT COUNCIL</strong><br><strong>Our Vision</strong></p><p>â€œShire of Opportunityâ€<br>To improve the quality of life for our communities through the delivery of efficient, effective and sustainable<br>services and facilities.</p><p><strong>Our Mission</strong></p><p>Our Council is committed to promoting and striving for continuous improvement in all that we do, for the benefit and growth of the whole of our Shire.</p><p><strong>Our Values</strong></p><p>â€¢ Advocacy for our people<br>â€¢ Effective and responsive leadership<br>â€¢ Integrity and mutual respect<br>â€¢ Honesty, equity and consistency in all aspects of Councilâ€™s operations<br>â€¢ Quality of service to our citizens<br>â€¢ Work constructively together, in the spirit of teamwork<br>â€¢ Sustainable growth and development</p><p><strong>GENERAL POSITION INFORMATION</strong></p><p>To assist in the implementation, coordination and promotion of Councilâ€™s Safety Management System in<br>accordance with legislative requirements and Council policies.</p><p><strong>TO APPLY</strong></p><p>Submit the following documentation via email or in person:</p><p>âš« Application for Employment<br>âš« Cover Letter<br>âš« Resume<br>âš« Copies of any relevant Qualifications/Tickets/Licences are not required â€“<br>please include details in the application form.</p><p>Your cover letter should outline qualifications, education and licences as well as abilities, skills and knowledge found on page two of the position description.</p><p>Ensure you provide relevant examples where you have demonstrated your<br>ability to perform the duties and responsibilities required in the position<br>description.</p><p>Email: enquiries@banana.qld.gov.au<br>In person: Banana Shire Council Admin Office, 62 Valentine Plains Road, Biloela</p>
                    </div>';



                    // Prepare job data for saving
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => 3,
                        'company_id' => $user->company->id,
                        'company_name' => 'Banana Shire Council',
                        'apply_url' => $pdfLink,
                        'description' => $description, // Use PDF content as job description
                        'state_id' => $sId, // Default state ID for Queensland (QLD)
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'apply_on' => 'custom_url',
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1, // Adjust as necessary
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 1
                    ];


                        // Save job into the database
                        $done = $this->createJobFromScrape($jobRequest);


                    // Sync categories or other relations if necessary
                    $categories = [0 => "3"];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);
                    // Add to the allJobs array
                    $allJobs[] = $jobRequest;


        }

        });

        // Return the number of jobs found
        return response()->json([
            'message' => count($allJobs) . ' job(s) scraped from Banana Shire Council',
        ]);
    }


    // Alice Springs Town Coun­cil


    public function AliceSprings()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        // $user = User::where('name', 'Broken Hill City Council')->first();
        $user = User::where('name', 'Alice Springs Town Coun­cil')->first();

        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://alicesprings.nt.gov.au/council/opportunities/jobs'; // Main job listing page
        // Make a request to the main job listing page
        $crawler = $client->request('GET', $mainUrl);

        // Extract all job listing cards
        $jobCards = $crawler->filter('.jobs .grid-x .cell');  // Target individual job containers
        $allJobs = [];
        // Iterate over each job listing
        $jobCards->each(function ($node) use ($client, &$allJobs, $user) {
            // Extract job title
            $jobUrl = $node->filter('.cell h3 a')->attr('href');
            $existingJob = Job::where('apply_url', $jobUrl)->first();

            if (!$existingJob) {

                $title = $node->filter('.cell .wrapper h3 a')->text();
                $data = $node->filter('.cell .meta')->html();
                preg_match('/<b>Closes:<\/b>(.*?)<br>/s', $data, $closingDateMatches);
                $closingDate = isset($closingDateMatches[1]) ? trim($closingDateMatches[1]) : 'Not Available';

                if ($closingDate === 'Not Available') {
                    $formattedExpiryDate = Carbon::now()->addWeeks(4)->format('Y-m-d');
                } else {
                    // If closing date is available, format it as Y-m-d
                    $formattedExpiryDate = Carbon::parse($closingDate)->format('Y-m-d');
                }

                $jobCrawler = $client->request('GET', $jobUrl);

                $jobDescription = $jobCrawler->filter('.content-blocks')->html();




                    $stateFullName = 'Northern Territory';
                    $location = 'Alice Springs Town Coun­cil';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);

                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => 3,
                        'company_id' => $user->company->id,
                        'company_name' => 'Alice Springs Town Council',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId, // Default state (Victoria)
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive', // Fallback if salary is not available
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];

                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => "3"];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
                    $allJobs[] = $jobRequest;
            }


        });

        // Return the number of jobs scraped
        return response()->json([
        'message' => count($allJobs) . ' job(s) scraped from Alice Springs Town Council',
        ]);
    }


    //  CardiniaShire

    public function CardiniaShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Cardinia Shire Council')->first();

        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://careers.cardinia.vic.gov.au/our-jobs';

        $crawler = $client->request('GET', $mainUrl);

        $jobCards = $crawler->filter('.page-central .adlogic_job_results .position');  // Target individual job containers
        $allJobs = [];

        $jobCards->each(function ($node) use ($client, &$allJobs, $user) {


            $jobUrl = $node->filter('.job-header-container h1 a')->attr('href');
            if($jobUrl == '{job_link}'){
                return;
            }
            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                $title = $node->filter('.job-header-container h1 a')->text();

                // Extract category
                $category = $node->filter('.ajb_classification li a')->text();
                $formattedExpiryDate = Carbon::today()->addWeeks(4)->format('Y-m-d');

                $categories = JobCategory::with('translations')->get();


                $categoryWords = explode(' ', strtolower($category));

                $categoryId = 3;

                foreach ($categories as $cat) {
                    // Get the translated name of the category (in English for example)
                    $catName = strtolower($cat->translations->first()->name);
                    $catWords = explode(' ', $catName);

                    if (array_intersect($categoryWords, $catWords)) {
                        $categoryId = $cat->id; // Set the matched category ID
                        break;
                    }
                }

                $jobDescription = '<div class="container my-5">
                    <h2 class="text-center mb-4">Our Recruitment Process</h2>
                    <p>We know it takes time to apply for a new role, and we want to make it as easy as possible for you to join our team. However, there are a few key steps in our process to ensure we are the right fit for you. Below is an overview of our recruitment process, so you know what to expect when applying for a position with us.</p>
                    <p>Cardinia’s recruitment process is a crucial part of our mission to create an inclusive workplace with teams made up of individuals from diverse backgrounds who are united by shared purpose and values.</p>
                    <p>Every role is different, so while the steps and timelines may vary, one thing remains the same: we can’t wait to meet you!</p>

                    <div class="accordion" id="recruitmentProcess">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Step 1 – Discover the right opportunity for you
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#recruitmentProcess">
                                <div class="accordion-body">
                                    Submit your application or expression of interest on our Current Vacancies page. If you have any support or access requirements, please let us know at the time you apply for a position by contacting our People and Culture team on 1300 787 624 or through the details listed below.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Step 2 – Submit your application
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#recruitmentProcess">
                                <div class="accordion-body">
                                    Please ensure you include a cover letter and an updated CV with your application. We’d love to learn more about you and why you believe you’ll be a great fit for our team.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Step 3 – Shortlisting applications
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#recruitmentProcess">
                                <div class="accordion-body">
                                    If you’re shortlisted, a member of our People and Culture team or the Hiring Manager will invite you to an interview, which may be held in person or online. This process typically takes around 14 days, but the timeframe may vary depending on the number of applications received. We understand the time and effort it takes to apply and interview, so we promise to respond to all applicants, no matter the outcome.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Step 4 – Prepare for your interview
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#recruitmentProcess">
                                <div class="accordion-body">
                                    Think of your previous experience in work situations and prepare specific examples of how you’ve handled those situations in a positive way, using the STAR method. This is a template for answering key selection criteria or interview questions. <br>
                                    <strong>STAR stands for:</strong><br>
                                    <ul>
                                        <li><strong>Situation:</strong> Explain the context.</li>
                                        <li><strong>Task:</strong> Explain the task and how you used your skills to complete it, or explain the problem and how you solved it.</li>
                                        <li><strong>Action:</strong> Explain what you did to achieve the goal or solve the problem.</li>
                                        <li><strong>Result:</strong> Explain the end result and its positive impact.</li>
                                    </ul>
                                    Remember, the interview is a two-way process, so make sure to prepare your own questions.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    Step 5 – Time for your interview!
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#recruitmentProcess">
                                <div class="accordion-body">
                                    Make sure you plan ahead to arrive at your interview on time. If your interview is taking place at our office at 20 Siding Ave, Officer VIC 3809, make a plan for how you’ll get here and how much time it will take you. Otherwise, if your interview is a virtual meeting, check you have the correct meeting software installed and the joining link works. <br><br>
                                    At your interview, you’ll be meeting with a selection panel, typically comprised of two to three people, with gender balance where possible. They will ask you a series of questions and may ask you to participate in other selection techniques, such as case studies/role-play, work samples, aptitude and ability. You will also have an opportunity to ask questions.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSix">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                    Step 6 – After the interview
                                </button>
                            </h2>
                            <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#recruitmentProcess">
                                <div class="accordion-body">
                                    You may be invited to come in for a second interview. If unsuccessful, after an interview, the Hiring Manager will contact you and provide feedback. <br><br>
                                    If you are the preferred applicant, the Hiring Manager will discuss the next stage, which includes pre-employment checks such as reference and National Police checks. Depending on your role, you may also need to undertake a Working with Children check or Pre-Employment medicals. This process can take between 4 – 10 days.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSeven">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                    Step 7 – Congratulations and welcome to the Cardinia team!
                                </button>
                            </h2>
                            <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#recruitmentProcess">
                                <div class="accordion-body">
                                    Welcome aboard! Our People and Culture Team will issue your contract. Once you accept, a formal letter of offer and new starter information is sent electronically and must be completed and returned prior to your first day of work. If you’d like to see what your future team is up to, you can follow us on Facebook, Instagram or LinkedIn.<br><br>
                                    Or, if you weren’t successful on this occasion but are still interested in joining the team, you can join our Talent Pool to be considered for future opportunities.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ';


                    $stateFullName = 'Victoria';
                    $location = 'Cardinia Shire Council';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);

                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Cardinia Shire Council',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId, // Default state (Victoria)
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive', // Fallback if salary is not available
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];

                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
                    $allJobs[] = $jobRequest;
            }


        });

        // Return the number of jobs scraped
        return response()->json([
        'message' => count($allJobs) . ' job(s) scraped from Cardinia Shire Council',
        ]);
    }


    // CentralLand


    public function CentralLand()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        // $user = User::where('name', 'Cardinia Shire Council')->first();
        $user = User::where('username', 'Central-Land')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://careers.clc.org.au';

        $crawler = $client->request('GET', $mainUrl);

        $jobCards = $crawler->filter('.nav-tabs li');  // Target individual job containers
        $allJobs = [];
        $jobCards->each(function ($node) use ($client, &$allJobs, $user) {


            $jobUrl = $node->filter('a')->attr('href'); // Extract 'href' from <a> tag

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                 // Get the job title
                    $title = $node->filter('span.span9')->text();
                    $title = preg_replace('/^[A-Z]{2,}\d{2,}\s+-\s+/i', '', $title);

                    $closingDateText = $node->filter('.span3')->text();
                    preg_match('/Closing Date: ([\d\/]+)/', $closingDateText, $matches);
                    $formattedExpiryDate = isset($matches[1]) ? Carbon::createFromFormat('d/m/Y', $matches[1])->format('Y-m-d') : null;

                    $location = $node->filter('.clearfix + div')->text();

                    $categoryId = 3;

                    $jobDescription = "<div>
                        <ul>
                            <li>Competitive salary and benefits package, five weeks annual leave plus paid Christmas shutdown</li>
                            <li>Temporary accommodation and relocation assistance available</li>
                            <li>Professional development opportunities available</li>
                        </ul>
                        <p><strong>OUR STORY</strong></p>
                        <p>The Central Land Council (CLC) is a corporate Commonwealth entity established under the <em>Aboriginal Land Rights (Northern Territory) Act 1976</em>. The CLC represents traditional landowners, native title holders and other Aboriginal people in the southern half of the Northern Territory—an area of almost 780,000 square kilometres.&nbsp;</p>
                        <p>The CLC provides its constituents with advice, advocacy and practical assistance to support their aspirations, manage their land and realise and protect their rights.</p>
                        <p>&nbsp;</p>
                        <p><strong>AFFIRMATIVE ACTION PLAN</strong></p>
                        <p>Eligible Aboriginal applicants will be granted priority consideration for this vacancy. If an Aboriginal applicant is selected, the remaining non-Aboriginal applicants will not be assessed.</p>
                        <p>Applicants must have relevant qualifications and demonstrate that they meet essential criteria in order to be considered.&nbsp; An applicant selected under this affirmative action plan will be required to provide evidence of their eligibility prior to commencement, such as:</p>
                        <ul>
                            <li>completed statutory declaration form, or</li>
                            <li>supporting statement from an appropriate Aboriginal organisation</li>
                        </ul>
                        <p>&nbsp;</p>
                        <p><strong>BENEFITS</strong></p>
                        <ul>
                            <li>Attractive base salary plus 15.4% superannuation</li>
                            <li>Generous salary packaging (maximum $29,000 annually, depending on individual circumstances);</li>
                            <li>Ongoing district&nbsp;allowance (circa $3,640 for an individual or $6,660 with dependents);</li>
                            <li>Yearly airfare allowance (circa $1,300;)</li>
                            <li>Relocation assistance, should you be moving to the region; and</li>
                            <li>Subsidised, fully furnished accommodation&nbsp;for the first four months.&nbsp;</li>
                        </ul>
                        <p>&nbsp;</p>
                        <p>The Central Land Council&nbsp;is&nbsp;dedicated to delivering&nbsp;ongoing professional development and career progression for its people.&nbsp;You'll have the opportunity to undertake professional development and to take part in a number of new projects as the organisation continues to grow and innovate. With this in mind, applications are invited from experienced legal secretaries, career paralegals, and law students with administrative experience.</p>
                        <p>Most importantly, this role will allow you to work in a&nbsp;diverse environment&nbsp;where you affect real change.&nbsp;</p>
                        <p>&nbsp;</p>
                        <p><strong>MANDATORY REQUIREMENTS</strong></p>
                        <ul>
                            <li>Selection Criteria Summary</li>
                            <li>Ochre card (working with vulnerable people check)</li>
                            <li>National police clearance</li>
                            <li>Driver's licence</li>
                        </ul>
                        <p>&nbsp;</p>
                        <p><strong>CONTACT DETAILS</strong></p>
                        <p>If you're interested in using your skills&nbsp;to make a real difference&nbsp;<strong>apply now!</strong></p>
                        <p>For further information about this role, please contact&nbsp;Emily Ryan&nbsp;on 8951 6211.</p>
                        <p>For more information about the application process please contact Jess Howard (Human Resources Advisor)&nbsp;on 08 8951 6211 or&nbsp;<a href='mailto:jobs@clc.org.au'>jobs@clc.org.au</a>.</p>
                        <p>&nbsp;</p>
                        <p><em>Total effective package includes: base salary, district allowance, superannuation, leave loading, relocation assistance, annual airfare allowance and salary packaging options. Annual progression within the salary scale is subject to satisfactory performance. Progression is in accordance with annual increments set out in an enterprise agreement.</em></p>
                        <p><em>The filling of this vacancy is an affirmative measure under section 8(1) of the Racial Discrimination Act 1975.</em></p>
                        <br><br>
                    </div>";


                    $stateFullName = 'Northern Territory';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Central Land Council',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId, // Default state (Victoria)
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive', // Fallback if salary is not available
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
                    $allJobs[] = $jobRequest;
            }


        });

        // Return the number of jobs scraped
        return response()->json([
        'message' => count($allJobs) . ' job(s) scraped from Central Land Council',
        ]);
    }

    //  CityBallarat

    public function CityBallarat()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'City of Ballarat')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://careers.ballarat.vic.gov.au/job/job_search_result.cfm';
        $client = new Client();
        $allJobs1 = []; // Array to store all job details

        function scrapeJobs($client, $mainUrl, &$allJobs) {
            $crawler = $client->request('GET', $mainUrl);
            do {
                // Extract job rows where title and apply links are present
                $crawler->filter('tr')->each(function (Crawler $node) use (&$allJobs) {
                    // Check if this row contains a job title
                    if ($node->filter('.clsJobTitle')->count() > 0) {
                        $title = $node->filter('.clsJobTitle')->text();
                        $applyLink = $node->filter('.clsJobTitle')->attr('href');

                        $allJobs[] = [
                            'title' => trim($title),
                            'apply_link' => 'https://careers.ballarat.vic.gov.au' . trim($applyLink),
                        ];
                    }
                });

                // Check if the 'Next 10' button exists to paginate
                $nextButton = $crawler->filter('#btnNextBottom');
                if ($nextButton->count() > 0) {
                    // Simulate a click on the "Next 10" button
                    $form = $nextButton->form();
                    $crawler = $client->submit($form);
                } else {
                    break; // Exit loop when no "Next 10" button exists
                }
            } while (true);
        }
        scrapeJobs($client, $mainUrl, $allJobs);



        foreach($allJobs as $job) {


            $title = $job['title'];

            $formattedExpiryDate = Carbon::today()->addWeeks(6)->format('Y-m-d');

            $jobUrl = $job['apply_link'];

            $existingJob = Job::where('apply_url', 'like', $jobUrl . '%')->first();

            if (!$existingJob) {

                $jobCrawler = $client->request('GET', $jobUrl);

                $jobDescription = $jobCrawler->filter('table .detailsBG')->html();


                $stateFullName = 'Victoria';
                $location = 'City of Ballarat';
                $clientC = new ClientC();
                $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                $nominatimResponse = $clientC->get($nominatimUrl, [
                    'query' => [
                        'q' => $location,
                        'format' => 'json',
                        'limit' => 1
                    ],
                    'headers' => [
                        'User-Agent' => 'YourAppName/1.0'
                    ]
                ]);

            $nominatimData = json_decode($nominatimResponse->getBody(), true);
            if (!empty($nominatimData)) {
                $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                $lng = $nominatimData[0]['lon'] ?? '145.372664';
                $exact_location = $nominatimData[0]['display_name'] ?? $location;

            } else {
                $lat = '-16.4614455' ;
                $lng =  '145.372664';
                $exact_location = $location;

            }


            $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
            if($stateId){
                $sId = $stateId->id;
            }else{
                $sId = 3909;
            }


                // Prepare job data for insertion
                $jobRequest = [
                    'title' => $title,
                    'category_id' => 3,
                    'company_id' => $user->company->id,
                    'company_name' => 'City of Ballarat',
                    'apply_url' => $jobUrl,
                    'description' => $jobDescription,
                    'state_id' => $sId,
                    'vacancies' => 1,
                    'deadline' => $formattedExpiryDate,
                    'salary_mode' => 'custom',
                    'salary_type_id' => 1,
                    'custom_salary' => 'Competitive',
                    'job_type_id' => 1,
                    'role_id' => 1,
                    'education_id' => 2,
                    'experience_id' => 4,
                    'featured' => 0,
                    'highlight' => 0,
                    'status' => 'active',
                    'ongoing' => 0,
                ];

                // Save the job to the database
                $done = $this->createJobFromScrape($jobRequest);

                // Update categories
                $categories = [0 => "3"];
                $done->selectedCategories()->sync($categories);

                $done->update([
                    'address' => $exact_location,
                    'neighborhood' => $exact_location,
                    'locality' => $exact_location,
                    'place' => $exact_location,
                    'country' => 'Australia',
                    'district' => $stateFullName, // Assuming state is NSW
                    'region' => $stateFullName, // Assuming state is NSW
                    'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                    'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                    'exact_location' => $exact_location,
                ]);

                // Add to allJobs array
                $allJobs1[] = $jobRequest;
            }
        }

        // Return the number of jobs scraped
        return response()->json([
            'message' => count($allJobs1) . ' job(s) scraped from City of Ballarat',
        ]);
    }



    //         City of Salisbury


    public function CitySalisbury()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'City of Salisbury')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://jobs.salisbury.sa.gov.au/job/job_search_result.cfm';
        $client = new Client();
        $allJobs1 = []; // Array to store all job details

        function scrapeJobsCitySalisbury($client, $mainUrl, &$allJobs) {
            $crawler = $client->request('GET', $mainUrl);
            do {
                // Extract job rows where title and apply links are present
                $crawler->filter('.jobSearchResult tr')->each(function (Crawler $node) use (&$allJobs) {
                    // Check if this row contains a job title
                    if ($node->filter('.clsJobTitle')->count() > 0) {
                        $title = $node->filter('.clsJobTitle')->text();
                        $applyLink = $node->filter('.clsJobTitle')->attr('href');

                        $allJobs[] = [
                            'title' => trim($title),
                            'apply_link' => 'https://jobs.salisbury.sa.gov.au' . trim($applyLink),
                        ];
                    }
                });

                // Check if the 'Next 10' button exists to paginate
                $nextButton = $crawler->filter('#btnNextBottom');
                if ($nextButton->count() > 0) {
                    // Simulate a click on the "Next 10" button
                    $form = $nextButton->form();
                    $crawler = $client->submit($form);
                } else {
                    break; // Exit loop when no "Next 10" button exists
                }
            } while (true);
        }
        scrapeJobsCitySalisbury($client, $mainUrl, $allJobs);


        foreach($allJobs as $job) {

            $title = $job['title'];

            $formattedExpiryDate = Carbon::today()->addWeeks(6)->format('Y-m-d');

            $jobUrl = $job['apply_link'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                $jobCrawler = $client->request('GET', $jobUrl);

                $jobDescription = $jobCrawler->filter('.jobDetailsBody .detailsBG')->html();


                $stateFullName = 'South Australia';
                $location = 'City of Salisbury';
                $clientC = new ClientC();
                $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                $nominatimResponse = $clientC->get($nominatimUrl, [
                    'query' => [
                        'q' => $location,
                        'format' => 'json',
                        'limit' => 1
                    ],
                    'headers' => [
                        'User-Agent' => 'YourAppName/1.0'
                    ]
                ]);

            $nominatimData = json_decode($nominatimResponse->getBody(), true);

            if (!empty($nominatimData)) {
                $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                $lng = $nominatimData[0]['lon'] ?? '145.372664';
                $exact_location = $nominatimData[0]['display_name'] ?? $location;

            } else {
                $lat = '-16.4614455' ;
                $lng =  '145.372664';
                $exact_location = $location;

            }


            $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
            if($stateId){
                $sId = $stateId->id;
            }else{
                $sId = 3909;
            }


                // Prepare job data for insertion
                $jobRequest = [
                    'title' => $title,
                    'category_id' => 3,
                    'company_id' => $user->company->id,
                    'company_name' => 'City of Salisbury',
                    'apply_url' => $jobUrl,
                    'description' => $jobDescription,
                    'state_id' => $sId,
                    'vacancies' => 1,
                    'deadline' => $formattedExpiryDate,
                    'salary_mode' => 'custom',
                    'salary_type_id' => 1,
                    'custom_salary' => 'Competitive',
                    'job_type_id' => 1,
                    'role_id' => 1,
                    'education_id' => 2,
                    'experience_id' => 4,
                    'featured' => 0,
                    'highlight' => 0,
                    'status' => 'active',
                    'ongoing' => 0,
                ];

                // Save the job to the database
                $done = $this->createJobFromScrape($jobRequest);

                // Update categories
                $categories = [0 => "3"];
                $done->selectedCategories()->sync($categories);

                $done->update([
                    'address' => $exact_location,
                    'neighborhood' => $exact_location,
                    'locality' => $exact_location,
                    'place' => $exact_location,
                    'country' => 'Australia',
                    'district' => $stateFullName, // Assuming state is NSW
                    'region' => $stateFullName, // Assuming state is NSW
                    'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                    'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                    'exact_location' => $exact_location,
                ]);

                // Add to allJobs array
                $allJobs1[] = $jobRequest;
            }
        }

        // Return the number of jobs scraped
        return response()->json([
            'message' => count($allJobs1) . ' job(s) scraped from City of Salisbury',
        ]);
    }


    //         Charters Towers Region

    public function ChartersTowers()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Charters Towers Regional Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.charterstowers.qld.gov.au/current-opportunities';

        $crawler = $client->request('GET', $mainUrl);

        $jobCards = $crawler->filter('.directory__list li');  // Target individual job containers
        $allJobs = [];
        $jobCards->each(function ($node) use ($client, &$allJobs, $user) {


            $jobUrl = $node->filter('.directory__link')->attr('href'); // Extract 'href' from <a> tag
            $jobUrl = 'https://www.charterstowers.qld.gov.au' . $jobUrl;
            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                 // Get the job title
                    $title = $node->filter('.directory__title')->text();

                    $formattedExpiryDate = Carbon::today()->addWeeks(6)->format('Y-m-d');


                    $categoryId = 3;


                    $jobCrawler = $client->request('GET', $jobUrl);

                    $jobDescription = $jobCrawler->filter('.directory-detail')->html();


                    $location = 'Charters Towers';
                    $stateFullName = 'Queensland';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Charters Towers Regional Council',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId, // Default state (Victoria)
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive', // Fallback if salary is not available
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
                    $allJobs[] = $jobRequest;
            }


        });

        // Return the number of jobs scraped
        return response()->json([
        'message' => count($allJobs) . ' job(s) scraped from Charters Towers Regional Council',
        ]);
    }


    // GreaterBendigo


    public function GreaterBendigo()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'City of Greater Bendigo')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://city-of-bendigo.applynow.net.au';

        $crawler = $client->request('GET', $mainUrl);

        $jobNodes = $crawler->filter('#joblist > div'); // Target job entries inside #joblist


        $jobNodes->each(function ($node) use (&$allJobs) {
            try {
                // Extract data
                $jobTitle = $node->filter('a.job_title')->text('');
                $jobUrl = $node->filter('a.job_title')->attr('href');
                $expiresRaw = $node->filter('span.expires')->text('');
                $location = $node->filter('span.location')->text('');

                // Format the expiry date
                $closeDate = Carbon::parse($expiresRaw); // Parse the raw date
                $currentDate = Carbon::now();

                // If close date is within a week, extend by 3 weeks
                if ($closeDate->diffInDays($currentDate) < 7) {
                    $closeDate = $currentDate->addWeeks(3);
                }

                $formattedExpiryDate = $closeDate->format('Y-m-d');

                // Append job to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $jobUrl,
                    'expires' => $formattedExpiryDate,
                    'location' => $location,
                ];
            } catch (\Exception $e) {
                // Handle missing data or parsing errors
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                 // Get the job title
                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = $job['location'];


                    $categoryId = 3;


                    $jobCrawler = $client->request('GET', $jobUrl);

                    $jobDescription = $jobCrawler->filter('#description')->html();

                    $stateFullName = 'Victoria';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'City of Greater Bendigo',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId, // Default state (Victoria)
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive', // Fallback if salary is not available
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => count($allJobs) . ' job(s) scraped from City of Greater Bendigo Council',
        ]);
    }


    // City of Greater Dandenong

    public function GreaterDandenong()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'City of Greater Dandenong')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://jobs.greaterdandenong.vic.gov.au/jobs';

        $crawler = $client->request('GET', $mainUrl);

        $jobNodes = $crawler->filter('.views-row'); // Target job entries

        $allJobs = []; // Initialize an array to store the jobs

        $jobNodes->each(function ($node) use (&$allJobs) {
            try {
                // Extract job title
                $jobTitle = $node->filter('.views-field-title .field-content a')->text();

                // Extract job URL
                $jobUrl = $node->filter('.views-field-title .field-content a')->attr('href');

                // Extract expiry date
                $expiresRaw = $node->filter('.deadline time')->attr('datetime');

                // Extract location
                $location = $node->filter('.location')->text('');

                // Format the expiry date
                $closeDate = Carbon::parse($expiresRaw);
                $currentDate = Carbon::now();

                // If close date is within a week, extend by 3 weeks
                if ($closeDate->diffInDays($currentDate) < 7) {
                    $closeDate = $currentDate->addWeeks(3);
                }

                $formattedExpiryDate = $closeDate->format('Y-m-d');

                // Append job to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $jobUrl,
                    'expires' => $formattedExpiryDate,
                    'location' => $location,
                ];
            } catch (\Exception $e) {
                // Handle missing data or parsing errors
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });

        // Print extracted jobs for debugging
        // dd($allJobs);
        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();

            if (!$existingJob) {

                $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = $job['location'];


                    $categoryId = 3;


                    $jobCrawler = $client->request('GET', $jobUrl);

                    $jobDescription = $jobCrawler->filter('.field--type-text-with-summary')->html();

                    $stateFullName = 'Victoria';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'City of Greater Dandenong',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from City of Greater Dandenong Council',
        ]);
    }



    // GreaterGeraldton

    public function GreaterGeraldton()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'City of Greater Geraldton')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.cgg.wa.gov.au/employment';

        $crawler = $client->request('GET', $mainUrl);

        $jobRows = $crawler->filter('.employment-container .module-list .table tr');
        $allJobs = []; // Initialize an array to store the jobs

            // Iterate through the rows, skipping the first row (header row)
        $jobRows->each(function ($row, $index) use (&$allJobs) {
            if ($index === 0) {
                // Skip the header row
                return;
            }

            try {
                // Extract job title
                $jobTitle = $row->filter('td a')->text();

                // Extract job URL
                $jobUrl = $row->filter('td a')->attr('href');
                $jobUrl = 'https://www.cgg.wa.gov.au' . $jobUrl;
                // Extract application close date
                $expiresRaw = $row->filter('td.table-col-right')->text();

                // Parse and format the expiry date
                $closeDate = Carbon::createFromFormat('d/m/Y g:i:s A', trim($expiresRaw));
                $formattedExpiryDate = $closeDate->format('Y-m-d');

                // Append the job to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $jobUrl,
                    'expires' => $formattedExpiryDate,
                ];
            } catch (\Exception $e) {

            }
        });

        // Print extracted jobs for debugging
        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'City of Greater Geraldton';


                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.data-container');

                    // Remove the table from the container
                    $dataContainer->filter('table')->each(function ($tableNode) {
                        // Unset or ignore the table HTML
                        $tableNode->getNode(0)->parentNode->removeChild($tableNode->getNode(0));
                    });

                    // Get the remaining HTML content as the job description
                    $jobDescription = $dataContainer->html();

                    // Debug or use the job description
                    $stateFullName = 'Western Australia';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'City of Greater Geraldton',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from City of Greater Geraldton Council',
        ]);
    }


    // CityHobart

    public function CityHobart()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'City of Hobart')->first();
        $allJobs = [];
        $client = new Client();

     // Define the main URL
        $mainUrl = 'https://www.hobartcity.com.au/Council/Careers/Our-current-vacancies';

        // Fetch the page content
        $crawler = $client->request('GET', $mainUrl);


        $jobItems = $crawler->filter('.list-container .list-item-container');

        $allJobs = []; // Initialize an array to store the jobs

        // Iterate through each job item
        $jobItems->each(function ($item) use (&$allJobs) {
            // try {
                // Extract job URL

                $jobUrl = $item->filter('a')->attr('href');

                // Extract job title
                $jobTitle = trim($item->filter('h2.list-item-title')->text());
                // Extract application close date
                $expiresRaw = $item->filter('p.applications-closing')->text();


                preg_match('/Applications closing on (.+)/', $expiresRaw, $matches);
                $closeDate = isset($matches[1]) ? Carbon::parse(trim($matches[1]))->format('Y-m-d') : null;
                // Extract job type

                // Extract job description

                // Append the job to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $jobUrl,
                    'expires' => $closeDate,
                ];
            // } catch (\Exception $e) {
            //     // Handle missing data or parsing errors
            //     // error_log("Error parsing job: " . $e->getMessage());
            // }
        });

        // Debug the extracted jobs

        // Print extracted jobs for debugging
        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();

            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Hobart';


                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('#description');

                    $jobDescription = $dataContainer->html();

                    // Debug or use the job description
                    $stateFullName = 'Tasmania';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'City of Hobart',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from City of Hobart Council',
        ]);
    }

    // CityPortPhillip

    public function CityPortPhillip()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'City of Port Phillip')->first();
        $allJobs = [];
        $client = new Client();

     // Define the main URL
        $mainUrl = 'https://recruitment.portphillip.vic.gov.au';

        // Fetch the page content
        $crawler = $client->request('GET', $mainUrl);


        $jobItems = $crawler->filter('.main table tbody tr');
        $allJobs = []; // Initialize an array to store the jobs
        $jobItems->each(function ($item) use (&$allJobs) {

            try {
                // Extract job title (from the first column of the row)
                $jobTitle = trim($item->filter('td[data-th="Position"]')->text());
                // Extract location (from the second column of the row)
                $location = trim($item->filter('td[data-th="Location"]')->text());

                // Extract the closing date (from the fourth column, with the class 'date')
                $expiresRaw = trim($item->filter('td[data-th="Closing"]')->text());

                // Convert the date into the desired format (e.g., Y-m-d)
                $closeDate = Carbon::createFromFormat('d/m/Y', $expiresRaw)->format('Y-m-d');

                $jobId = $item->attr('id');  // Extract job ID
                $jobUrl = 'https://recruitment.portphillip.vic.gov.au/vacancies/' . $jobId . '/edit';

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'location' => $location,
                    'expires' => $closeDate,
                    'url' => $jobUrl,
                ];

            } catch (\Exception $e) {
                // Handle any missing data or parsing errors
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });

        // Debug: Dump all the job data
        // Debug the extracted jobs

        // Print extracted jobs for debugging
        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();

            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = $job['location'];


                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.form-control-plaintext');

                    $jobDescription = $dataContainer->html();
                    // Debug or use the job description
                    $stateFullName = 'Victoria';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'City of Port Phillip',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from City of Port Phillip Council',
        ]);
    }


    // Circular Head

    public function ClarenceValley()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Clarence Valley Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://clarencevalleycouncil.applynow.net.au';
        $crawler = $client->request('GET', $mainUrl);


        $crawler->filter('#joblist > div')->each(function (Crawler $item) use (&$allJobs) {
            try {
                // Extract job title from jobid (use this if job title is inside jobid)
                $jobTitle = trim($item->filter('.location')->text());
                // Extract apply link (assuming it's part of jobid)
                $applyLink = trim($item->filter('a')->attr('href'));

                // Extract location (from the .location span)
                $location = trim($item->filter('.location')->text());
                // Extract closing date from expires span
                $expiresRaw = $item->filter('.expires')->text(); // Get the expires text
                $expiresRaw = str_replace(' AEDT', '', $expiresRaw); // Remove ' AEDT' part

                $closeDate = Carbon::createFromFormat('d M Y', trim($expiresRaw))->format('Y-m-d'); // Format the closing date

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'location' => $location,
                    'expires' => $closeDate,
                ];

            } catch (\Exception $e) {
                // Handle any missing data or parsing errors
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });

        // Output the collected jobs

        // Print extracted jobs for debugging
        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();

            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = $job['location'];


                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('#job_description');

                    $jobDescription = $dataContainer->html();

                    // Debug or use the job description
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Clarence Valley Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Clarence Valley Council',
        ]);
    }

    // CookShire

    public function CookShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Cook Shire Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://csc-ext.applynow.net.au';
        $crawler = $client->request('GET', $mainUrl);

        $jobItems = $crawler->filter('#joblist .jobblock'); // Select all job blocks
        $allJobs = []; // Initialize an array to store the jobs

        // Loop through each job item
        $jobItems->each(function ($item) use (&$allJobs) {
            try {
                // Extract job title (from the <a> inside jobblock)
                $jobTitle = trim($item->filter('.job_title')->text());

                // Extract apply link (from the <a> href attribute)
                $applyLink = trim($item->filter('.job_title')->attr('href'));

                // Extract location (from the <span> with class 'location')
                $location = trim($item->filter('.location')->text());

                // Extract the expires date (from the <span> with class 'expires')
                $expiresRaw = $item->filter('.expires')->text();
                $expiresRaw = str_replace(' AEST', '', $expiresRaw); // Remove ' AEST' if it exists

                // Convert to the correct date format (e.g., Y-m-d)
                $closeDate = Carbon::createFromFormat('d M Y', trim($expiresRaw))->format('Y-m-d');

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'location' => $location,
                    'expires' => $closeDate,
                ];

            } catch (\Exception $e) {
                // Handle any missing data or parsing errors gracefully
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });



        // Output the collected jobs

        // Print extracted jobs for debugging
        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = $job['location'];


                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('#job_description');

                    $jobDescription = $dataContainer->html();

                    // Debug or use the job description
                    $stateFullName = 'Queensland';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Cook Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Cook Shire Council',
        ]);
    }


    //         Cumberland City Council


    public function CumberlandCity()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Cumberland City Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://cumberland.applynow.net.au'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        // Select all job blocks inside the #joblist
        $jobItems = $crawler->filter('#joblist .jobblock');
        $allJobs = []; // Initialize an array to store the jobs

        // Loop through each job item
        $jobItems->each(function ($item) use (&$allJobs) {
            try {
                // Extract job title (from the <a> inside jobblock)
                $jobTitle = trim($item->filter('.job_title')->text());

                // Extract apply link (from the <a> href attribute)
                $applyLink = trim($item->filter('.job_title')->attr('href'));

                // Extract location (from the <span> with class 'location')
                $location = trim($item->filter('.location')->text());

                // Extract the expires date (from the <span> with class 'expires')
                $expiresRaw = $item->filter('.expires')->text();
                $expiresRaw = str_replace(' AEDT', '', $expiresRaw); // Remove ' AEDT' if it exists

                // Convert to the correct date format (e.g., Y-m-d)
                $closeDate = Carbon::createFromFormat('d M Y', trim($expiresRaw))->format('Y-m-d');

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'location' => $location,
                    'expires' => $closeDate,
                ];

            } catch (\Exception $e) {
                // Handle any missing data or parsing errors gracefully
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });


        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = $job['location'];

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('#job_description');

                    $jobDescription = $dataContainer->html();

                    // Debug or use the job description
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Cumberland City Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Cumberland City Council',
        ]);
    }


    // FairfieldCity

    public function FairfieldCity()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Fairfield City Council')->first();
        $allJobs = [];
        $client = new Client();
        $baseUrl = 'https://www.fairfieldcity.nsw.gov.au/Your-Council/Working-for-Fairfield-City-Council/Job-Vacancies';
        $currentPage = 1;

        do {
            // Construct the URL for the current page
            $pageUrl = $baseUrl . '?page=' . $currentPage . '&dlv_OC%20CL%20Public%20Job%20Listing=(pageindex=' . $currentPage . ')';

            // Make the request
            $crawler = $client->request('GET', $pageUrl);

            // Check if there are job items on the current page
            $jobItems = $crawler->filter('.list-container.job-list-container .list-item-container');
            if ($jobItems->count() == 0) {
                break; // Exit if no jobs are found
            }

            // Loop through each job item
            $jobItems->each(function ($item) use (&$allJobs) {
                try {
                    // Extract job title
                    $jobTitle = trim($item->filter('.list-item-title')->text());

                    // Extract apply link
                    $applyLink = trim($item->filter('a')->attr('href'));

                    // Extract the closing date
                    $expiresRaw = trim($item->filter('.applications-closing')->text());
                    $expiresRaw = str_replace('Applications closing on ', '', $expiresRaw);
                    $closeDate = Carbon::createFromFormat('l, d F Y', $expiresRaw)->format('Y-m-d');

                    // Append the job details to the array
                    $allJobs[] = [
                        'title' => $jobTitle,
                        'url' => $applyLink,
                        'expires' => $closeDate,
                    ];
                } catch (\Exception $e) {
                    // Handle errors gracefully
                }
            });

            // Check if there is a next page
            $nextPage = $crawler->filter('.button-next input.btn_scPagingNonJS_enabled')->count() > 0;
            $currentPage++;
        } while ($nextPage);

        // Output or save the scraped data
        // dd($allJobs);

        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Fairfield City Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.body-content');

                    $jobDescription = $dataContainer->html();


                    $salaryContainer = $jobCrawler->filter('.content-details-list.job-details-list');

                    $salaryPackage = 'Competitive';

                    // Loop through each list item to find the 'Package' field
                    $salaryContainer->filter('li')->each(function ($item) use (&$salaryPackage) {
                        $label = $item->filter('.field-label')->text();
                        if (stripos($label, 'Package') !== false) {
                            $salaryPackage = trim($item->filter('.field-value')->text());
                        }
                    });


                    // Debug or use the job description
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Fairfield City Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 4,
                        'custom_salary' => $salaryPackage,
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Fairfield City Council',
        ]);
    }

    // FlindersShire

    public function FlindersShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Flinders Shire Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://www.flinders.qld.gov.au/our-governance/employment/general-vacancies'; // Your target URL
        $client = new Client();
        $crawler = $client->request('GET', $mainUrl);

        $allJobs = []; // Initialize an array to store the jobs

        // Select the table rows within the job listing table
        $jobRows = $crawler->filter('table tbody tr');
        // Loop through each row
        $jobRows->each(function ($row) use (&$allJobs) {
            try {
                // Extract job title and remove VRN and number
                $jobTitleRaw = trim($row->filter('td:nth-child(1)')->text());
                $jobTitle = preg_replace('/\s*VRN\s*\d+\/\d+/', '', $jobTitleRaw);


                $closingDateRaw = trim($row->filter('td:nth-child(4)')->text());

                if (preg_match('/(\d{1,2}\/\d{1,2}\/\d{4})/', $closingDateRaw, $matches)) {
                    $closingDate = Carbon::createFromFormat('d/m/Y', $matches[1])->format('Y-m-d');
                } else {
                    // Add 6 weeks to today's date if no valid date is found
                    $closingDate = Carbon::now()->addWeeks(6)->format('Y-m-d');
                }


                // Extract position information HTML
                $positionInfoHtml = $row->filter('td:nth-child(2)')->html();

                // Extract application form HTML and link
                $applyFormHtml = $row->filter('td:nth-child(3)')->html();
                $applyFormLink = $row->filter('td:nth-child(3) a')->attr('href');

                $historicalDescription = '
                <div class="editor">
                    <p>First settlement in 1863 was by Ernest Henry on Hughenden Station, beginning the foundation of the pastoral industry. It was not until 1877 that the site of Hughenden Township was surveyed.</p>
                    <p>The Division of Hughenden was constituted by proclamation in the Gazette of April 22nd, 1882. The first meeting of the board was held on August 21st, 1882, the board consisting of Messrs. J.H. Harris (Chairman), J.R. Chisholm, G.C. Amos, W. Price, J. Luckmann, and Dean. Captain T.J. Sadler later became the first town clerk of Hughenden.</p>
                    <p>On April 20th, 1887, the town of Hughenden became a separate entity from the Division of Hughenden by proclamation with the first election being held on June 1, 1887.</p>
                    <p>When the Local Authorities Act of 1902 came into force on March 31, 1903, the Division of Hughenden became the Shire of Hughenden. On September 5, 1903, the name was altered to the Shire of Flinders. The Shire was divided into two areas by constituting portions thereof into a new Shire by the name of Wyangarie (now Richmond Shire) on October 23, 1915. The year 1958 saw the amalgamation of Hughenden Town Council and the Shire of Flinders.</p>
                </div>
                ';

                // Add to the jobs array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'expires' => $closingDate,
                    'description' => $positionInfoHtml . '<br>' . $applyFormHtml . '<br>' . $historicalDescription,
                    'url' => $applyFormLink,
                ];
            } catch (\Exception $e) {
                // Handle errors gracefully
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });


        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $jobDescription = $job['description'];

                    $location = 'Flinders Shire Council';

                    $categoryId = 3;


                    // Debug or use the job description
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Flinders Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Flinders Shire Council',
        ]);
    }


    // Glen Innes Severn Council

    public function GlenInnesSevern()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Glen Innes Severn Council')->first();

        $client = new Client();
        $mainUrl = 'https://www.gisc.nsw.gov.au/Council/Jobs-at-Council'; // Your target URL

        $crawler = $client->request('GET', $mainUrl);

        // Initialize an array to store job details (move it outside the loop to make it accessible after)
        $allJobs = [];

        // Find the section after the "Current Vacancies" heading
        $crawler->filter('h2:contains("Current Vacancies")')->each(function ($node) use ($crawler, &$allJobs) {
            // Get all following siblings (this gets the whole section after the heading)
            $vacanciesSection = $node->nextAll();

            // Loop through all siblings and filter <p> tags
            $vacanciesSection->each(function ($item) use (&$allJobs) {
                // Check if the item is a <p> tag
                if ($item->nodeName() === 'p') {
                    // Check if the <p> tag contains an <a> tag
                    if ($item->filter('a')->count() > 0) {
                        try {
                            // Extract job title from the link text inside the <a> tag
                            $jobTitle = trim($item->text());

                            // Clean the job title (remove 'GISC' numbers, 'closes' dates, and 'ongoing' text)
                            $jobTitle = preg_replace('/\bGISC\d+\b/', '', $jobTitle); // Remove job numbers (e.g., GISC258)
                            $jobTitle = preg_replace('/\s+closes \d{1,2} [A-Za-z]+ \d{4}/', '', $jobTitle); // Remove close date text
                            $jobTitle = preg_replace('/\s+ongoing/', '', $jobTitle); // Remove "ongoing" text

                            // Extract apply link from the href attribute of the <a> tag
                            $applyLink = trim($item->filter('a')->attr('href')); // Ensure full URL

                            // Extract close date (if present) from the job title or use a default value
                            $expiresText = $item->text();
                            if (preg_match('/closes (\d{1,2} [A-Za-z]+ \d{4})/', $expiresText, $matches)) {
                                $closeDate = Carbon::createFromFormat('d F Y', $matches[1])->format('Y-m-d');
                            } else {
                                // If no close date, set it to 7 days from the current date
                                $closeDate = Carbon::now()->addWeek()->format('Y-m-d');
                            }

                            // Append job details to the array
                            $allJobs[] = [
                                'title' => $jobTitle,
                                'url' => $applyLink,
                                'expires' => $closeDate,
                            ];
                        } catch (\Exception $e) {
                            // Handle any missing data or parsing errors gracefully
                            // error_log("Error parsing job: " . $e->getMessage());
                        }
                    }
                }
            });
        });


        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Glen Innes Severn Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('#description');

                    $jobDescription = $dataContainer->html();
                    // Debug or use the job description
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Glen Innes Severn Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Glen Innes Severn Council',
        ]);
    }

    // GoulburnMulwaree


    public function GoulburnMulwaree()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Goulburn Mulwaree Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://www.ezisuite.net/eziJob/Goulburn/HRRegistry/default.cfm?act=listVacancies'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        $jobItems = $crawler->filter('table tbody tr'); // Select all rows in the table body
        $allJobs = []; // Initialize an array to store the jobs

        // Loop through each job row
        $jobItems->each(function ($item) use (&$allJobs) {
            try {
                // Extract job title from the <a> tag inside the first <td>
                $jobTitle = trim($item->filter('td a')->text());

                // Extract apply link from the href attribute of the <a> tag
                $applyLink = 'https://www.ezisuite.net/eziJob/Goulburn/HRRegistry/' . trim($item->filter('td a')->attr('href'));

                // Extract the close date from the third <td>
                $expiresRaw = trim($item->filter('td')->eq(2)->text());

                // Convert close date to the correct format (Y-m-d)
                $closeDate = Carbon::createFromFormat('d/m/Y', $expiresRaw)->format('Y-m-d');

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $closeDate,
                ];

            } catch (\Exception $e) {
                // Handle any missing data or parsing errors gracefully
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });



        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Goulburn Mulwaree Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.section .fg')->slice(0, -1); // Exclude the last .fg element

                    $jobDescription = $dataContainer->each(function ($node) {
                        return $node->html();
                    });

                    $jobDescription = implode('', $jobDescription);


                    // Debug or use the job description
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Goulburn Mulwaree Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Goulburn Mulwaree Council',
        ]);
    }


    // GriffithCity

    public function GriffithCity()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Goulburn Mulwaree Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://www.ezisuite.net/eziJob/Griffith/HRRegistry/default.cfm'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        $jobItems = $crawler->filter('table tbody tr'); // Select all rows in the table body
        $allJobs = []; // Initialize an array to store the jobs

        // Loop through each job row
        $jobItems->each(function ($item) use (&$allJobs, $mainUrl) {
            try {
                // Extract job title from the <a> tag inside the first <td>
                $jobTitle = trim($item->filter('td a')->text());

                // Extract apply link from the href attribute of the <a> tag
                $applyLink = rtrim($mainUrl, 'default.cfm') . trim($item->filter('td a')->attr('href'));

                // Extract the close date from the third <td>
                $expiresRaw = trim($item->filter('td')->eq(2)->text());

                // Convert close date to the correct format (Y-m-d)
                $closeDate = Carbon::createFromFormat('d/m/Y', $expiresRaw)->format('Y-m-d');

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $closeDate,
                ];
            } catch (\Exception $e) {
                // Handle any missing data or parsing errors gracefully
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });

        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Griffith City Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.section .fg')->slice(0, -1); // Exclude the last .fg element

                    $jobDescription = $dataContainer->each(function ($node) {
                        return $node->html();
                    });

                    $jobDescription = implode('', $jobDescription);

                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Griffith City Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Griffith City Council',
        ]);
    }

    // GympieRegional

    public function GympieRegional()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Gympie Regional Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://gympie.applynow.net.au'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        $jobItems = $crawler->filter('.jobblock'); // Select all job divs with the class "jobblock"

        // Loop through each job div
        $jobItems->each(function ($item) use (&$allJobs) {
            try {
                // Extract job title
                $jobTitle = trim($item->filter('.job_title')->text());

                // Extract apply link
                $applyLink = trim($item->filter('.job_title')->attr('href'));

                // Extract expiry date
                $expiresRaw = trim($item->attr('data-expires_at'));
                $closeDate = Carbon::createFromFormat('Y-m-d H:i:s O', $expiresRaw)->format('Y-m-d');

                // Extract location
                $location = trim($item->attr('data-location'));

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $closeDate,
                    'location' => $location,
                ];
            } catch (\Exception $e) {
                // Handle any missing data or parsing errors gracefully
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });


        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = $job['location'];

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('#description'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();

                    $stateFullName = 'Queensland';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '-16.4614455' ;
                    $lng =  '145.372664';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Gympie Regional Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Gympie Regional Council',
        ]);
    }

    //

    public function HinchinbrookShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Hinchinbrook Shire Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://clientapps.jobadder.com/65146/hinchinbrook-shire-council'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        $allJobs = [];

        $crawler->filter('.col-md-12 > .row > .job_items')->each(function ($item) use (&$allJobs) {
            try {
                // Extract job title
                $jobTitle = trim($item->filter('h2 a')->text());

                // Extract apply link
                $applyLink = trim($item->filter('h2 a')->attr('href'));

                // Extract expiry date
                $expiryDate = trim($item->filter('h3 sub')->text());
                $closeDate = Carbon::createFromFormat('jS F, Y', $expiryDate)->format('Y-m-d');


                // Extract location
                $location ='Hinchinbrook Shire Council'; // Assuming location is the 3rd list item

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => 'https://clientapps.jobadder.com' . $applyLink,
                    'expires' => $closeDate,
                    'location' => $location,
                ];
            } catch (\Exception $e) {
                // Handle any missing data or parsing errors gracefully
                error_log("Error parsing job: " . $e->getMessage());
            }
        });



        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = $job['location'];

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.pricing-item'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'Queensland';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Hinchinbrook Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Hinchinbrook Shire Council',
        ]);
    }


    // HornsbyShire

    public function HornsbyShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Hornsby Shire Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://www.ezisuite.net/eziJob/Hornsby/HRRegistry/default.cfm'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        $allJobs = [];

        // Select all rows from the tbody of the table
        $crawler->filter('table.list tbody tr')->each(function ($row) use (&$allJobs) {
            try {
                // Extract the job title
                $jobTitle = trim($row->filter('td')->eq(0)->filter('a')->text());

                // Extract the apply link
                $applyLink = trim($row->filter('td')->eq(0)->filter('a')->attr('href'));

                // Extract the close date
                $closeDate = trim($row->filter('td')->eq(2)->text());
                $formattedCloseDate = Carbon::createFromFormat('d/m/Y', $closeDate)->format('Y-m-d');

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => 'https://www.ezisuite.net/eziJob/Hornsby/HRRegistry/' . $applyLink,
                    'expires' => $formattedCloseDate,
                ];
            } catch (\Exception $e) {
                // Handle any missing data or parsing errors gracefully
                error_log("Error parsing job: " . $e->getMessage());
            }
        });


        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Hornsby Shire Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.main-content .section'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();

                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Hornsby Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Hornsby Shire Council',
        ]);
    }

    // Horsham Rural City Council

    public function LeetonShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Leeton Shire Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://www.leeton.nsw.gov.au/Your-Council/Work-With-Us/Jobs/Vacancies'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        $allJobs = [];

        // Select all job containers
        $crawler->filter('div.list-container.job-list-container > div.list-item-container')->each(function (Crawler $job) use (&$allJobs) {
            try {
                // Extract the job title
                $jobTitle = trim($job->filter('h2.list-item-title')->text());

                // Extract the job link
                $applyLink = trim($job->filter('a')->attr('href'));

                // Extract the closing date
                $closeDateRaw = trim($job->filter('p.applications-closing')->text());
                preg_match('/Applications closing on (.+)/i', $closeDateRaw, $matches);
                $formattedCloseDate = isset($matches[1]) ? Carbon::createFromFormat('l, d F Y', $matches[1])->format('Y-m-d') : null;

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $formattedCloseDate,
                ];
            } catch (\Exception $e) {
                // Handle errors gracefully
                error_log("Error parsing job: " . $e->getMessage());
            }
        });


        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url']; // URL of the job
            $jobCrawler = $client->request('GET', $jobUrl); // Request the job URL
            $dataContainer = $jobCrawler->filter('.content-details-list.job-details-list');
            $dataDescription = $jobCrawler->filter('.body-content');
            $jobDescription = $dataDescription->html();

            $pdfLink = null;
            $dataContainer->filter('a.document.ext-pdf')->each(function (Crawler $pdf) use (&$pdfLink) {
                $pdfLink = $pdf->attr('href'); // Extract the href attribute (the PDF link)
            });

            if ($pdfLink && strpos($pdfLink, 'http') === false) {
                $pdfLink = 'https://www.leeton.nsw.gov.au' . $pdfLink;
            }

            $existingJob = Job::where('apply_url', $pdfLink)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Leeton Shire Council';

                    $categoryId = 3;


                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    try {
                        // Make the request to Nominatim API
                        $nominatimResponse = $clientC->get($nominatimUrl, [
                            'query' => [
                                'q' => $location,
                                'format' => 'json',
                                'limit' => 1
                            ],
                            'headers' => [
                                'User-Agent' => 'YourAppName/1.0'
                            ]
                        ]);

                        // Decode the response
                        $nominatimData = json_decode($nominatimResponse->getBody(), true);

                        if (!empty($nominatimData)) {
                            // If the response is not empty, extract the location details
                            $lat = $nominatimData[0]['lat'] ?? '-16.4614455';
                            $lng = $nominatimData[0]['lon'] ?? '145.372664';
                            $exact_location = $nominatimData[0]['display_name'] ?? $location;
                        } else {
                            // If no results, apply fallback values
                            $lat = '-34.507000';
                            $lng = '146.154338';
                            $exact_location = $location;
                        }

                    } catch (\GuzzleHttp\Exception\RequestException $e) {
                        $lat = '-34.507000';
                        $lng = '146.154338';
                        $exact_location = $location;

                    }



                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Leeton Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $pdfLink,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Leeton Shire Council',
        ]);
    }

    // LivingstoneShire

    public function LivingstoneShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Livingstone Shire Council')->first();
        $allJobs = [];
        $client = new Client();
        $mainUrl = 'https://www.livingstone.qld.gov.au/homepage/91/job-vacancies'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        $allJobs = [];

        // Select all rows from the tbody of the table
        $crawler->filter('li.directory__item')->each(function ($li) use (&$allJobs) {
            try {
                // Extract the job title
                $jobTitle = trim($li->filter('.directory__title')->text());

                // // Extract the apply link
                $applyLink = trim($li->filter('.directory__item-content a')->attr('href'));

                // $closeDateText = $li->filter('.directory-detail__value')->text() ?? Carbon::now()->addWeeks(4)->format('l, j F Y');
                // if($closeDateText){
                //     preg_match('/Applications close (.+) at/', $closeDateText, $matches);
                //     $closeDate = isset($matches[1]) ? $matches[1] : 'N/A';

                // }else{
                    $closeDate = Carbon::now()->addWeeks(4)->format('l, j F Y');
                // }
                $formattedCloseDate = Carbon::createFromFormat('l, j F Y', $closeDate)->format('Y-m-d');

                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => 'https://www.livingstone.qld.gov.au' . $applyLink,
                    'expires' => $formattedCloseDate,
                ];
            } catch (\Exception $e) {
                // Handle any missing data or parsing errors gracefully
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });

        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Livingstone Shire Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.page-content'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'Queensland';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Livingstone Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Livingstone Shire Council',
        ]);
    }


    // LoddonShire

    public function LoddonShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Loddon Shire Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.loddon.vic.gov.au/Our-Council/Working-with-us/Current-vacancies'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        $allJobs = []; // Initialize an array to store the jobs

        // Select all job containers
        $crawler->filter('.job-list-container .list-item-container')->each(function ($item) use (&$allJobs) {
            try {
                // Extract job title
                $jobTitle = trim($item->filter('.list-item-title')->text());

                // Extract apply link
                $applyLink = trim($item->filter('a')->attr('href'));

                // Check for close date or use default
                $closeDateText = $item->filter('.applications-closing')->count()
                    ? trim($item->filter('.applications-closing')->text())
                    : null;

                if ($closeDateText && preg_match('/Applications closing on (.+)$/', $closeDateText, $matches)) {
                    $closeDate = $matches[1];
                    $formattedCloseDate = Carbon::createFromFormat('l, j F Y', $closeDate)->format('Y-m-d');
                } else {
                    $formattedCloseDate = Carbon::now()->addWeeks(4)->format('Y-m-d');
                }

                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $formattedCloseDate,
                ];
            } catch (\Exception $e) {

            }
        });



        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Loddon Shire Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.body-content'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'Victoria';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Loddon Shire Council ',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 1,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Loddon Shire Council',
        ]);
    }

    // MansfieldShire

    public function MansfieldShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Mansfield Shire Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.mansfield.vic.gov.au/Council/Work-With-Us/Career-Job-Opportunities'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);
        $allJobs = []; // Initialize an array to store the jobs

        // Select all job containers
        // Select all job containers
        $crawler->filter('.list-item-container')->each(function ($item) use (&$allJobs) {
            try {
                // Extract job title
                $jobTitle = trim($item->filter('h2.list-item-title')->text());

                // Extract apply link
                $applyLink = trim($item->filter('a')->attr('href'));

                // Extract close date
                $closeDateText = $item->filter('p.applications-closing')->count()
                    ? trim($item->filter('p.applications-closing')->text())
                    : null;

                if ($closeDateText && preg_match('/Applications closing on (.+)$/', $closeDateText, $matches)) {
                    $closeDate = $matches[1];
                    $formattedCloseDate = Carbon::createFromFormat('l, j F Y', $closeDate)->format('Y-m-d');
                } else {
                    // Default close date to 4 weeks from today if not present
                    $formattedCloseDate = Carbon::now()->addWeeks(4)->format('Y-m-d');
                }


                // Append the job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $formattedCloseDate,
                ];
            } catch (\Exception $e) {
                // Handle errors gracefully
                // error_log("Error parsing job: " . $e->getMessage());
            }
        });


        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Mansfield Shire Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.body-content'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'Victoria';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Mansfield Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Mansfield Shire Council',
        ]);
    }


    // MountAlexanderShire

    public function MountAlexanderShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Mount Alexander Shire Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.mountalexander.vic.gov.au/Council/Work-with-us/Current-vacancies'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);
        $allJobs = []; // Initialize an array to store the jobs

        $crawler->filter('.list-item-container')->each(function ($item) use (&$allJobs) {
            try {
                // Extract job title
                $titleElement = $item->filter('h2.list-item-title');
                $jobTitle = $titleElement->count() ? trim($titleElement->text()) : 'No title available';

                // Extract apply link
                $linkElement = $item->filter('a');
                $applyLink = $linkElement->count() ? trim($linkElement->attr('href')) : 'No link available';

                // Extract closing date
                $closeDateText = $item->filter('p.applications-closing')->count()
                    ? trim($item->filter('p.applications-closing')->text())
                    : null;

                if ($closeDateText && preg_match('/Applications closing on (.+)/', $closeDateText, $matches)) {
                    $closeDate = $matches[1];
                    $formattedCloseDate = Carbon::createFromFormat('l, d F Y', $closeDate)->format('Y-m-d');
                } else {
                    $formattedCloseDate = Carbon::now()->addWeeks(4)->format('Y-m-d');
                }

                // Append job details
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $formattedCloseDate,
                ];
            } catch (\Exception $e) {
                // Handle errors gracefully
                // Optionally log or debug
            }
        });

        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Mount Alexander Shire Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.body-content'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'Victoria';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Mount Alexander Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Mount Alexander Shire Council',
        ]);
    }


    // MurrayRiver

    public function MurrayRiver()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Murray River Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.murrayriver.nsw.gov.au/Council/Careers/Current-vacancies'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);
        $allJobs = []; // Initialize an array to store the jobs

        $crawler->filter('.list-item-container')->each(function ($item) use (&$allJobs) {
            try {
                // Extract job title
                $titleElement = $item->filter('h2.list-item-title');
                $jobTitle = $titleElement->count() ? trim($titleElement->text()) : 'No title available';

                // Extract apply link
                $linkElement = $item->filter('a');
                $applyLink = $linkElement->count() ? trim($linkElement->attr('href')) : 'No link available';

                // Extract closing date
                $closeDateText = $item->filter('p.applications-closing')->count()
                    ? trim($item->filter('p.applications-closing')->text())
                    : null;

                if ($closeDateText && preg_match('/Applications closing on (.+)/', $closeDateText, $matches)) {
                    $closeDate = $matches[1];
                    $formattedCloseDate = Carbon::createFromFormat('l, d F Y', $closeDate)->format('Y-m-d');
                } else {
                    $formattedCloseDate = Carbon::now()->addWeeks(5)->format('Y-m-d');
                }

                // Append job details
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $formattedCloseDate,
                ];
            } catch (\Exception $e) {
                // Handle errors gracefully
                // Optionally log or debug
            }
        });

        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Murray River Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.body-content'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Murray River Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Murray River Council',
        ]);
    }

    // MurrindindiShire

    public function MurrindindiShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Murrindindi Shire Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.murrindindi.vic.gov.au/Council/Jobs-and-Tenders/Vacant-Positions'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);
        $allJobs = []; // Initialize an array to store the jobs

        $crawler->filter('.list-item-container')->each(function ($item) use (&$allJobs) {
            try {
                // Extract job title
                $titleElement = $item->filter('h2.list-item-title');
                $jobTitle = $titleElement->count() ? trim($titleElement->text()) : 'No title available';

                // Extract apply link
                $linkElement = $item->filter('a');
                $applyLink = $linkElement->count() ? trim($linkElement->attr('href')) : 'No link available';

                // Extract closing date
                $closeDateText = $item->filter('p.applications-closing')->count()
                    ? trim($item->filter('p.applications-closing')->text())
                    : null;

                if ($closeDateText && preg_match('/Applications closing on (.+)/', $closeDateText, $matches)) {
                    $closeDate = $matches[1];
                    $formattedCloseDate = Carbon::createFromFormat('l, d F Y', $closeDate)->format('Y-m-d');
                } else {
                    $formattedCloseDate = Carbon::now()->addWeeks(5)->format('Y-m-d');
                }

                // Append job details
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $formattedCloseDate,
                ];
            } catch (\Exception $e) {
                // Handle errors gracefully
                // Optionally log or debug
            }
        });

        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Murrindindi Shire Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.body-content'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'Victoria';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Murrindindi Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Murrindindi Shire Council',
        ]);
    }


    //   Muswellbrook Shire Council

    public function MuswellbrookShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Muswellbrook Shire Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://muswellbrookshirecouncil.applynow.net.au/'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        $crawler->filter('#joblist .jobblock.block')->each(function ($item) use (&$allJobs) {
            try {
                // Extract attributes directly
                $jobTitle = $item->attr('data-title') ?? 'No title available';
                $applyLink = $item->attr('data-url') ?? 'No link available';
                $expiresAt = $item->attr('data-expires_at')
                    ? Carbon::parse($item->attr('data-expires_at'))->format('Y-m-d')
                    : Carbon::now()->addWeeks(4)->format('Y-m-d');
                $location = $item->attr('data-location') ?? 'Unknown';



                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $expiresAt,
                    'location' => $location,
                ];
            } catch (\Exception $e) {
                // Handle errors gracefully
                // Optionally log or debug
            }
        });




        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;


                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = $job['location'];

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('#description'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Muswellbrook Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Muswellbrook Shire Council',
        ]);
    }

    // NorthernBeaches

    public function NorthernBeaches()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Northern Beaches Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://jobs.northernbeaches.nsw.gov.au/go/All-jobs/4427001/'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);

        $crawler->filter('tr.data-row')->each(function ($row) use (&$allJobs) {
            try {
                // Extract job title
                $jobTitle = $row->filter('a.jobTitle-link')->count()
                    ? trim($row->filter('a.jobTitle-link')->text())
                    : 'No title available';

                // Extract apply link
                $applyLink = $row->filter('a.jobTitle-link')->count()
                    ? trim($row->filter('a.jobTitle-link')->attr('href'))
                    : 'No link available';

                 $applyLink = 'https://jobs.northernbeaches.nsw.gov.au' . $applyLink;
                // Extract close date
                $closeDate = $row->filter('span.jobDate')->count()
                ? trim($row->filter('span.jobDate')->text())
                : null;

            if ($closeDate) {
                // Try to parse and format the close date
                try {
                    $formattedCloseDate = Carbon::createFromFormat('d M Y', $closeDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Handle parsing errors, fall back to a default
                    $formattedCloseDate = Carbon::now()->addWeeks(4)->format('Y-m-d');
                }
            } else {
                // Default to 4 weeks from today if no date is available
                $formattedCloseDate = Carbon::now()->addWeeks(4)->format('Y-m-d');
            }

                // Append the extracted information
                $allJobs[] = [
                    'title' => $jobTitle,
                    'expires' => $formattedCloseDate,
                    'url' => $applyLink,
                ];
            } catch (\Exception $e) {
                // Handle errors gracefully
                // Optionally log or debug
            }
        });

        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Northern Beaches Council ';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.job'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Northern Beaches Council ',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Northern Beaches Council ',
        ]);
    }


    // ParkesShire
    public function ParkesShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Parkes Shire Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://parkes.applynow.net.au'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);
        $crawler->filter('#joblist > div.jobblock')->each(function ($row) use (&$allJobs) {
            try {
                // Extract Job Title from `data-title`
                $jobTitle = $row->attr('data-title') ?? 'No title available';

                // Extract Apply Link from `data-url`
                $applyLink = $row->attr('data-url') ?? 'No link available';


                $expiresText = $row->attr('data-expires_at');

                if ($expiresText) {
                    try {
                        // Parse and format the expiration date
                        $formattedExpireDate = Carbon::parse($expiresText)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Default to 4 weeks from today on parsing failure
                        $formattedExpireDate = Carbon::now()->addWeeks(4)->format('Y-m-d');
                    }
                } else {
                    // Default to 4 weeks from today if no expiration date is provided
                    $formattedExpireDate = Carbon::now()->addWeeks(4)->format('Y-m-d');
                }

                // Append job details
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $formattedExpireDate,
                ];
            } catch (\Exception $e) {
                // Handle exceptions gracefully
                // Optionally log or debug
            }
        });


        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Parkes Shire Council';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('#description'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Parkes Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Parkes Shire Council',
        ]);
    }

    // ParooShire
    public function ParooShire()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Paroo Shire Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.paroo.qld.gov.au/council/employment'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);
      // Locate the specific <p> tag with job listings and extract <a> links
        $crawler->filter('p')->each(function ($paragraph) use (&$allJobs) {
            try {
                // Check if the paragraph contains job links
                $links = $paragraph->filter('a');

                if ($links->count() > 0) {
                    $links->each(function ($link) use (&$allJobs) {
                        // Extract Job Title from the link text
                        $jobTitle = trim($link->text()) ?? 'No title available';

                        // Extract Apply Link from the href attribute
                        $applyLink = trim($link->attr('href')) ?? 'No link available';

                        // Calculate expiration date as 4 weeks from today
                        $formattedExpireDate = Carbon::now()->addWeeks(6)->format('Y-m-d');

                        // Append job details
                        $allJobs[] = [
                            'title' => $jobTitle,
                            'url' => $applyLink,
                            'expires' => $formattedExpireDate,
                        ];
                    });
                }
            } catch (\Exception $e) {
                // Handle exceptions gracefully
                // Optionally log or debug
            }
        });


        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Paroo Shire Council';

                    $categoryId = 3;

                    $jobDescription = '<div class="editor">


                            <p><strong>About Paroo Shire Council:</strong></p>

                            <p>A Paroo Shire Council career goes beyond business as usual. You ll find exciting development pathways rich in opportunity. Our thriving and engaged culture-first workplace is built on the passion and talent of people who proudly deliver vital services and exciting projects to a community they care about.&nbsp;Our organisation comprises of a multicultural workplace of around 89 full-time, part-time and casual employees, all benefiting from great flexibility, work/life sway, study support, health and wellbeing initiatives and ongoing learning.&nbsp;Paroo Shire Council has a close-knit culture, with networking encouraged to support all teams. &nbsp;</p>

                            <p><strong>Why You’ll Like Working Here:&nbsp;</strong>&nbsp;</p>

                            <p>At Paroo Shire Council, we are committed to our community and its environment and provide our employees with the same level of commitment and care. As a member of a close-knit team, you will experience a connected and supportive environment. The team you will join is welcoming and knowledgeable and ready to collaborate to continually improve our systems and processes.&nbsp;We offer diverse and rewarding work, ongoing training and development opportunities, and genuine work-life balance. Additionally, our staff have the opportunity to deliver on initiatives that have a tangible impact on the daily lives of residents.&nbsp;</p>

                            <p><strong>We will offer you:</strong></p>

                            <ul>
                                <li>Competitive remuneration packaging and allowances</li>
                                <li>Partly furnished accommodation where you can create a home may be considered</li>
                                <li>Relocation expenses considered on application</li>
                                <li>Time to relax with family and friends with 4 weeks annual leave and 17.5% loading</li>
                                <li>A nine (9) day fortnight to enjoy a leisurely long weekend</li>
                                <li>Uniforms so that you never need to find something to wear.</li>
                            </ul>

                            <p>If you see yourself working and living in the Paroo Shire, a great way to start is to connect with us. We are here to listen and accept Expressions of Interest and Casual Pool applicants at any time.&nbsp;We have Traineeships and Apprenticeship opportunities, so let us know what your field/interest is so we can then arrange a meet and greet here at our Council Headquarters.&nbsp;If you are interested in attending a recruitment open day, please email Human Resources Manager Denise O’Brien on hr@paroo.qld.gov.au<br>
                            See you in 2025 for the start to YOUR New beginnings</p>
                            </div>';
                    $stateFullName = 'Queensland';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Paroo Shire Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }


        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Paroo Shire Council',
        ]);
    }

    // RichmondValley
    public function RichmondValley()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Richmond Valley Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://richmondvalleycouncil.applynow.net.au'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);
        $crawler->filter('#joblist > div.jobblock')->each(function ($row) use (&$allJobs) {
            try {
                // Extract Job Title from `data-title`
                $jobTitle = $row->attr('data-title') ?? 'No title available';

                // Extract Apply Link from `data-url`
                $applyLink = $row->attr('data-url') ?? 'No link available';

                // Extract Expiration Date from `data-expires_at`
                $expiresText = $row->attr('data-expires_at');

                // Parse and format the expiration date
                if ($expiresText) {
                    try {
                        $formattedExpireDate = Carbon::parse($expiresText)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Default to 4 weeks from today on parsing failure
                        $formattedExpireDate = Carbon::now()->addWeeks(4)->format('Y-m-d');
                    }
                } else {
                    // Default to 4 weeks from today if no expiration date is provided
                    $formattedExpireDate = Carbon::now()->addWeeks(4)->format('Y-m-d');
                }

                // Append job details
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'expires' => $formattedExpireDate,
                ];
            } catch (\Exception $e) {
                // Handle exceptions gracefully
                // Optionally log or debug the error
            }
        });



        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Richmond Valley';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('#description'); // Exclude the last .fg element

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'New South Wales';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Richmond Valley',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => 'Competitive',
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }

        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Richmond Valley',
        ]);
    }

    // RuralCityWangaratta
    public function RuralCityWangaratta()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Rural City of Wangaratta')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://candidate.aurion.cloud/wangaratta/production'; // Your target URL
        $crawler = $client->request('GET', $mainUrl);
      // Filter the table rows and extract job data
        $crawler->filter('tbody > tr.table-clickable-row')->each(function ($row) use (&$allJobs) {
            try {
                // Extract job position
                $jobTitle = trim($row->filter('td[data-th="Position"]')->text());

                // Extract closing date from the `data-order` attribute
                $closingDate = $row->filter('td[data-th="Closing"]')->attr('data-order') ?? null;

                if ($closingDate) {
                    // Format closing date
                    try {
                        $formattedCloseDate = Carbon::parse($closingDate)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $formattedCloseDate = Carbon::now()->addWeeks(4)->format('Y-m-d'); // Default to 4 weeks from today
                    }
                } else {
                    $formattedCloseDate = Carbon::now()->addWeeks(4)->format('Y-m-d'); // Default if no closing date
                }

                // Extract the URL from the `data-url` attribute
                $applyLink = $row->attr('data-url') ?? 'No link available';

                // Append job details to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'expires' => $formattedCloseDate,
                    'url' => 'https://candidate.aurion.cloud/wangaratta/production/' . $applyLink,
                ];
            } catch (\Exception $e) {
                // Handle exceptions gracefully
                // Optionally log or debug the error
            }
        });

        // Output the extracted job data


        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Rural City of Wangaratta';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    $dataContainer = $jobCrawler->filter('.form-control-plaintext'); // Exclude the last .fg element
                    $packageValue = $jobCrawler->filter('input[id*="VACANCY_PACKAGE"]')->attr('value');
                    if($packageValue){
                        $salary = $packageValue;
                    }else{
                        $salary = 'Competitive';
                    }

                    $jobDescription = $dataContainer->html();
                    $stateFullName = 'Victoria';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Rural City of Wangaratta',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 3,
                        'custom_salary' => $salary,
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }

        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Rural City of Wangaratta',
        ]);
    }

    //         Roper Gulf Regional Council
    public function RoperGulfRegional()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Roper Gulf Regional Council')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://ropergulf.nt.gov.au/jobs/job-vacancies'; // Your target URL
        $crawler = $client->request('GET', $mainUrl); // Assuming $client is a configured HTTP client

        $allJobs = []; // Array to hold extracted job data

        // Filter for job listings
        $crawler->filter('.item-list')->each(function (Crawler $node) use (&$allJobs) {
            try {
                // Extract the job title
                $jobTitle = $node->filter('h3 > a')->text();

                // Extract the apply link
                $applyLink = $node->filter('h3 > a')->attr('href');
                $applyLink = 'https://ropergulf.nt.gov.au' . $applyLink; // Ensure full URL

                // Extract the closing date
                $closingDate = $node->filter('time')->attr('datetime');

                // Format the closing date
                try {
                    $formattedCloseDate = Carbon::parse($closingDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    $formattedCloseDate = Carbon::now()->addWeeks(4)->format('Y-m-d'); // Default to 4 weeks from today
                }

                // Append extracted job data to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'expires' => $formattedCloseDate,
                    'url' => $applyLink,
                ];
            } catch (\Exception $e) {
                // Handle exceptions gracefully (e.g., log the error)
            }
        });



        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['expires'];
                    $location = 'Roper Gulf Region';

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                    // Select the container that holds the job details
                    $dataContainer = $jobCrawler->filter('.node--type-vacancy');

                        $dom = $dataContainer->getNode(0);

                        // Remove the "Apply Now" button
                        $applyNowButton = (new Crawler($dom))->filter('a.button');
                        foreach ($applyNowButton as $button) {
                            $button->parentNode->removeChild($button);
                        }

                        // Remove the "content-date" element
                        $contentDate = (new Crawler($dom))->filter('.content-date');
                        foreach ($contentDate as $date) {
                            $date->parentNode->removeChild($date);
                        }

                        // Convert the updated DOM back to HTML
                        $jobDescription = (new Crawler($dom))->html();

                        // Output or process the cleaned HTML

                        $salary = 'Competitive';


                    $stateFullName = 'Northern Territory';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Roper Gulf Regional Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 3,
                        'custom_salary' => $salary,
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }

        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Roper Gulf Regional Council',
        ]);
    }

    // ShireAugustaMargaretRiver
    public function ShireAugustaMargaretRiver()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Shire of Augusta Margaret River')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.amrshire.wa.gov.au/shire-and-council/jobs-careers-and-tenders/current-job-vacancies'; // Your target URL
        $crawler = $client->request('GET', $mainUrl); // Assuming $client is a configured HTTP client

        $allJobs = []; // Array to hold extracted job data

        // Filter for job listings
        // Filter for job listings
        $crawler->filter('.landing-hotboxes .col-12')->each(function (Crawler $node) use (&$allJobs) {
            try {
                // Extract the job title
                $jobTitle = $node->filter('.news-content h2')->text();

                // Extract the apply link
                $applyLink = $node->filter('a')->attr('href');
                $applyLink = 'https://www.amrshire.wa.gov.au' . $applyLink; // Ensure full URL

                // Extract the salary (if present in the description paragraph)
                $description = $node->filter('.news-content p')->text();
                preg_match('/\$\d{1,3}(,\d{3})*(\.\d{2})? - \$\d{1,3}(,\d{3})*(\.\d{2})?/', $description, $matches);
                $salary = $matches[0] ?? 'Not specified';

                // Append extracted job data to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'salary' => $salary,
                ];
            } catch (\Exception $e) {
                // Handle exceptions gracefully (e.g., log the error)
            }
        });




        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $location = 'Shire of Augusta Margaret River';

                    if($job['salary']){
                        $salary = $job['salary'];

                    }else{
                        $salary = 'Competitive';

                    }

                    $categoryId = 3;

                    $jobCrawler = $client->request('GET', $jobUrl);

                            $dataContainer = $jobCrawler->filter('.aw-layout .container');

                            $dom = $dataContainer->getNode(0);

                            // Remove unwanted elements
                            $applyNowButton = (new Crawler($dom))->filter('a.button');
                            foreach ($applyNowButton as $button) {
                                $button->parentNode->removeChild($button);
                            }

                            $contentDate = (new Crawler($dom))->filter('.content-date');
                            foreach ($contentDate as $date) {
                                $date->parentNode->removeChild($date);
                            }

                            // Convert the updated DOM back to HTML for the job description
                            $jobDescription = (new Crawler($dom))->html();

                            // Extract the closing date and format it
                            $closingDateText = $dataContainer->filter('h3:contains("Applications close") + p')->text();
                            try {
                                $formattedExpiryDate = Carbon::parse($closingDateText)->format('Y-m-d');
                            } catch (\Exception $e) {
                                $formattedExpiryDate = Carbon::now()->addWeeks(4)->format('Y-m-d'); // Default fallback
                            }

                            $jobDescription = $dataContainer->html();






                    $stateFullName = 'Western Australia';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Shire of Augusta Margaret River',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => $salary,
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }

        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Shire of Augusta Margaret River',
        ]);
    }

    // ShireEastPilbara

    public function ShireEastPilbara()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Shire of East Pilbara')->first();
        $allJobs = [];
        $client = new Client();

        $mainUrl = 'https://www.eastpilbara.wa.gov.au/employment'; // Your target URL
        $crawler = $client->request('GET', $mainUrl); // Assuming $client is a configured HTTP client
        $allJobs = []; // Array to hold extracted job data

       // Filter for job listings
        $crawler->filter('#jobsContainer .job-item-container')->each(function (Crawler $node) use (&$allJobs) {

                $jobTitle = $node->filter('.job-title')->text();

                // Extract the apply link
                $applyLink = $node->filter('a')->attr('href');
                $applyLink = 'https://www.eastpilbara.wa.gov.au' . $applyLink; // Ensure full URL
                // Extract the location
                $location = $node->filter('.job-location')->text();

                $closingDateText = $node->filter('.job-closing-date b')->text();

                $cleanedDateText = str_replace(['Closing Date:', 'W. Australia Standard Time'], '', $closingDateText);
                $cleanedDateText = trim($cleanedDateText); // Result: "1/04/2025 4:00 PM"

                // Parse the cleaned date
                $formattedClosingDate = Carbon::createFromFormat('d/m/Y h:i A', $cleanedDateText)->format('Y-m-d');


                // Append extracted job data to the array
                $allJobs[] = [
                    'title' => $jobTitle,
                    'url' => $applyLink,
                    'location' => $location,
                    'closing_date' => $formattedClosingDate,
                ];

        });




        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $location = 'Shire of East Pilbara';


                        $salary = 'Competitive';
                        $formattedExpiryDate = $job['closing_date'];


                        $categoryId = 3;

                        $jobCrawler = $client->request('GET', $jobUrl);

                            $dataContainer = $jobCrawler->filter('.pulse-details-container');


                            $jobDescription = $dataContainer->html();





                    $stateFullName = 'Western Australia';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);

                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Shire of East Pilbara',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => $salary,
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }

        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Shire of East Pilbara',
        ]);
    }

    // ShireNgaanyatjarraku

    public function ShireNgaanyatjarraku()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Shire of Ngaanyatjarraku')->first();
        $allJobs = [];
        $client = new Client();

            $mainUrl = 'https://www.ngaanyatjarraku.wa.gov.au/our-shire/work-with-us/employment.aspx'; // Target URL
            $crawler = $client->request('GET', $mainUrl); // Fetch the page
            $allJobs = []; // Array to hold extracted job data

            // Select rows from the table (skipping the header row)
            $crawler->filter('table[title="Content Table"] tr')->each(function (Crawler $row, $index) use (&$allJobs) {
                // Skip the header row
                if ($index === 0) {
                    return;
                }

                try {
                    $jobTitle = $row->filter('td:nth-child(1)')->text();

                    $positionDescriptionLink = $row->filter('td:nth-child(2) a')->attr('href');
                    $positionDescriptionLink = 'https://www.ngaanyatjarraku.wa.gov.au/' . ltrim($positionDescriptionLink, '/');

                    $closingDateText = $row->filter('td:nth-child(3)')->text();
                    $formattedClosingDate = $closingDateText === '-'
                        ? Carbon::now()->addWeeks(4)->format('Y-m-d') // 4 weeks from today
                        : Carbon::createFromFormat('d/m/Y', trim($closingDateText))->format('Y-m-d');


                    $allJobs[] = [
                        'title' => $jobTitle,
                        'url' => $positionDescriptionLink,
                        'closing_date' => $formattedClosingDate,
                    ];
                } catch (\Exception $e) {

                }
            });




        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $location = 'Shire of Ngaanyatjarraku';


                        $salary = 'Competitive';
                        $formattedExpiryDate = $job['closing_date'];


                        $categoryId = 3;

                        // $jobCrawler = $client->request('GET', $jobUrl);

                        //     $dataContainer = $jobCrawler->filter('.pulse-details-container');


                        //     $jobDescription = $dataContainer->html();
                        $jobDescription = '<div class="col-xs-12 col-md-8 col-lg-9 cp-placeholder">


            <h1>Who We Are</h1>
        <p>The Shire of Ngaanyatjarraku is responsible for the provision of "mainstream" local government and delivery of services to the ten communities and visitors within its boundaries.</p>
        <p>The Shire encompasses an area of 159,948 square kilometres and is located approximately 1542km from Perth. The region itself is diverse in natural beauty from the magnificent Rawlinson ranges to the red sandy plains of the Gibson Desert.</p>
        <p>The Shire Offices are located in the Tjulyuru Cultural and Civic Centre in Warburton.</p>
        <p><img alt="shire offices" src="https://www.ngaanyatjarraku.wa.gov.au/Profiles/shire/Assets/ClientData/Images/Page_Centre/new_tjulyru_image.jpg" width="325" height="216"></p>
        <p><img alt="ngaanyatjarraku boundary" src="https://www.ngaanyatjarraku.wa.gov.au/Profiles/shire/Assets/ClientData/Images/Page_Centre/Shire_of_Ngaanyatjarraku_Boundary.jpg" width="640" height="480"></p>

                    </div>';





                    $stateFullName = 'Western Australia';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Shire of Ngaanyatjarraku',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => $salary,
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

                    // Add to allJobs array
            }

        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Shire of Ngaanyatjarraku',
        ]);
    }


    // SomersetRegional

    public function SomersetRegional()
    {
        ini_set('max_execution_time', 3000000); // Set maximum execution time (5 minutes)

        $user = User::where('name', 'Somerset Regional Council')->first();
        $allJobs = [];
        $client = new Client();

            $mainUrl = 'https://www.somerset.qld.gov.au/your-council/employment';
            $crawler = $client->request('GET', $mainUrl);

            $allJobs = []; // Array to hold extracted job data
            // Iterate over each job entry
            $crawler->filter('.editor table tbody tr')->each(function ($node) use (&$allJobs) {

                    // Extract the job title and apply link
                    $titleNode = $node->filter('td:nth-child(1) a');
                    $jobTitle = $titleNode->text();
                    $applyLink = $titleNode->attr('href');
                    if (!str_starts_with($applyLink, 'http')) {
                        $applyLink = 'https://www.somerset.qld.gov.au' . $applyLink; // Ensure full URL
                    }

                    $closingDate = $node->filter('td:nth-child(4)')->text();

                    // If closing date is not provided, set it to 4 weeks from today
                    if (empty($closingDate) || stripos($closingDate, 'Applications will be reviewed as they are received') !== false) {
                        // Calculate 4 weeks from today
                        $closingDate = \Carbon\Carbon::now()->addWeeks(4)->format('Y-m-d');
                    } else {
                        $closingDate = str_replace('pm', '', $closingDate);
                        $closingDate = \Carbon\Carbon::parse($closingDate)->format('Y-m-d');
                    }
                    // Append extracted job data to the array
                    $allJobs[] = [
                        'title' => $jobTitle,
                        'closing_date' => $closingDate,
                        'url' => $applyLink,
                    ];

            });



        $jobAdded = 0;
        foreach($allJobs as $job) {

            $jobUrl = $job['url'];

            $existingJob = Job::where('apply_url', $jobUrl)->first();
            if (!$existingJob) {

                    $jobAdded++;

                    $title = $job['title'];
                    $formattedExpiryDate = $job['closing_date'];

                    $location = 'Somerset Regional Council';


                        $salary = 'Competitive';



                    $categoryId = 3;



                            $jobDescription = '<div class="editor">
                                    <p>&nbsp;</p>

                                <p>Somerset Regional Council was formed on 15 March 2008 following an amalgamation of the Councils of Esk Shire and Kilcoy Shire.</p>

                                <p>SRC has a Mayor and six Councillors, each is elected by their constituents&nbsp;and serve a four-year term.</p>

                                <p>Please visit our <a href="http://www.somerset.qld.gov.au/councillor-profiles">Councillor Profiles page</a> for more information about our current Mayor and Councillors.</p>

                                <p>This regional local government is an hour west of Brisbane and is the fastest growing local government area in south east Queensland. It has strong agricultural, environmental, heritage and tourism values. It contains important vegetation and forest, areas of high scenic and landscape amenity and significantly, the key water catchments for southeast Queensland.</p>

                                <p>The Somerset region has an area of 5382 sq km and includes five major townships, Esk, Fernvale, Kilcoy, Lowood and Toogoolawah. The region is home to approximately 25,000 people and is expected to grow to an estimated 34,500 by 2031.</p>

                                <p>Somersets neighbouring local governments are Lockyer Valley, Ipswich City, Brisbane City, Moreton Bay, Sunshine Coast, Gympie, South Burnett and Toowoomba.</p>

                                <p>Somerset Regional Councils logo represents the regions two major dams, with the larger body of water representing Wivenhoe and the smaller body being Somerset. The overall shape of the icon with the water flowing from Somerset to Wivenhoe creates the shape of a clear "S", which uniquely identifies this water graphic to be that of Somerset Regional Council.</p>

                                <p>The previous Esk and Kilcoy Shire Councils had adopted floral and faunal emblems. The continued use of these emblems is symbolic, given that none of these emblems are reflected in the logo. On 19 December 2008 Council adopted the following emblems:</p>

                                <p>Floral:</p>

                                <ul>
                                    <li>Weeping bottlebrush (Callistemon viminalis)</li>
                                    <li>Native frangipani (Hymenosporum flavum)</li>
                                </ul>

                                <p>Fauna:</p>

                                <ul>
                                    <li>Red deer (Cervus elaphus)</li>
                                </ul>

                                <p>Deer were first introduced into Queensland in September 1873 when two stags and four hinds were released at Scrub Creek, Cressbrook Station. These deer were from Windsor Great Park and were a gift from Queen Victoria to the Acclimatisation Society of Queensland. Today, the descendants of the original release are well entrenched in the ranges of the Brisbane and Mary Valleys.</p>

                                <p>Somerset Regional Council covers the largest land area of all south east Queensland Councils and currently has the smallest rate base. In spite of the challenges, the region continues to develop in an economically, environmentally and socially sustainable manner and will continue to attract new residents because of the community, lifestyle and amenity on offer.</p>
                                </div>';






                    $stateFullName = 'Queensland';
                    $clientC = new ClientC();
                    $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
                    $nominatimResponse = $clientC->get($nominatimUrl, [
                        'query' => [
                            'q' => $location,
                            'format' => 'json',
                            'limit' => 1
                        ],
                        'headers' => [
                            'User-Agent' => 'YourAppName/1.0'
                        ]
                    ]);
                $nominatimData = json_decode($nominatimResponse->getBody(), true);
                if (!empty($nominatimData)) {
                    $lat = $nominatimData[0]['lat'] ?? '-16.4614455' ;
                    $lng = $nominatimData[0]['lon'] ?? '145.372664';
                    $exact_location = $nominatimData[0]['display_name'] ?? $location;

                } else {
                    $lat = '18.65060012243828' ;
                    $lng =  '146.154338';
                    $exact_location = $location;

                }


                $stateId = State::where('name', 'like', '%' . $stateFullName . '%')->first();
                if($stateId){
                    $sId = $stateId->id;
                }else{
                    $sId = 3909;
                }

                    // Prepare job data for insertion
                    $jobRequest = [
                        'title' => $title,
                        'category_id' => $categoryId,
                        'company_id' => $user->company->id,
                        'company_name' => 'Somerset Regional Council',
                        'apply_on' => 'custom_url',
                        'apply_url' => $jobUrl,
                        'description' => $jobDescription,
                        'state_id' => $sId,
                        'vacancies' => 1,
                        'deadline' => $formattedExpiryDate,
                        'salary_mode' => 'custom',
                        'salary_type_id' => 1,
                        'custom_salary' => $salary,
                        'job_type_id' => 1,
                        'role_id' => 1,
                        'education_id' => 2,
                        'experience_id' => 4,
                        'featured' => 0,
                        'highlight' => 0,
                        'status' => 'active',
                        'ongoing' => 0,
                    ];
                    // Save the job to the database
                    $done = $this->createJobFromScrape($jobRequest);

                    // Update categories
                    $categories = [0 => $categoryId];
                    $done->selectedCategories()->sync($categories);

                    $done->update([
                        'address' => $exact_location,
                        'neighborhood' => $exact_location,
                        'locality' => $exact_location,
                        'place' => $exact_location,
                        'country' => 'Australia',
                        'district' => $stateFullName, // Assuming state is NSW
                        'region' => $stateFullName, // Assuming state is NSW
                        'long' => $lng, // Default longitude, can be adjusted if coordinates are available
                        'lat' => $lat, // Default latitude, can be adjusted if coordinates are available
                        'exact_location' => $exact_location,
                    ]);

            }

        };

        // Return the number of jobs scraped
        return response()->json([
        'message' => $jobAdded . ' job(s) scraped from Somerset Regional Council',
        ]);
    }




    private function extractTextFromPdfForBlueMountain($pdfUrl)
    {
        // Initialize Guzzle Client with appropriate headers
        $client = new \GuzzleHttp\Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
                'Referer' => 'https://www.bmcc.nsw.gov.au',  // Optional: some sites require a referer
            ]
        ]);

        try {
            // Make a GET request to fetch the PDF content
            $response = $client->request('GET', $pdfUrl);

            // Check for successful response
            if ($response->getStatusCode() !== 200) {
                return 'Failed to fetch the PDF file. Status Code: ' . $response->getStatusCode();
            }

            // Get the PDF content from the response body
            $pdfContent = $response->getBody()->getContents();

            // Initialize the PdfParser
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseContent($pdfContent);

            // Extract the text content from the PDF
            $text = $pdf->getText();

            // If text is found, return it as is, otherwise return a default message
            if ($text) {
                return $text;  // Plain text from the PDF
            } else {
                return 'No text available in the PDF';
            }

        } catch (\Exception $e) {
            // Handle errors (e.g., failed to fetch PDF or parse it)
            return 'Error extracting text from PDF: ' . $e->getMessage();
        }
    }



    // private function extractTextFromPdf($pdfUrl)
    // {
    //     // Use a library like PdfParser or another PDF parsing library to extract text from PDF
    //     // For simplicity, we'll assume you have a method that extracts the text from a PDF file
    //     try {
    //         // Download the PDF file contents
    //         $pdfContent = file_get_contents($pdfUrl);

    //         // Assuming you have a library to extract text from PDF (e.g., PdfParser)
    //         $parser = new \Smalot\PdfParser\Parser();
    //         $pdf = $parser->parseContent($pdfContent);

    //         // Extract the text content from the PDF
    //         $text = $pdf->getText();

    //         // Return the extracted text
    //         return $text ?: 'No description available'; // Return default if no text is found
    //     } catch (\Exception $e) {
    //         // Handle errors (e.g., failed to fetch PDF or parse it)
    //         return null;
    //     }
    // }

    private function extractTextFromPdf($pdfUrl)
{
    // Use the PdfParser library to extract text from the PDF
    try {
        // Download the PDF file contents
        $pdfContent = file_get_contents($pdfUrl);

        // Initialize the PdfParser
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseContent($pdfContent);

        // Extract the text content from the PDF
        $text = $pdf->getText();

        // If text is found, convert it to HTML by wrapping it in <p> tags
        if ($text) {
            // Split the text by newlines and wrap each line in <p> tags
            $lines = explode("\n", $text);
            $htmlContent = '';
            foreach ($lines as $line) {
                // Escape any HTML special characters
                $htmlContent .= '<p>' . htmlspecialchars($line) . '</p>';
            }
        } else {
            $htmlContent = '<p>No description available</p>';
        }

        // Return the HTML content
        return $htmlContent;
    } catch (\Exception $e) {
        // Handle errors (e.g., failed to fetch PDF or parse it)
        return $e->getMessage();
    }
}




    private function createJobFromScrape($jobData)
    {
       $job =  Job::create($jobData);
       return $job;
    }


}
