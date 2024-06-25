<?php

namespace App\Http\Traits;

use App\Models\Job;
use App\Models\Tag;
use App\Models\JobRole;
use App\Models\JobType;
use App\Models\Setting;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TagTranslation;
use Illuminate\Support\Facades\DB;
use Modules\Location\Entities\Country;
use Modules\Language\Entities\Language;

trait JobableApi
{
    private function getJobs($request)
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

            if ($tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
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
        $selected_country = request()->header('selected_country');

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

        $jobs = $query->latest()->paginate(12)->withQueryString();

        return [
            'total_jobs' => $jobs->total(),
            'jobs' => $jobs,
            'countries' => Country::all(['id', 'name', 'slug']),
            'categories' => JobCategory::all(),
            'job_roles' => JobRole::all(),
            'max_salary' => \DB::table('jobs')->max('max_salary'),
            'min_salary' => \DB::table('jobs')->max('min_salary'),
            'experiences' => Experience::all(),
            'educations' => Education::all(),
            'job_types' => JobType::all(),
            'popularTags' => $this->popularTags(),
        ];
    }

    private function getJobDetails($job)
    {
        if (auth('sanctum')->user()) {

            $job_details = $job->load([
                'bookmarkJobs', 'benefits', 'education',
                'experience', 'tags', 'role',
                'company.user' => function ($q) {
                    return $q->with('contactInfo', 'socialInfo');
                },
                'appliedJobs' => function ($q) {
                    return $q->where('candidate_id',  auth('sanctum')->user()->candidate ? auth('sanctum')->user()->candidate->id : '');
                }
            ])
                ->loadCount([
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id',  auth('sanctum')->user()->candidate ? auth('sanctum')->user()->candidate->id : '');
                    }, 'appliedJobs as applied' => function ($q) {
                        $q->where('candidate_id',  auth('sanctum')->user()->candidate ? auth('sanctum')->user()->candidate->id : '');
                    }
                ]);
        } else {

            $job_details = $job->load([
                'benefits', 'education', 'experience', 'tags', 'role',
                'company.user' => function ($q) {
                    return $q->with('contactInfo', 'socialInfo');
                },
            ])->loadCount([
                'bookmarkJobs', 'appliedJobs',
                'bookmarkJobs as bookmarked' => function ($q) {
                    $q->where('candidate_id',  '');
                }, 'appliedJobs as applied' => function ($q) {
                    $q->where('candidate_id',  '');
                }
            ]);
        }

