<?php

namespace App\Services\Website;

use App\Http\Traits\HasCountryBasedJobs;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobRole;
use Modules\Location\Entities\Country;
use Modules\Testimonial\Entities\Testimonial;

class IndexPageService
{
    use HasCountryBasedJobs;

    /**
     * Get index page data
     *
     * @return void
     */
    public function execute(): array
    {
        $data['newjobs'] = $this->filterCountryBasedJobs(Job::withoutEdited()->newJobs())->count();

        $data['companies'] = Company::count();
        $data['candidates'] = Candidate::count();
        $data['testimonials'] = Testimonial::whereCode(currentLangCode())->get();
        $data['top_companies'] = Company::with('user', 'user.contactInfo', 'industry.translations')
            ->withCount([
                'jobs as jobs_count' => function ($q) {
                    $q->where('status', 'active');
                    $q = $this->filterCountryBasedJobs($q);
                },
            ])
            ->latest('jobs_count')
            ->get()
            ->take(9);

        // Featured Jobs With Single && Multiple Country Base
        $featured_jobs_query = Job::query()->withoutEdited()->with('company.user', 'job_type:id', 'category')->withCount([
            'bookmarkJobs', 'appliedJobs',
            'bookmarkJobs as bookmarked' => function ($q) {
                $q->where('candidate_id', auth('user')->check() && currentCandidate() ? currentCandidate()->id : '');
            }, 'appliedJobs as applied' => function ($q) {
                $q->where('candidate_id', auth('user')->check() && currentCandidate() ? currentCandidate()->id : '');
            },
        ]);
        $data['featured_jobs'] = $this->filterCountryBasedJobs($featured_jobs_query)->where('featured', 1)->deadlineActive()->active()->get()->take(6);

        $setting = loadSetting();
        $is_single_base_country_type = $setting->app_country_type == 'single_base' ? true : false;
        $popular_categories_list = JobCategory::query()
            ->withCount(['jobs' => function ($query) use ($setting, $is_single_base_country_type) {
                $country = null;
                if ($is_single_base_country_type && $setting->app_country) {
                    $country = Country::query()->find($setting->app_country);
                }
                if ($selected_country = session()->get('selected_country')) {
                    $country = Country::query()->find($selected_country);
                }

                return $query->openPosition()
                    ->when($country, function ($query) use ($country) {
                        $query->where('country', 'LIKE', "%$country->name%");
                    });

            }])
            ->orderByDesc('jobs_count')
            ->get()->take(8);

        $data['popular_categories'] = $popular_categories_list->values();

        $popular_roles_list = JobRole::withCount('jobs')->take(8)->get()->map(function ($role) use ($setting, $is_single_base_country_type) {
            if ($is_single_base_country_type) {
                if ($setting->app_country) {

                    $country = Country::where('id', $setting->app_country)->first();
                    if ($country) {
                        $role->open_position_count = $role->jobs()->where('country', 'LIKE', "%$country->name%")->openPosition()->count();
                    }
                }
            } else {
                $selected_country = session()->get('selected_country');

                if ($selected_country && $selected_country != null) {
                    $country = selected_country()->name;
                    $role->open_position_count = $role->jobs()->where('country', 'LIKE', "%$country%")->openPosition()->count();
                } else {
                    $role->open_position_count = $role->jobs()->openPosition()->count();
                }
            }

            return $role;
        });
        $data['popular_roles'] = $popular_roles_list->sortByDesc('open_position_count')->values();
        $data['top_categories'] = JobCategory::withCount('jobs')->latest('jobs_count')->get()->take(4);
        if (auth('user')->check() && authUser()->role == 'candidate') {
            $data['resumes'] = currentCandidate()->resumes;
        } else {
            $data['resumes'] = [];
        }

        return $data;
    }
}
