<?php

namespace App\Http\Traits;

use App\Models\Benefit;
use App\Models\BenefitTranslation;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobRole;
use App\Models\JobType;
use App\Models\Skill;
use App\Models\SkillTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Notifications\Website\Candidate\ApplyJobNotification;
use App\Notifications\Website\Candidate\BookmarkJobNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Location\Entities\Country;

trait JobAble
{
    protected function getJobs($request)
    {
        $filteredJobs = $this->filterJobs($request)->latest();
        $featured_jobs = $this->filterJobs($request)->latest()->where('featured', 1)->take(18)->get();
        $jobs = $filteredJobs->paginate(18)->withQueryString();

        return [
            'total_jobs' => $jobs->total(),
            'jobs' => $jobs,
            'featured_jobs' => $featured_jobs,
            'countries' => Country::all(['id', 'name', 'slug']),
            'categories' => JobCategory::all()->sortBy('name'),
            'job_roles' => JobRole::all()->sortBy('name'),
            'max_salary' => \DB::table('jobs')->max('max_salary'),
            'min_salary' => \DB::table('jobs')->max('min_salary'),
            'experiences' => Experience::all(),
            'educations' => Education::all(),
            'job_types' => JobType::all(),
            'skills' => Skill::all()->sortBy('name'),
            'popularTags' => $this->popularTags(),
        ];
    }

    protected function moreJobs($request)
    {
        $jobs = $this->filterJobs($request)->latest()->take(18)->get();

        return $jobs;
    }

