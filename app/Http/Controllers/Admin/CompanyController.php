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
        // $client = new Client();

        // $mainUrl = 'https://www.bmcc.nsw.gov.au/jobs'; // Updated job listing page
        // $crawler = $client->request('GET', $mainUrl);
        // Step 1: Extract job listings from the table with the given class
        // $jobRows = $crawler->filter('.field-middle-body'); // Select table rows
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

            $jobUrl = $crawler->filter('.related-download-link a')->attr('href') ?? $link;



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


    // BanyuleCity





    


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
