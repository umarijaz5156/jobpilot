<?php

namespace App\Services\API\Website;

use App\Http\Resources\Home\FeaturedJobsResource;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobRole;
use App\Models\Setting;
use App\Models\Candidate;
use App\Models\JobCategory;
use Modules\Location\Entities\Country;
use App\Http\Resources\Home\PopularRolesResource;
use App\Http\Resources\Home\PopularCategoriesResource;
use App\Http\Resources\Home\TopCompaniesResource;
use App\Http\Traits\HasCountryBasedJobsApi;
use Modules\Testimonial\Entities\Testimonial;

/**
 * Class HomePageService
 * @package App\Services
 */
class HomePageService
{
    use HasCountryBasedJobsApi;

    public function execute(){
        $states = [
            'live_jobs' => livejob(),
            'new_jobs' => $this->filterCountryBasedJobs(Job::withoutEdited()->newJobs())->count(),
            'companies' => Company::count(),
            'candidates' => Candidate::count(),
        ];

        // Featured Jobs With Single && Multiple Country Base
        $featured_jobs_query = Job::query()->withoutEdited()->with('company', 'job_type:id,name')->withCount([
            'bookmarkJobs', 'appliedJobs',
            'bookmarkJobs as bookmarked' => function ($q) {
                $q->where('candidate_id',  auth('sanctum')->check() && auth('sanctum')->user()->candidate ? auth('sanctum')->user()->candidate->id : '');
            }, 'appliedJobs as applied' => function ($q) {
                $q->where('candidate_id',  auth('sanctum')->check() && auth('sanctum')->user()->candidate ? auth('sanctum')->user()->candidate->id : '');
            }
        ]);
        $featured_jobs = $this->filterCountryBasedJobs($featured_jobs_query)->where('featured', 1)->active()->get()->take(6);

        $setting = Setting::first();
        $is_single_base_country_type = $setting->app_country_type == 'single_base' ? true : false;

        // Popular Roles
        $popular_roles_list = JobRole::withCount('jobs')->latest('jobs_count')->take(8)->get()->map(function ($role) use ($setting, $is_single_base_country_type) {
            if ($is_single_base_country_type) {
                if ($setting->app_country) {

                    $country = Country::where('id', $setting->app_country)->first();
                    if ($country) {
                        $role->open_position_count = $role->jobs()->where('country', 'LIKE', "%$country->name%")->openPosition()->count();
                    }
                }
            } else {
                $selected_country = request()->header('selected_country');

                if ($selected_country && $selected_country != null) {
                    $role->open_position_count = $role->jobs()->where('country', 'LIKE', "%$selected_country%")->openPosition()->count();
                }else{
                    $role->open_position_count = $role->jobs()->openPosition()->count();
                }
            }

            return $role;
        });
        $popular_roles = $popular_roles_list->sortBy('open_position_count');

        // Popular Categories
        $popular_categories_list = JobCategory::withCount('jobs')->latest('jobs_count')->get()->take(8)->map(function($category) use ($setting, $is_single_base_country_type) {
            if ($is_single_base_country_type) {
                if ($setting->app_country) {

                    $country = Country::where('id', $setting->app_country)->first();
                    if ($country) {
                        $category->open_position_count = $category->jobs()->where('country', 'LIKE', "%$country->name%")->openPosition()->count();
                    }
                }
            } else {
                $selected_country = request()->header('selected_country');

                if ($selected_country && $selected_country != null) {
                    // $country = selected_country()->name;
                    $category->open_position_count = $category->jobs()->where('country', 'LIKE', "%$selected_country%")->openPosition()->count();
                }else{
                    $category->open_position_count = $category->jobs()->openPosition()->count();
                }
            }

            return $category;
        })->sortBy('open_position_count');
        $popular_categories = $popular_categories_list->sortBy('open_position_count');

        // Top Companies
        $top_companies = Company::with('user.contactInfo')
            ->withCount([
                'jobs as jobs_count' => function ($q) {
                    $q->where('status', 'active');
                    $q = $this->filterCountryBasedJobs($q);
                }
            ])
            ->latest('jobs_count')
            ->get()
            ->take(9);

        // Top Categories
        $top_categories = JobCategory::withCount('jobs')->latest('jobs_count')->get()->take(8)->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name
            ];
        });

        return [
            'top_companies' => TopCompaniesResource::collection($top_companies),
            'featured_jobs' => FeaturedJobsResource::collection($featured_jobs),
            'states' => $states,
            'most_popular_vacancies' => PopularRolesResource::collection($popular_roles),
            'popular_categories' => PopularCategoriesResource::collection($popular_categories),
            'testimonial' =>  $this->testimonial(),
            'top_categories' => $top_categories,
        ];
    }

    public function testimonial(){
        return Testimonial::all()->transform(function($testimonial){
            return [
                'name' => $testimonial->name,
                'designation' => $testimonial->designation,
                'description' => $testimonial->description,
                'image' => $testimonial->image,
            ];
        });
    }
}








