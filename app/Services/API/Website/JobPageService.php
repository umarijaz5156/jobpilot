<?php

namespace App\Services\API\Website;

use App\Http\Resources\Job\JobListResource;
use App\Models\Job;
use App\Models\JobType;
use App\Models\Setting;
use App\Models\Education;
use App\Models\Experience;
use Illuminate\Support\Str;
use App\Http\Traits\Jobable;
use App\Models\TagTranslation;
use Modules\Location\Entities\Country;

class JobPageService
{
    use Jobable;

    public function execute($request)
    {
        if (auth()->user()) {
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
            $adress = $request->location;
            if ($adress) {
                $adress_array = explode(" ", $adress);
                if ($adress_array) {
                    $last_two = array_splice($adress_array, -2);
                }
                $final_address = Str::slug(implode(" ", $last_two));
            }
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
        $selected_country = session()->get('selected_country');

        if ($selected_country && $selected_country != null) {
            $country = selected_country()->name;
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

        return JobListResource::collection($query->latest()->paginate(12)->withQueryString());
    }
}