        // Related Jobs With Single && Multiple Country Base
        if (auth('sanctum')->user()) {
            $related_jobs_query = Job::query()->withoutEdited()->active()->where('id', '!=', $job->id)->where('category_id', $job->category_id);
            $setting = Setting::first();
            if ($setting->app_country_type == 'single_base') {
                if ($setting->app_country) {

                    $country = Country::where('id', $setting->app_country)->first();
                    if ($country) {
                        $related_jobs_query->where('country', 'LIKE', "%$country->name%");
                    }
                }
            } else {
                $selected_country = request()->header('selected_country');

                if ($selected_country && $selected_country != null) {
                    $country = $selected_country;
                    $related_jobs_query->where('country', 'LIKE', "%$country%");
                }
            }
            $related_jobs = $related_jobs_query->latest()->limit(18)
                ->withCount([
                    'bookmarkJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id',  auth('sanctum')->user()->candidate ? auth('sanctum')->user()->candidate->id : '');
                    }
                ])->get();
        } else {
            $related_jobs_query = Job::query()->withoutEdited()->active()->where('id', '!=', $job->id)->where('category_id', $job->category_id);
            $setting = Setting::first();
            if ($setting->app_country_type == 'single_base') {
                if ($setting->app_country) {

                    $country = Country::where('id', $setting->app_country)->first();
                    if ($country) {
                        $related_jobs_query->where('country', 'LIKE', "%$country->name%");
                    }
                }
            } else {
                $selected_country = request()->header('selected_country');

                if ($selected_country && $selected_country != null) {
                    $country = $selected_country;
                    $related_jobs_query->where('country', 'LIKE', "%$country%");
                }
            }
            $related_jobs = $related_jobs_query->latest()->limit(18)
                ->withCount([
                    'bookmarkJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id', '');
                    }
                ])->get();
        }

        if (auth('sanctum')->check() && auth('sanctum')->user()->role == 'candidate') {
            $resumes = auth('sanctum')->user()->candidate->resumes;
        } else {
            $resumes = [];
        }

        return [
            'job' => $job_details,
            'related_jobs' => $related_jobs,
            'resumes' => $resumes,
        ];
    }

    private function getIndeedJobs(Request $request, $limit = null)
    {
        if (config('zakirsoft.indeed_active') && config('zakirsoft.indeed_id')) {
            $keyword = $request->keyword ?? '';
            $category = $request->category ? JobCategory::whereId($request->category)->first() : '';
            $role = $request->job_role ? JobRole::whereId($request->category)->first() : '';
            $keywords = $keyword ? $keyword : ($category ? $category->name : ($role ? $role->name : 'job'));
            $location = $request->location;

            $q       = $keywords;
            $l       = $location ?? '';
            $limit   = $limit ?? (config('zakirsoft.indeed_limit') ?? 10);
            $start   = '';
            $end     = '';
            $sort    = 'date';
            $jt      = '';
            $fromage = '';
            $radius  = '';
            $data    = array(
                'publisher' => config('zakirsoft.indeed_id'),
                'v' => 2,
                'format' => 'json',
                'q' => $q,
                'l' => $l,
                'jt' => $jt,
                'fromage' => $fromage,
                'limit' => $limit,
                'start' => $start,
                'end' => $end,
                'radius' => $radius,
                'sort' => $sort,
                'highlight' => 1,
                'filter' => 1,
                // 'latlong' => 1,
                // 'co' => 'uk',
                // 'co' => 'United Kingdom'
            );
            $param   = http_build_query($data) . "\n";
            $url     = 'http://api.indeed.com/ads/apisearch?' . $param;

            header('Content-type: application/json');
            $obj = file_get_contents($url);
            $json_decode = json_decode($obj);
            return $json_decode;
        }
    }

    public function getCareerjetJobs(Request $request, $pagesize = null)
    {
        if (config('zakirsoft.careerjet_active') && config('zakirsoft.careerjet_id')) {
            $keyword = $request->keyword ?? '';
            $category = $request->category ? JobCategory::whereId($request->category)->first() : '';
            $role = $request->job_role ? JobRole::whereId($request->category)->first() : '';
            $keywords = $keyword ? $keyword : ($category ? $category->name : ($role ? $role->name : 'job'));
            $location = $request->location;

            $page = 1;
            $pagesize =  $pagesize ?? (config('zakirsoft.careerjet_limit') ?? 10);
            $result = $this->search(array(
                'keywords' => $keywords ?? '',
                'location' => $location ?? '',
                'page' => $page,
                'sort' => 'date',
                'pagesize' => $pagesize,
                'affid' => config('zakirsoft.careerjet_id'),
            ));

            return $result;
        }
    }

    function call($fname, $args)
    {
        $locale = config('zakirsoft.careerjet_default_locale');
        $url = 'http://public.api.careerjet.net/' . $fname . '?locale_code=' . $locale;

        if (empty($args['affid'])) {
            return (object) array(
                'type' => 'ERROR',
                'error' => "Your Careerjet affiliate ID needs to be supplied. If you don't " .
                    "have one, open a free Careerjet partner account."
            );
        }

        foreach ($args as $key => $value) {
            $url .= '&' . $key . '=' . urlencode($value);
        }

        if (empty($_SERVER['REMOTE_ADDR'])) {
            return (object) array(
                'type' => 'ERROR',
                'error' => 'not running within a http server'
            );
        }

        $ip = $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER["HTTP_CF_CONNECTING_IP"] # when behind cloudflare
            ?? $_SERVER['HTTP_X_FORWARDED']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['HTTP_FORWARDED']
            ?? $_SERVER['HTTP_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '0.0.0.0';

        $url .= '&user_ip=' . $ip;
        $url .= '&user_agent=' . urlencode($_SERVER['HTTP_USER_AGENT']);

        // determine current page
        $current_page_url = '';
        if (!empty($_SERVER["SERVER_NAME"]) && !empty($_SERVER["REQUEST_URI"])) {
            $current_page_url = 'http';
            if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $current_page_url .= "s";
            }
            $current_page_url .= "://";

            if (!empty($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
                $current_page_url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $current_page_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
        }

        $version = '3.6';
        $header = "User-Agent: careerjet-api-client-v" . $version . "-php-v" . phpversion();
        if ($current_page_url) {
            $header .= "\nReferer: " . $current_page_url;
        }

        header('Content-type: application/json');
        $obj = file_get_contents($url);
        $json_decode = json_decode($obj);
        return $json_decode;
    }

    function search($args)
    {
        $result =  $this->call('search', $args);
        if ($result->type == 'ERROR') {
            trigger_error($result->error);
        }
        return $result;
    }

    public function location_filter($request)
    {
        $latitude = $request->lat;
        $longitude = $request->long;

        if ($request->has('radius') && $request->radius != null) {
            $distance = $request->radius;
        } else {
            $distance = 50;
        }

        $haversine = "(
                    6371 * acos(
                        cos(radians(" . $latitude . "))
                        * cos(radians(`lat`))
                        * cos(radians(`long`) - radians(" . $longitude . "))
                        + sin(radians(" . $latitude . ")) * sin(radians(`lat`))
                    )
                )";

        $data = Job::select('id')->selectRaw("$haversine AS distance")
            ->having("distance", "<=", $distance)->get();

        $ids = [];

        foreach ($data as $id) {
            array_push($ids, $id->id);
        }

        return $ids;
    }

    public function jobTagsInsert($tags, $job)
    {
        if ($tags) {
            $tagsArray = [];

            foreach ($tags as $tag) {
                $taggable = TagTranslation::where('tag_id', $tag)->orWhere('name', $tag)->first();

                if (!$taggable) {
                    $new_tag = Tag::create(['name' => $tag]);

                    $languages = Language::all();
                    foreach ($languages as $language) {
                        $new_tag->translateOrNew($language->code)->name = $tag;
                    }
                    $new_tag->save();

                    array_push($tagsArray, $new_tag->id);
                } else {
                    array_push($tagsArray, $tag);
                }
            }

            $job->tags()->attach($tagsArray);
        }
    }

    public function jobTagsSync($tags, $job)
    {
        if ($tags) {
            $tagsArray = [];

            foreach ($tags as $tag) {
                $taggable = TagTranslation::where('tag_id', $tag)->orWhere('name', $tag)->first();

                if (!$taggable) {
                    $new_tag = Tag::create(['name' => $tag]);

                    $languages = Language::all();
                    foreach ($languages as $language) {
                        $new_tag->translateOrNew($language->code)->name = $tag;
                    }
                    $new_tag->save();

                    array_push($tagsArray, $new_tag->id);
                } else {
                    array_push($tagsArray, $tag);
                }
            }

            $job->tags()->sync($tagsArray);
        }
    }

    public function jobBenefitsInsert($benefits, $job)
    {
        if ($benefits && count($benefits)) {
            $job->benefits()->attach($benefits);
        }
    }

    public function jobBenefitsSync($benefits, $job)
    {
        if ($benefits && count($benefits)) {
            $job->benefits()->sync($benefits);
        }
    }

    public function popularTags()
    {
        return Tag::popular()->withCount('tags')->latest('tags_count')->get()->take(10);
    }
}
