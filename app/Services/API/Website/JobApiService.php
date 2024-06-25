<?php

namespace App\Services\API\Website;

use App\Http\Resources\Job\JobListCollection;
use App\Http\Resources\Job\JobListResource;
use App\Models\Job;
use App\Models\JobType;
use App\Models\Setting;
use App\Models\Education;
use App\Models\Experience;
use Illuminate\Support\Str;
use App\Http\Traits\JobableApi;
use App\Models\TagTranslation;
use Modules\Location\Entities\Country;

class JobApiService
{
    use JobableApi;

    public function execute($request)
    {
        if (auth('sanctum')->user()) {
            $query = Job::with('company.user', 'category', 'job_type:id,name')
                ->withCount([
                    'bookmarkJobs', 'appliedJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id',  auth('sanctum')->user()->candidate ? auth('sanctum')->user()->candidate->id : '');
                    }, 'appliedJobs as applied' => function ($q) {
                        $q->where('candidate_id',  auth('sanctum')->user()->candidate ? auth('sanctum')->user()->candidate->id : '');
                    }
                ])
                ->active()->withoutEdited();
        } else {

            $query = Job::with('company.user', 'category', 'job_type:id,name')
                ->withCount([
                    'bookmarkJobs', 'appliedJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id', '');
                    }, 'appliedJobs as applied' => function ($q) {
                        $q->where('candidate_id', '');
                    }
                ])
                ->withoutEdited()
                ->active();
        }

        // company search
        if ($request->has('company') && $request->company != null) {
            $company = $request->company;
            $query->whereHas('company.user', function ($q) use ($company) {
                $q->where('username', $company);
            });
        }

        // Keyword search
        if ($request->has('keyword') && $request->keyword != null) {
            $query->where('title', 'LIKE', "%$request->keyword%");
        }

        // Category filter
        if ($request->has('category') && $request->category != null) {
            $query->where('category_id', $request->category);
        }

        // job role filter
        if ($request->has('job_role') && $request->job_role != null) {
            $query->where('role_id', $request->job_role);
        }

        // Salery filter
        if ($request->has('price_min') && $request->price_min != null) {
            $query->where('min_salary', '>=', $request->price_min);
        }
        if ($request->has('price_max') && $request->price_max != null) {
            $query->where('max_salary', '<=', $request->price_max);
        }

         // tags filter
         if ($request->has('tag') && $request->tag != null) {
            $tag = TagTranslation::where('name', $request->tag)->first();

            if($tag){
                $query->whereHas('tags', function($q) use ($tag){
                    $q->where('job_tag.tag_id', $tag->tag_id);
                });
            }
        }

        // location
        $final_address = '';
        if ($request->has('location') && $request->location != null) {
            $location = $request->location;
            
            //previous code
            // if ($adress) {
            //     $adress_array = explode(" ", $adress);
            //     if ($adress_array) {
            //         $last_two = array_splice($adress_array, -2);
            //     }
            //     $final_address = Str::slug(implode(" ", $last_two));
            // }

            //search on country name basis in jobs
            $query->where('country', 'LIKE', "%$location%");

        }
        // lat Long
        if ($request->has('lat') && $request->has('long') && $request->lat != null && $request->long != null) {
            session()->forget('selected_country');
            $ids = $this->location_filter($request);
            $query->whereIn('id', $ids)
                ->orWhere('address', $final_address ? $final_address : '')
                ->orWhere('country', $request->location ? $request->location : '');
        }


        // country
        $selected_country = $request->header('selected_country');

        if ($selected_country && $selected_country != null) {
            $country = $selected_country;
            $query->where('country', 'LIKE', "%$country%");
        } else {

            $setting = Setting::first();
            if ($setting->app_country_type == 'single_base') {
                if ($setting->app_country) {

                    $country = Country::where('id', $setting->app_country)->first();
                    if ($country) {
                        $query->where('country', 'LIKE', "%$country->name%");
                    }
                }
            }
        }

        // Sort by ads
        if ($request->has('sort_by') && $request->sort_by != null) {
            switch ($request->sort_by) {
                case 'latest':
                    $query->latest('id');
                    break;
                case 'featured':
                    $query->where('featured', 1)->latest();
                    break;
            }
        }

        // Experience filter
        if ($request->has('experience') && $request->experience != null) {
            $experience_id = Experience::where('name', $request->experience)->value('id');
            $query->where('experience_id', $experience_id);
        }

        // Education filter
        if ($request->has('education') && $request->education != null) {
            $education_id = Education::where('name', $request->education)->value('id');
            $query->where('education_id', $education_id);
        }

        // Work type filter
        if ($request->has('is_remote') && $request->is_remote != null) {
            $query->where('is_remote', 1);
        }

        // Job type filter
        if ($request->has('job_type') && $request->job_type != null) {
            $job_type_id = JobType::where('name', $request->job_type)->value('id');
            $query->where('job_type_id', $job_type_id);
        }

        $paginate = $request->has('paginate') ? $request->paginate : 12;
        return $query->latest()->paginate($paginate)->withQueryString()->onEachSide(0)->through(function($data){
            $salary = $data->salary_mode == 'range' ? currencyAmountShort($data->min_salary) . ' - ' . currencyAmountShort($data->max_salary) . ' ' . currentCurrencyCode() : $data->custom_salary;

            return [
                'id' => $data->id,
                'title' => $data->title,
                'slug' => $data->slug,
                'job_details' => route('website.job.details', $data->slug),
                'company_name' => $data->company && $data->company->user ? $data->company->user->name : '',
                'company_logo' => $data->company->logo_url,
                'job_type' => $data->job_type->name,
                'job_role' => $data->role->name,
                'category' => $data->category->name,
                'country' => $data->country,
                'is_featured' => $data->featured,
                'is_highlighted' => $data->highlight,
                'deadline' => $data->deadline,
                'salary' => $salary,
                'salary_mode' => $data->salary_mode,
                'min_salary' => $data->min_salary,
                'max_salary' => $data->max_salary,
                'bookmarked' => $data->bookmarked,
                'allAppliedJobs_count' => $data->all_applied_jobs_count

            ];
        });
    }
}
