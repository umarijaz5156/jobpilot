<?php

namespace App\Http\Traits;

use Modules\Location\Entities\Country;

trait HasCountryBasedJobs
{
    public function filterCountryBasedJobs($jobs)
    {
        $selected_country = session()->get('selected_country');

        if ($selected_country && $selected_country != null && $selected_country != 'all') {
            $country = selected_country()->name;
            $jobs->where('country', 'LIKE', "%$country%");
        } else {

            $setting = loadSetting();
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
