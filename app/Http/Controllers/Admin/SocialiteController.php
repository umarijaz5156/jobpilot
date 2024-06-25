<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SocialiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only(['update', 'updateStatus']);

        $this->middleware(['permission:setting.view|setting.update'])->only(['index']);

        $this->middleware(['permission:setting.update'])->only(['update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.settings.pages.socialite');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        switch ($request->type) {
            case 'google':
                $this->updateGoogleCredential($request);
                break;
            case 'facebook':
                $this->updateFacebookCredential($request);
                break;
            case 'twitter':
                $this->updateTwitterCredential($request);
                break;
            case 'linkedin':
                $this->updateLinkedinCredential($request);
                break;
            case 'github':
                $this->updateGithubCredential($request);
                break;
        }
    }

    /**
     * Update login with google credential
     *
     * @return void
     */
    public function updateGoogleCredential(Request $request)
    {
        $request->validate([
            'google_client_id' => ['required'],
            'google_client_secret' => ['required'],
        ]);

        try {

            checkSetConfig('services.google.client_id', $request->google_client_id);
            checkSetConfig('services.google.client_secret', $request->google_client_secret);
            checkSetConfig('services.google.active', $request->google ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            session()->flash('success', ucfirst($request->type).__('setting_updated_successfully'));

            return redirect()->route('settings.social.login')->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update login with facebook credential
     *
     * @return void
     */
    public function updateFacebookCredential(Request $request)
    {
        $request->validate([
            'facebook_client_id' => ['required'],
            'facebook_client_secret' => ['required'],
        ]);

        try {

            checkSetConfig('services.facebook.client_id', $request->facebook_client_id);
            checkSetConfig('services.facebook.client_secret', $request->facebook_client_secret);
            checkSetConfig('services.facebook.active', $request->facebook ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            session()->flash('success', ucfirst($request->type).__('setting_updated_successfully'));

            return redirect()->route('settings.social.login')->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update login with twitter credential
     *
     * @return void
     */
    public function updateTwitterCredential(Request $request)
    {
        $request->validate([
            'twitter_client_id' => ['required'],
            'twitter_client_secret' => ['required'],
        ]);

        try {

            checkSetConfig('services.twitter.client_id', $request->twitter_client_id);
            checkSetConfig('services.twitter.client_secret', $request->twitter_client_secret);
            checkSetConfig('services.twitter.active', $request->twitter ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            session()->flash('success', ucfirst($request->type).__('setting_updated_successfully'));

            return redirect()->route('settings.social.login')->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update login with linkedin credential
     *
     * @return void
     */
    public function updateLinkedinCredential(Request $request)
    {

        try {
            $request->validate([
                'linkedin_client_id' => ['required'],
                'linkedin_client_secret' => ['required'],
            ]);

            checkSetConfig('services.linkedin-openid.client_id', $request->linkedin_client_id);
            checkSetConfig('services.linkedin-openid.client_secret', $request->linkedin_client_secret);
            checkSetConfig('services.linkedin-openid.active', $request->linkedin ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            session()->flash('success', ucfirst($request->type).__('setting_updated_successfully'));

            return redirect()->route('settings.social.login')->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update login with github credential
     *
     * @return void
     */
    public function updateGithubCredential(Request $request)
    {

        $request->validate([
            'github_client_id' => ['required'],
            'github_client_secret' => ['required'],
        ]);

        try {

            checkSetConfig('services.github.client_id', $request->github_client_id);
            checkSetConfig('services.github.client_secret', $request->github_client_secret);
            checkSetConfig('services.github.active', $request->github ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            session()->flash('success', ucfirst($request->type).__('setting_updated_successfully'));

            return redirect()->route('settings.social.login')->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
