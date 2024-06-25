<?php

namespace App\Services\Website\Company;

use App\Models\Company;
use App\Models\Job;
use Carbon\Carbon;
use Modules\Location\Entities\Country;

class CompanyDetailsService
{
    /**
     * Get company details
     */
    public function execute($user): array
    {
        $companyDetails = Company::with('organization', 'industry', 'team_size:id')
            ->where('user_id', $user->id)
            ->withCount([
                'jobs as activejobs' => function ($q) {
                    $q->where('status', true);
                    $q->where('deadline', '>=', Carbon::now()->toDateString());
                    $selected_country = session()->get('selected_country');
                    if ($selected_country && $selected_country != null && $selected_country != 'all') {
                        $country = selected_country()->name;
                        $q->where('country', 'LIKE', "%$country%");
                    } else {
                        $setting = loadSetting();
                        if ($setting->app_country_type == 'single_base') {
                            if ($setting->app_country) {
                                $country = Country::where('id', $setting->app_country)->first();
                                if ($country) {
                                    $q->where('country', 'LIKE', "%$country->name%");
                                }
                            }
                        }
                    }
                },
            ])
            ->withCount([
                'bookmarkCandidateCompany as candidatemarked' => function ($q) {
                    $q->where('user_id', auth()->id());
                },
            ])
            ->withCasts(['candidatemarked' => 'boolean'])
            ->first();

        // open_jobs Jobs With Single && Multiple Country Base
        $open_jobs_query = Job::withoutEdited()->with('company', 'job_type');

        $setting = loadSetting();
        if ($setting->app_country_type == 'single_base') {
            if ($setting->app_country) {
                $country = Country::where('id', $setting->app_country)->first();
                if ($country) {
                    $open_jobs_query->where('country', 'LIKE', "%$country->name%");
                }
            }
        } else {
            $selected_country = session()->get('selected_country');

            if ($selected_country && $selected_country != null) {
                $country = selected_country()->name;
                $open_jobs_query->where('country', 'LIKE', "%$country%");
            }
        }
        $open_jobs = $open_jobs_query
            ->companyJobs($companyDetails->id)
            ->openPosition()
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return [
            'user' => $user,
            'companyDetails' => $companyDetails,
            'open_jobs' => $open_jobs,
        ];
    }
}
