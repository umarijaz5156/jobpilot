<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AffiliateSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'careerjetUpdate',
            'indeedUpdate',
            'setDefaultJob',
        ]);
    }

    /*
    * Show the affiliate settings page
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function index()
    {
        return view('backend.settings.pages.affiliate');
    }

    /*
    * Update the careerjet settings
    *
    * @param Request $request
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function careerjetUpdate(Request $request)
    {
        $request->validate([
            'default_locale' => 'required',
            'job_limit' => 'required',
        ]);

        checkSetConfig('templatecookie.careerjet_id', $request->careerjet_affiliate_id ?? '');
        checkSetConfig('templatecookie.careerjet_default_locale', $request->default_locale);
        checkSetConfig('templatecookie.careerjet_limit', $request->job_limit);

        sleep(3);
        Artisan::call('cache:clear');

        flashSuccess(__('careerjet_api_configuration_updated'));

        return back();
    }

    /*
    * Update the indeed affiliate settings
    *
    * @param Request $request
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function indeedUpdate(Request $request)
    {
        $request->validate([
            'job_limit' => 'required',
        ]);

        checkSetConfig('templatecookie.indeed_id', $request->indeed_affiliate_id ?? '');
        checkSetConfig('templatecookie.indeed_limit', $request->job_limit);

        sleep(3);
        Artisan::call('cache:clear');

        flashSuccess(__('indeed_api_configuration_updated'));

        return back();
    }

    /*
    * Set the default job provider
    *
    * @param Request $request
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function setDefaultJob(Request $request)
    {
        $provider = $request->job_provider;

        if ($provider) {
            checkSetConfig('templatecookie.default_job_provider', $provider);
        } else {
            checkSetConfig('templatecookie.default_job_provider', '');
        }

        sleep(3);
        Artisan::call('cache:clear');

        flashSuccess(__('default_affiliate_job_provider_updated'));

        return back();
    }
}
