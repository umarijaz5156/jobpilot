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
use Illuminate\Support\Facades\Mail;
use PDF;

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
}
