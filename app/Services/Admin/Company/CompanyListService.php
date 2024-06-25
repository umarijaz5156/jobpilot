<?php

namespace App\Services\Admin\Company;

use App\Models\Company;
use App\Models\Job;

class CompanyListService
{
    /**
     * Get company list
     */
    public function execute($request): mixed
    {
        $query = Company::query();

        // sortby status
        if ($request->sort_by == 'latest' || $request->sort_by == null) {
            $query->latest();
        } else {
            $query->oldest();
        }

        // verified status
        if ($request->has('ev_status') && $request->ev_status != null) {
            $ev_status = null;
            if ($request->ev_status == 'true') {
                $query->whereHas('user', function ($q) {
                    $q->whereNotNull('email_verified_at');
                });
            } else {
                $query->whereHas('user', function ($q) {
                    $q->whereNull('email_verified_at');
                });
            }
        }

        if ($request->keyword && $request->keyword != null) {
            $query->whereLike(['user.name', 'user.email'], $request->keyword);
        }

        // organization type filter
        if ($request->organization_type && $request->organization_type != null) {
            $query->where('organization_type_id', $request->organization_type);
        }

        // industry type filter
        if ($request->industry_type && $request->industry_type != null) {
            $query->where('industry_type_id', $request->industry_type);
        }

        $companies = $query->with('organization.translations', 'user')->paginate(10)->through(function ($company) {
            $company->active_jobs = Job::where('company_id', $company->id)->openPosition()->count();

            return $company;
        });

        return $companies;
    }
}