    private function filterJobs($request)
    {
        if (auth()->user()) {
            $query = Job::with('company.user', 'category', 'job_type:id,name')
                ->withCount([
                    'bookmarkJobs',
                    'appliedJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id', currentCandidate() ? currentCandidate()->id : '');
                    },
                    'appliedJobs as applied' => function ($q) {
                        $q->where('candidate_id', currentCandidate() ? currentCandidate()->id : '');
                    },
                ])
                ->active()
                ->withoutEdited();
        } else {
            $query = Job::with('company.user', 'category', 'job_type:id,name')
                ->withCount([
                    'bookmarkJobs',
                    'appliedJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id', '');
                    },
                    'appliedJobs as applied' => function ($q) {
                        $q->where('candidate_id', '');
                    },
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
            $keyword = $request->get('keyword');
            if (is_array($keyword)) {
                $keyword = $keyword[0];
            }
            $query->where('title', 'LIKE', "%$keyword%");
        }

        // Category filter
        if ($request->has('category') && $request->category != null) {
            $category = JobCategory::where('slug', $request->category)->first();
            $query->when($category, function ($query) use ($category) {
                return $query->where('category_id', $category->id);
            });
        }

        // job role filter
        if ($request->has('job_role') && $request->job_role != null) {
            $query->where('role_id', $request->job_role);
        }

        // Salary filter
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

        // id filter for load more
        if ($request->id) {
            $query->where('id', '<', $request->id);
        }

        // location
        $final_address = '';
        if ($request->has('location') && $request->location != null) {
            $adress = $request->location;
            if ($adress) {
                $adress_array = explode(' ', $adress);
                if ($adress_array) {
                    $last_two = array_splice($adress_array, 0, 2);
                }
                $final_address = Str::slug(implode(' ', $last_two));
                $query->Where('country', 'LIKE', '%'.$request->location.'%')
                    ->orWhere('address', 'LIKE', '%'.$final_address.'%');
            }
        }
        // lat Long
        if ($request->has('lat') && $request->has('long') && $request->lat != null && $request->long != null) {
            session()->forget('selected_country');
            // $query->Where('address', $final_address ? $final_address : '')->orWhere('country', $request->location ? $request->location : '');
        }

        // country
        $selected_country = session()->get('selected_country');

        if ($selected_country && $selected_country != null) {
            $country = selected_country()->name;
            $query->where('country', 'LIKE', "%$country%");
        } else {
            $setting = loadSetting();
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

        // SKill filter
        if ($request->has('skills') && $request->skills != null) {
            $skills = SkillTranslation::where('name', $request->skills)->first();

            if ($skills) {
                $query->whereHas('skills', function ($q) use ($skills) {
                    $q->where('job_skills.skill_id', $skills->skill_id);
                });
            }
        }

        return $query;
    }

    private function getJobsCategory($request, $slug)
    {
        if (auth()->user()) {

            $query = Job::with('company.user', 'job_type:id,name')
                ->withCount([
                    'bookmarkJobs', 'appliedJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id', currentCandidate() ? currentCandidate()->id : '');
                    }, 'appliedJobs as applied' => function ($q) {
                        $q->where('candidate_id', currentCandidate() ? currentCandidate()->id : '');
                    },
                ])
                ->active()->withoutEdited();
        } else {

            $query = Job::with('company.user', 'job_type:id,name')
                ->withCount([
                    'bookmarkJobs', 'appliedJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id', '');
                    }, 'appliedJobs as applied' => function ($q) {
                        $q->where('candidate_id', '');
                    },
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

        $id = JobCategory::where('slug', $slug)->first();
        if ($id == null) {
            abort(404);
        } else {
            $category_id = $id->id;
            $query->where('category_id', $category_id);
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
                $adress_array = explode(' ', $adress);
                if ($adress_array) {
                    $last_two = array_splice($adress_array, -2);
                }
                $final_address = Str::slug(implode(' ', $last_two));
            }
        }
        // lat Long
        if ($request->has('lat') && $request->has('long') && $request->lat != null && $request->long != null) {
            session()->forget('selected_country');
            $query->Where('address', $final_address ? $final_address : '')
                ->orWhere('country', $request->location ? $request->location : '');
        }

        // country
        $selected_country = session()->get('selected_country');

        if ($selected_country && $selected_country != null) {
            $country = selected_country()->name;
            $query->where('country', 'LIKE', "%$country%");
        } else {

            $setting = loadSetting();
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

        $featured_jobs = $query->latest()->where('featured', 1)->take(20)->get();
        $jobs = $query->latest()->paginate(20)->withQueryString();

        return [
            'total_jobs' => $jobs->total(),
            'jobs' => $jobs,
            'featured_jobs' => $featured_jobs,
            'countries' => Country::all(['id', 'name', 'slug']),
            'categories' => JobCategory::all()->sortBy('name'),
            'job_roles' => JobRole::all()->sortBy('name'),
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
        if (auth()->user()) {
            $job_details = $job
                ->load([
                    'benefits',
                    'education',
                    'experience',
                    'tags',
                    'role',
                    'questions',
                    'job_type',
                    'company.user' => function ($q) {
                        return $q->with('contactInfo', 'socialInfo');
                    },
                ])
                ->loadCount([
                    'bookmarkJobs',
                    'appliedJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id', currentCandidate() ? currentCandidate()->id : '');
                    },
                    'appliedJobs as applied' => function ($q) {
                        $q->where('candidate_id', currentCandidate() ? currentCandidate()->id : '');
                    },
                ]);
        } else {
            $job_details = $job
                ->load([
                    'benefits',
                    'education',
                    'experience',
                    'tags',
                    'role',
                    'questions',
                    'job_type',
                    'company.user' => function ($q) {
                        return $q->with('contactInfo', 'socialInfo');
                    },
                ])
                ->loadCount([
                    'bookmarkJobs',
                    'appliedJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id', '');
                    },
                    'appliedJobs as applied' => function ($q) {
                        $q->where('candidate_id', '');
                    },
                ]);
        }

        // Related Jobs With Single && Multiple Country Base
        if (auth()->user()) {
            $related_jobs_query = Job::query()
                ->withoutEdited()
                ->active()
                ->with([
                    'benefits',
                    'education',
                    'experience',
                    'tags',
                    'role',
                    'questions',
                    'job_type',
                    'company.user' => function ($q) {
                        return $q->with('contactInfo', 'socialInfo');
                    },
                ])
                ->where('id', '!=', $job->id)
                ->where('category_id', $job->category_id);
            $setting = loadSetting();
            if ($setting->app_country_type == 'single_base') {
                if ($setting->app_country) {
                    $country = Country::where('id', $setting->app_country)->first();
                    if ($country) {
                        $related_jobs_query->where('country', 'LIKE', "%$country->name%");
                    }
                }
            } else {
                $selected_country = session()->get('selected_country');

                if ($selected_country && $selected_country != null) {
                    $country = selected_country()->name;
                    $related_jobs_query->where('country', 'LIKE', "%$country%");
                }
            }
            $related_jobs = $related_jobs_query
                ->latest()
                ->limit(12)
                ->withCount([
                    'bookmarkJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id', currentCandidate() ? currentCandidate()->id : '');
                    },
                ])
                ->get();
        } else {
            $related_jobs_query = Job::query()
                ->withoutEdited()
                ->active()
                ->with([
                    'benefits',
                    'education',
                    'experience',
                    'tags',
                    'role',
                    'questions',
                    'job_type',
                    'company.user' => function ($q) {
                        return $q->with('contactInfo', 'socialInfo');
                    },
                ])
                ->where('id', '!=', $job->id)
                ->where('category_id', $job->category_id);
            $setting = loadSetting();
            if ($setting->app_country_type == 'single_base') {
                if ($setting->app_country) {
                    $country = Country::where('id', $setting->app_country)->first();
                    if ($country) {
                        $related_jobs_query->where('country', 'LIKE', "%$country->name%");
                    }
                }
            } else {
                $selected_country = session()->get('selected_country');

                if ($selected_country && $selected_country != null) {
                    $country = selected_country()->name;
                    $related_jobs_query->where('country', 'LIKE', "%$country%");
                }
            }
            $related_jobs = $related_jobs_query
                ->latest()
                ->limit(18)
                ->withCount([
                    'bookmarkJobs',
                    'bookmarkJobs as bookmarked' => function ($q) {
                        $q->where('candidate_id', '');
                    },
                ])
                ->get();
        }

        if (auth('user')->check() && authUser()->role == 'candidate') {
            $resumes = currentCandidate()->resumes;
        } else {
            $resumes = [];
        }

        return [
            'job' => $job_details,
            'related_jobs' => $related_jobs,
            'resumes' => $resumes,
        ];
    }

    private function getIndeedJobs(Request $request, $limit = null, $page = null)
    {
        if (config('templatecookie.default_job_provider') == 'indeed' && config('templatecookie.indeed_id')) {
            $keyword = $request->keyword ?? '';
            // $id = JobCategory::where('slug', $request->category)->first();
            // $category_id = $id->id;
            // $category = $category_id ? JobCategory::whereId($category_id)->first() : '';
            // $role = $request->job_role ? JobRole::whereId($category_id)->first() : '';
            $keywords = $keyword;
            $location = $request->location;

            $q = $keywords;
            $l = $location ?? '';
            $limit = $limit ?? (config('templatecookie.indeed_limit') ?? 10);
            $start = '';
            $end = '';
            $sort = 'date';
            $jt = '';
            $fromage = '';
            $radius = '';
            $data = [
                'publisher' => config('templatecookie.indeed_id'),
                'v' => 2,
                'format' => 'json',
                'q' => $q,
                'l' => $l,
                'jt' => $jt,
                'fromage' => $page ?? $fromage,
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
            ];
            $param = http_build_query($data)."\n";
            $url = 'http://api.indeed.com/ads/apisearch?'.$param;

            header('Content-type: application/json');
            $obj = file_get_contents($url);
            $json_decode = json_decode($obj);

            return $json_decode;
        }

        return [];
    }

    private function getIndeedJobsCategory(Request $request, $limit, $page, $slug)
    {
        if (config('templatecookie.default_job_provider') == 'indeed' && config('templatecookie.indeed_id')) {
            $keyword = $request->keyword ?? '';
            $id = JobCategory::where('slug', $slug)->first();
            $category_id = $id->id;
            $category = $category_id ? JobCategory::whereId($category_id)->first() : '';
            $role = $request->job_role ? JobRole::whereId($category_id)->first() : '';
            $keywords = $keyword ? $keyword : ($category ? $category->name : ($role ? $role->name : 'job'));
            $location = $request->location;

            $q = $keywords;
            $l = $location ?? '';
            $limit = $limit ?? (config('templatecookie.indeed_limit') ?? 10);
            $start = '';
            $end = '';
            $sort = 'date';
            $jt = '';
            $fromage = '';
            $radius = '';
            $data = [
                'publisher' => config('templatecookie.indeed_id'),
                'v' => 2,
                'format' => 'json',
                'q' => $q,
                'l' => $l,
                'jt' => $jt,
                'fromage' => $page ?? $fromage,
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
            ];
            $param = http_build_query($data)."\n";
            $url = 'http://api.indeed.com/ads/apisearch?'.$param;

            header('Content-type: application/json');
            $obj = file_get_contents($url);
            $json_decode = json_decode($obj);

            return $json_decode;
        }

        return [];
    }

    public function getCareerjetJobs(Request $request, $pagesize = null, $page = null)
    {
        if (config('templatecookie.default_job_provider') == 'careerjet' && config('templatecookie.careerjet_id')) {
            $keyword = $request->keyword ?? '';
            // $id = JobCategory::where('slug', $slug)->first();
            // $category_id = $id->id;
            // $category = $category_id ? JobCategory::whereId($category_id)->first() : '';
            // $role = $request->job_role ? JobRole::whereId($category_id)->first() : '';
            $keywords = $keyword;
            $location = $request->location;

            $page = $page ?? 1;
            $pagesize = $pagesize ?? (config('templatecookie.careerjet_limit') ?? 10);
            $result = $this->search([
                'keywords' => $keywords ?? '',
                'location' => $location ?? '',
                'page' => $page,
                'sort' => 'date',
                'pagesize' => $pagesize,
                'affid' => config('templatecookie.careerjet_id'),

            ]);

            return $result;
        }

        return [];
    }

    public function getCareerjetJobsCategory(Request $request, $pagesize, $page, $slug)
    {
        if (config('templatecookie.default_job_provider') == 'careerjet' && config('templatecookie.careerjet_id')) {
            $keyword = $request->keyword ?? '';
            $id = JobCategory::where('slug', $slug)->first();
            $category_id = $id->id;
            $category = $category_id ? JobCategory::whereId($category_id)->first() : '';
            $role = $request->job_role ? JobRole::whereId($category_id)->first() : '';
            $keywords = $keyword ? $keyword : ($category ? $category->name : ($role ? $role->name : 'job'));
            $location = $request->location;

            $page = $page ?? 1;
            $pagesize = $pagesize ?? (config('templatecookie.careerjet_limit') ?? 10);
            $result = $this->search([
                'keywords' => $keywords ?? '',
                'location' => $location ?? '',
                'page' => $page,
                'sort' => 'date',
                'pagesize' => $pagesize,
                'affid' => config('templatecookie.careerjet_id'),

            ]);

            return $result;
        }

        return [];
    }

    public function call($fname, $args)
    {
        $locale = config('templatecookie.careerjet_default_locale');
        $url = 'http://public.api.careerjet.net/'.$fname.'?locale_code='.$locale;

        if (empty($args['affid'])) {
            return (object) [
                'type' => 'ERROR',
                'error' => "Your Careerjet affiliate ID needs to be supplied. If you don't ".'have one, open a free Careerjet partner account.',
            ];
        }

        foreach ($args as $key => $value) {
            $url .= '&'.$key.'='.urlencode($value);
        }

        if (empty($_SERVER['REMOTE_ADDR'])) {
            return (object) [
                'type' => 'ERROR',
                'error' => 'not running within a http server',
            ];
        }

        $ip =
            $_SERVER['HTTP_CLIENT_IP'] ??
            ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? // when behind cloudflare
                ($_SERVER['HTTP_X_FORWARDED'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['HTTP_FORWARDED'] ?? ($_SERVER['HTTP_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'))))));

        $url .= '&user_ip='.$ip;
        $url .= '&user_agent='.urlencode($_SERVER['HTTP_USER_AGENT']);

        // determine current page
        $current_page_url = '';
        if (! empty($_SERVER['SERVER_NAME']) && ! empty($_SERVER['REQUEST_URI'])) {
            $current_page_url = 'http';
            if (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $current_page_url .= 's';
            }
            $current_page_url .= '://';

            if (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
                $current_page_url .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
            } else {
                $current_page_url .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
            }
        }

        $version = '3.6';
        $header = 'User-Agent: careerjet-api-client-v'.$version.'-php-v'.phpversion();
        if ($current_page_url) {
            $header .= "\nReferer: ".$current_page_url;
        }

        header('Content-type: application/json');
        $obj = file_get_contents($url);
        $json_decode = json_decode($obj);

        return $json_decode;
    }

    public function search($args)
    {
        $result = $this->call('search', $args);
        if ($result->type == 'ERROR') {
            trigger_error($result->error);
        }

        return $result;
    }

    public function jobTagsInsert($tags, $job)
    {
        if ($tags) {
            $tagsArray = [];

            foreach ($tags as $tag) {
                $tag_exists = TagTranslation::where('name', $tag)->first();

                if ($tag_exists) {
                    $taggable = TagTranslation::where('tag_id', $tag)
                        ->orWhere('name', $tag)
                        ->exists();

                    if (! $taggable) {
                        $new_tag = Tag::create(['name' => $tag]);

                        $languages = loadLanguage();
                        foreach ($languages as $language) {
                            $new_tag->translateOrNew($language->code)->name = $tag;
                        }
                        $new_tag->save();

                        array_push($tagsArray, $new_tag->id);
                    } else {
                        array_push($tagsArray, $tag);
                    }
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
                $tag_exists = TagTranslation::where('name', $tag)->first();

                if ($tag_exists) {
                    $taggable = TagTranslation::where('tag_id', $tag)
                        ->orWhere('name', $tag)
                        ->first();

                    if (! $taggable) {
                        $new_tag = Tag::create(['name' => $tag]);

                        $languages = loadLanguage();
                        foreach ($languages as $language) {
                            $new_tag->translateOrNew($language->code)->name = $tag;
                        }
                        $new_tag->save();

                        array_push($tagsArray, $new_tag->id);
                    } else {
                        array_push($tagsArray, $tag);
                    }
                }
            }

            $job->tags()->sync($tagsArray);
        }
    }

    public function jobSkillsInsert($skills, $job)
    {
        if ($skills) {
            $skillsArray = [];

            foreach ($skills as $skill) {
                $skill_exists = SkillTranslation::where('skill_id', $skill)
                    ->orWhere('name', $skill)
                    ->first();

                if (! $skill_exists) {
                    $select_skill = Skill::create(['name' => $skill]);

                    $languages = loadLanguage();
                    foreach ($languages as $language) {
                        $select_skill->translateOrNew($language->code)->name = $skill;
                    }
                    $select_skill->save();

                    array_push($skillsArray, $select_skill->id);
                } else {
                    array_push($skillsArray, $skill_exists->skill_id);
                }
            }

            $job->skills()->attach($skillsArray);
        }
    }

    public function jobSkillsSync($skills, $job)
    {
        if ($skills) {
            $skillsArray = [];

            foreach ($skills as $skill) {
                $skill_exists = SkillTranslation::where('skill_id', $skill)
                    ->orWhere('name', $skill)
                    ->first();

                if (! $skill_exists) {
                    $select_skill = Skill::create(['name' => $skill]);

                    $languages = loadLanguage();
                    foreach ($languages as $language) {
                        $select_skill->translateOrNew($language->code)->name = $skill;
                    }
                    $select_skill->save();

                    array_push($skillsArray, $select_skill->id);
                } else {
                    array_push($skillsArray, $skill_exists->skill_id);
                }
            }

            $job->skills()->sync($skillsArray);
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
        return Tag::popular()
            ->withCount('tags')
            ->latest('tags_count')
            ->get()
            ->take(10);
    }

    /**
     * Toggle bookmark job
     *
     * @return void
     */
    public function toggleBookmarkJob(Job $job)
    {
        $check = $job->bookmarkJobs()->toggle(currentCandidate());

        if ($check['attached'] == [1]) {
            $user = authUser();
            // make notification to company candidate bookmark job
            Notification::send($job->company->user, new BookmarkJobNotification($user, $job));

            // make notification to candidate for notify
            if (auth()->user()->recent_activities_alert) {
                Notification::send(authUser(), new BookmarkJobNotification($user, $job));
            }
        }

        $check['attached'] == [1] ? ($message = __('job_added_to_favorite_list')) : ($message = __('job_removed_from_favorite_list'));

        flashSuccess($message);

        return back();
    }

    /**
     * Toggle apply job
     *
     * @param  Candidate  $candidate
     * @return void
     */
    public function toggleApplyJob(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'resume_id' => 'required',
                'cover_letter' => 'required',
            ],
            [
                'resume_id.required' => 'Please select resume',
                'cover_letter.required' => 'Please enter cover letter',
            ],
        );

        if ($validator->fails()) {
            flashError($validator->errors()->first());

            return back();
        }

        if (currentCandidate()->profile_complete != 0) {
            flashError(__('complete_your_profile_before_applying_to_jobs_add_your_information_resume_and_profile_picture_for_a_better_chance_of_getting_hired'));

            return redirect()->route('candidate.dashboard');
        }

        $candidate = currentCandidate();
        $job = Job::find($request->id);

        DB::table('applied_jobs')->insert([
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
            'cover_letter' => $request->cover_letter,
            'candidate_resume_id' => $request->resume_id,
            'application_group_id' => $job->company->applicationGroups->where('is_deleteable', false)->first()->id ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // make notification to candidate and company for notify
        $job->company->user->notify(new ApplyJobNotification(authUser(), $job->company->user, $job));

        if (authUser()->recent_activities_alert) {
            auth('user')
                ->user()
                ->notify(new ApplyJobNotification(authUser(), $job->company->user, $job));
        }

        flashSuccess(__('job_applied_successfully'));

        return back();
    }

    /**
     * job benefit create
     *
     * @return Renderable
     */
    public function jobBenefitCreate(Request $request)
    {
        $benefit = $request->benefit;
        $languages = loadLanguage();
        $benefit_exists = BenefitTranslation::where('name', $benefit)->first();

        if ($benefit_exists) {
            return __('benefit_already_exists');
        }

        $translation = new Benefit();
        $translation->company_id = currentCompany()->id;
        $translation->save();

        foreach ($languages as $language) {
            $translation->translateOrNew($language->code)->name = $benefit;
        }

        $translation->save();

        return $translation;
    }
}
