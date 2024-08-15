<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class JobController extends Controller
{

    public function storeViaApi(Request $request)
    {

        $companyData = User::where('email',$request->company_email)->first();
        $company_id = $companyData->company->id;
        $company_name = $companyData->name;

        // Create the job
        $job = Job::create([
            'title' => $request->title,
            'company_id' => $company_id,
            'company_name' => $company_name,
            'category_id' => $request->category_id,
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
            'featured' => $request->featured,
            'highlight' => $request->highlight,
            'featured_until' => $request->featured_until,
            'highlight_until' => $request->highlight_until,
            'is_remote' => $request->is_remote ?? 0,
            'status' => 'active',

            'address' => $request->address ?? '',
            'neighborhood' => $request->neighborhood ?? '',
            'locality' => $request->locality ?? '',
            'place' => $request->place ?? '',
            'district' => $request->district ?? '',
            'postcode' => $request->postcode ?? '',
            'region' => $request->region ?? '',
            'country' => $request->country ?? '',
            'long' => $request->long ?? '',
            'lat' => $request->lat ?? '',
            'exact_location' => $request->exact_location ?? '',

        ]);

        $job->selectedCategories()->sync($request->categories);


        return response()->json(['success' => true, 'job' => $job], 201);
    }

    public function storeViaApiCompany(Request $request){

        // Create the user
        $name = $request->name;
        $username = $request->username ? Str::slug($request->username) : Str::slug($name).'_'.time();

        // Check if the username is unique
        while (User::where('username', $username)->exists()) {
            $username = Str::slug($name).'_'.time();
        }

        $company = User::create([
          'name' => $request->name,
          'username' => $request->username,
          'email' => $request->email,
          'password' => bcrypt($request->password),
          'role' => 'company',
          'email_verified_at' => now(),
      ]);

      // Process the logo
      if ($request->logo) {

          $path = 'uploads/images/company';

          $logo_url = uploadImage($request->logo, $path, [68, 68]);
      } else {
          $setDimension = [100, 100]; //Here needs to be [68, 68] but avatar image not looks good in view that's why increase value 100 from 68
          $path = 'uploads/images/company';
          $logo_url = createAvatar($name, $path, $setDimension);
      }

              // insert banner
              if ($request->image) {

                  $path = 'uploads/images/company';

                  $banner_url = uploadImage($request->image, $path, [1920, 312]);
              } else {
                  $setDimension = [1920, 312];
                  $path = 'uploads/images/company';
                  $banner_url = createAvatar($name, $path, $setDimension);
              }

      // Format date

      // Create the company
       $company->company()->update([
          'industry_type_id' => $request->industry_type_id,
          'organization_type_id' => $request->organization_type_id,
          'team_size_id' => $request->team_size_id,
          'logo' => $logo_url,
          'banner' => $banner_url,
          'website' => $request->website,
          'bio' => $request->bio,
          'vision' => $request->vision,
          'video_url' => $request->video_url,
          'is_profile_verified' => 1
      ]);

      // Update company contact info
      $company->contactInfo()->create([
          'phone' => $request->contact_phone,
          'email' => $request->contact_email,
      ]);

      // Insert social media links
      if ($request->social_media && $request->url) {
          foreach ($request->social_media as $key => $value) {
              $company->socialInfo()->create([
                  'social_media' => $value,
                  'url' => $request->url[$key] ?? '',
              ]);
          }
      }

      $company->update([
          'status' => 1,
      ]);


      $location = json_decode($request->location, true);

      $this->updateMap($company->company(),$location);

      // Return a response
      return response()->json([
          'message' => 'Company created successfully',
      ], 200);

    }

    function updateMap($company,$location)
    {

        $location = $location;

        if ($location) {
            $company->update([
                'address' => $location['exact_location'] ?? '',
                'neighborhood' => $location['neighborhood'] ?? '',
                'locality' => $location['locality'] ?? '',
                'place' => $location['place'] ?? '',
                'district' => $location['district'] ?? '',
                'postcode' => $location['postcode'] ?? '',
                'region' => $location['region'] ?? '',
                'country' => $location['country'] ?? '',
                'long' => $location['lng'] ?? '',
                'lat' => $location['lat'] ?? '',
                'exact_location' => $location['exact_location'] ?? '',
            ]);

            session()->forget('location');
            session([
                'selectedCountryId' => null,
                'selectedStateId' => null,
                'selectedCityId' => null,
                'selectedCountryLong' => null,
                'selectedCountryLat' => null,
                'selectedStateLong' => null,
                'selectedStateLat' => null,
                'selectedCityLong' => null,
                'selectedCityLat' => null,
            ]);
        }

        return true;
    }




    // Waterland jobs post selected

    public function WaterLandSelectedJobs(Request $request){

        foreach ($request->ids as $jobId) {

           $job = Job::where('id',$jobId)->first();


           $client = new Client();
           $websiteUrl = env('WEBSITE_URL');

           $companyData = Company::findOrFail($job->company_id);
           $categories = $job->selectedCategories()->pluck('category_id')->toArray();
           $categoryId = $categories[0] ?? 3;


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

                       'address' => $request->address ?? '',
                       'neighborhood' => $request->neighborhood ?? '',
                       'locality' => $request->locality ?? '',
                       'place' => $request->place ?? '',
                       'district' => $request->district ?? '',
                       'postcode' => $request->postcode ?? '',
                       'region' => $request->region ?? '',
                       'country' => $request->country ?? '',
                       'long' => $request->long ?? '',
                       'lat' => $request->lat ?? '',
                       'exact_location' => $request->exact_location ?? '',
                   ]
               ]);

            //    return json_decode($response->getBody(), true);

        }

        return true;
    }


    // Engineering Jobs Selected
    public function EngineeringjobshubSelectedJobs(Request $request){

        foreach ($request->ids as $jobId) {

           $job = Job::where('id',$jobId)->first();


           $client = new Client();
           $websiteUrl = env('WEBSITE_URL_JOB_EngineeringJobsHub');

           $companyData = Company::findOrFail($job->company_id);
           $categories = $job->selectedCategories()->pluck('category_id')->toArray();
           $categoryId = $categories[0] ?? 3;


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

                       'address' => $request->address ?? '',
                       'neighborhood' => $request->neighborhood ?? '',
                       'locality' => $request->locality ?? '',
                       'place' => $request->place ?? '',
                       'district' => $request->district ?? '',
                       'postcode' => $request->postcode ?? '',
                       'region' => $request->region ?? '',
                       'country' => $request->country ?? '',
                       'long' => $request->long ?? '',
                       'lat' => $request->lat ?? '',
                       'exact_location' => $request->exact_location ?? '',
                   ]
               ]);

            //    return json_decode($response->getBody(), true);

        }

        return true;
    }


    // planning jobs selected
    public function planningjobsSelectedJobs(Request $request){

        foreach ($request->ids as $jobId) {

           $job = Job::where('id',$jobId)->first();


           $client = new Client();
           $websiteUrl = env('WEBSITE_URL_JOB_PlanningJob');

           $companyData = Company::findOrFail($job->company_id);
           $categories = $job->selectedCategories()->pluck('category_id')->toArray();
           $categoryId = $categories[0] ?? 3;


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

                       'address' => $request->address ?? '',
                       'neighborhood' => $request->neighborhood ?? '',
                       'locality' => $request->locality ?? '',
                       'place' => $request->place ?? '',
                       'district' => $request->district ?? '',
                       'postcode' => $request->postcode ?? '',
                       'region' => $request->region ?? '',
                       'country' => $request->country ?? '',
                       'long' => $request->long ?? '',
                       'lat' => $request->lat ?? '',
                       'exact_location' => $request->exact_location ?? '',
                   ]
               ]);

            //    return json_decode($response->getBody(), true);

        }

        return true;
    }

}
