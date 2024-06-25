<?php

namespace App\Http\Traits;

use App\Models\Setting;
use Modules\Location\Entities\Country;

trait HasCountryBasedJobsApi
{
    public function filterCountryBasedJobs($jobs){
        $selected_country = request()->header('selected_country');

        if ($selected_country && $selected_country != null && $selected_country != 'all') {
            $jobs->where('country', 'LIKE', "%$selected_country%");
        } else {

            $setting = Setting::first();
            if ($setting->app_country_type == 'single_base') {
                if ($setting->app_country) {

                    $country = Country::where('id', $setting->app_country)->first();
                    if ($country) {
                        $jobs->where('country', 'LIKE', "%$country->name%");
                    }
                }
            }
        }

        return $jobs;
    }
}
