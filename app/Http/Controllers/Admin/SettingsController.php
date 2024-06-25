<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminGeneralSettingUpdateRequest;
use App\Http\Requests\AdminMailUpdateRequest;
use App\Http\Requests\AdminPayperjobSettingUpdateRequest;
use App\Http\Requests\AdminWPUpdateRequest;
use App\Http\Traits\UploadAble;
use App\Mail\SmtpTestEmail;
use App\Models\cms;
use App\Models\Cookies;
use App\Models\Job;
use App\Models\LanguageData;
use App\Models\Timezone;
use App\Models\User;
use App\Services\Admin\Settings\AdListingService;
use App\Services\Admin\Settings\SystemInfoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Modules\Currency\Entities\Currency;
use Modules\Currency\Http\Controllers\CurrencyController;
use Modules\Language\Entities\Language;
use Modules\Language\Http\Controllers\TranslationController;
use Modules\Location\Entities\Country;
use Modules\Seo\Entities\Seo;
use Modules\Seo\Entities\SeoPageContent;
use Modules\SetupGuide\Entities\SetupGuide;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SettingsController extends Controller
{
    use UploadAble;

    public $setting;

    public function __construct()
    {
        $this->middleware('access_limitation')->only(['generalUpdate', 'custumCSSJSUpdate', 'emailUpdate', 'systemUpdate', 'cookiesUpdate', 'seoUpdate', 'recaptchaUpdate', 'pusherUpdate', 'colorUpdate', 'layoutUpdate', 'pwaUpdate']);

        $this->setting = loadSetting(); // see helpers.php
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function general()
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            $data['timezones'] = Timezone::all();
            $data['currencies'] = Currency::all();
            $data['countries'] = Country::all();
            $data['setting'] = $this->setting;

            return view('backend.settings.pages.general', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function theme()
    {
        abort_if(! userCan('setting.view'), 403);

        return view('backend.settings.pages.theme');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function custom()
    {
        abort_if(! userCan('setting.view'), 403);

        return view('backend.settings.pages.custom');
    }

    /**
     * Website Data Update.
     *
     * @return void
     */
    public function generalUpdate(AdminGeneralSettingUpdateRequest $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            if ($request->name && $request->name != config('app.name')) {
                replaceAppName('APP_NAME', $request->name);
            }

            $setting = $this->setting;

            if ($request->hasFile('dark_logo')) {
                $setting['dark_logo'] = uploadFileToPublic($request->dark_logo, 'app/logo');
                deleteFile($setting->dark_logo);
            }

            if ($request->hasFile('light_logo')) {
                $setting['light_logo'] = uploadFileToPublic($request->light_logo, 'app/logo');
                deleteFile($setting->light_logo);
            }

            if ($request->hasFile('favicon_image')) {
                $setting['favicon_image'] = uploadFileToPublic($request->favicon_image, 'app/logo');
                deleteFile($setting->favicon_image);
            }

            $setting->email = $request->email;

            $setting->save();
            SetupGuide::where('task_name', 'app_setting')->update(['status' => 1]);

            return back()->with('success', 'Website setting updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function preferenceUpdate(Request $request)
    {
        try {
            // validation
            $request->validate([
                'footer_phone_no' => ['nullable'],
                'footer_facebook_link' => ['nullable', 'url'],
                'footer_instagram_link' => ['nullable', 'url'],
                'footer_twitter_link' => ['nullable', 'url'],
                'footer_youtube_link' => ['nullable', 'url'],
            ]);

            //Footer Update
            $cms = cms::first();
            $cms->footer_phone_no = $request->footer_phone_no;
            $cms->footer_facebook_link = $request->footer_facebook_link;
            $cms->footer_instagram_link = $request->footer_instagram_link;
            $cms->footer_twitter_link = $request->footer_twitter_link;
            $cms->footer_youtube_link = $request->footer_youtube_link;
            $cms->save();

            forgetCache('cms_setting');

            return back()->with('success', 'Website Footer Info updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update website layout
     *
     * @return void
     */
    public function layoutUpdate()
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $this->setting->update(request()->only('default_layout'));

            return back()->with('success', 'Website layout updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * color Data Update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function colorUpdate()
    {
        try {
            abort_if(! userCan('setting.update'), 403);
            session()->forget('primaryColor');
            session()->forget('secondaryColor');
            forgetCache('setting_data');
            $this->setting->update(request()->only(['sidebar_color', 'nav_color', 'sidebar_txt_color', 'nav_txt_color', 'main_color', 'accent_color', 'frontend_primary_color', 'frontend_secondary_color']));

            SetupGuide::where('task_name', 'theme_setting')->update(['status' => 1]);

            return back()->with('success', 'Color setting updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * custom js and css Data Update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function custumCSSJSUpdate()
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $this->setting->update(request()->only(['header_css', 'header_script', 'body_script']));

            return back()->with('success', 'Custom css/js updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Mode Update.
     *
     * @return bool
     */
    public function modeUpdate(Request $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $dark_mode = $request->only(['dark_mode']);
            $this->setting->update($dark_mode);

            return back()->with('success', 'Theme updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function email()
    {
        return view('backend.settings.pages.mail');
    }

    /**
     * Update mail configuration settings on .env file
     *
     * @return void
     */
    public function emailUpdate(AdminMailUpdateRequest $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $request->validate([
                'mail_host' => 'required',
                'mail_port' => 'required',
                'numeric',
                'mail_username' => 'required',
                'mail_password' => 'required',
                'mail_encryption' => 'required',
                'mail_from_name' => 'required',
                'mail_from_address' => 'required',
                'email',
            ]);

            envUpdate('MAIL_HOST', $request->mail_host);
            envUpdate('MAIL_PORT', $request->mail_port);
            envUpdate('MAIL_USERNAME', $request->mail_username);
            envUpdate('MAIL_PASSWORD', $request->mail_password);
            envUpdate('MAIL_ENCRYPTION', $request->mail_encryption);
            replaceAppName('MAIL_FROM_NAME', $request->mail_from_name);
            envUpdate('MAIL_FROM_ADDRESS', $request->mail_from_address);

            SetupGuide::where('task_name', 'smtp_setting')->update(['status' => 1]);

            return back()->with('success', 'Mail configuration update successfully');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Send a test email for check mail configuration credentials
     *
     * @return void
     */
    public function testEmailSent(Request $request)
    {
        $request->validate(['test_email' => ['required', 'email']]);
        try {
            Mail::to($request->test_email)->send(new SmtpTestEmail());

            return back()->with('success', 'Test email sent successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Mail send failed: '.$th->getMessage());
        }
    }

    /**
     * View Website mode page
     *
     * @return void
     */
    public function system()
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            $timezones = Timezone::all();
            $setting = $this->setting;
            $currencies = Currency::all();

            return view('backend.settings.pages.preference', compact('timezones', 'setting', 'currencies'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function systemUpdate(Request $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            // timezone update
            if ($request->has('timezone')) {
                $this->timezone($request);
            }

            // set default language
            if ($request->has('code')) {
                (new TranslationController())->setDefaultLanguage($request);
            }

            // app mode update
            if ($request->app_debug == 1) {
                Artisan::call('env:set APP_DEBUG=true');
            } else {
                Artisan::call('env:set APP_DEBUG=false');
            }

            // language changing update
            if ($request->has('language_changing')) {
                $this->allowLanguageChange($request);
            }

            // set default currency
            if ($request->has('currency')) {
                (new CurrencyController())->defaultCurrency($request);
            }

            // setting update
            $this->setting->update([
                'email_verification' => $request->email_verification ? true : false,
                'employer_auto_activation' => $request->employer_auto_activation ? true : false,
                'candidate_account_auto_activation' => $request->candidate_account_auto_activation ? true : false,
                'job_auto_approved' => $request->job_approval ? true : false,
                'edited_job_auto_approved' => $request->edited_job_approval ? true : false,
                'currency_switcher' => $request->currency_switcher,
            ]);

            return redirect()
                ->back()
                ->with('success', 'App configuration update successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function systemModeUpdate(Request $request)
    {
        try {
            if ($request->app_mode == 'live') {
                envUpdate('APP_MODE', $request->app_mode);
                $message = 'App is now live mode';
            } elseif ($request->app_mode == 'maintenance') {
                envUpdate('APP_MODE', $request->app_mode);
                $message = 'App is in maintenance mode';
            } else {
                envUpdate('APP_MODE', $request->app_mode);
                $message = 'App is in coming soon mode!';
            }

            flashSuccess($message);

            return redirect()->back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update google analytics setting
     *
     * @return void
     */
    public function googleAnalytics($request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            if ($request->google_analytics == 1) {
                $this->setting->update(['google_analytics' => true]);
            } else {
                $this->setting->update(['google_analytics' => false]);
            }

            $env = new Env();
            $env->setValue(strtoupper('GOOGLE_ANALYTICS_ID'), request('google_analytics_id', ''));

            session()->put('google_analytics', request('google_analytics', 0));

            return back()->with('success', 'Google Analytics update successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update facebook pixel setting
     *
     * @return void
     */
    public function facebookPixel($request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $env = new Env();
            $env->setValue(strtoupper('FACEBOOK_PIXEL_ID'), request('facebook_pixel_id', ''));

            if ($request->facebook_pixel == 1) {
                $this->setting->update([
                    'facebook_pixel' => true,
                ]);
            } else {
                $this->setting->update([
                    'facebook_pixel' => false,
                ]);
            }

            session()->put('facebook_pixel', request('facebook_pixel', 0));

            return back()->with('success', 'Facebook Pixel update successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Allow language changing
     *
     * @return void
     */
    public function allowLanguageChange($request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $this->setting->update([
                'language_changing' => request('language_changing', 0),
            ]);

            flashSuccess(__('language_changing_status_changed'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update timezone
     *
     * @return void
     */
    public function timezone($request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $request->validate(['timezone' => 'required']);

            $timezone = $request->timezone;

            if ($timezone && $timezone != config('app.timezone')) {
                replaceAppName('APP_TIMEZONE', $timezone);

                flashSuccess(__('timezone_updated_successfully'));
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Cookies Settings fetch
     *
     * @return void
     */
    public function cookies()
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            $cookie = Cookies::firstOrFail();

            return view('backend.settings.pages.cookies', compact('cookie'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Cookies Settings update
     *
     * @return void
     */
    public function cookiesUpdate(Request $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            // validating request data
            $request->validate([
                'cookie_name' => 'required|max:50|string',
                'cookie_expiration' => 'required|numeric|max:365',
            ]);

            // updating data to database
            $cookies = Cookies::first();
            $cookies->allow_cookies = request('allow_cookies', 0);
            $cookies->cookie_name = $request->cookie_name;
            $cookies->cookie_expiration = $request->cookie_expiration;
            $cookies->force_consent = request('force_consent', 0);
            $cookies->darkmode = request('darkmode', 0);
            $cookies->save();

            flashSuccess(__('cookies_settings_successfully_updated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Seo Settings fetch
     *
     * @return void
     */
    public function seoIndex(Request $request)
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            $query = $request->lang_query;
            $seos = Seo::with([
                'contents' => function ($q) use ($query) {
                    if ($query) {
                        return $q->where('language_code', $query);
                    } else {
                        return $q->where('language_code', 'en');
                    }
                },
            ])->paginate(20);

            $languages = Language::get(['id', 'code', 'name']);

            return view('backend.settings.pages.seo.index', compact('seos', 'languages'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Seo Settings fetch
     *
     * @return void
     */
    public function seoEdit($page)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $seo = Seo::FindOrFail($page);
            $en_content = $seo
                ->contents()
                ->where('language_code', 'en')
                ->first();

            if (request('lang_query')) {
                $exist_content = $seo
                    ->contents()
                    ->where('language_code', request('lang_query'))
                    ->first();

                if (! $exist_content) {
                    $new_content = $seo->contents()->create([
                        'language_code' => request('lang_query'),
                        'title' => $en_content->title,
                        'description' => $en_content->description,
                        'image' => $en_content->image,
                    ]);
                }
            }

            if (request('lang_query')) {
                $content = $seo
                    ->contents()
                    ->where('language_code', request('lang_query'))
                    ->first();
            } else {
                $content = $seo->contents()->first();
            }

            $seo->load('contents');
            $languages = Language::get(['id', 'code', 'name']);

            return view('backend.settings.pages.seo.edit', compact('seo', 'languages', 'content'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Seo content create
     *
     * @return void
     */
    public function seoContentCreate(Request $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $seo = Seo::FindOrFail($request->page_id);
            $exist_content = $seo
                ->contents()
                ->where('language_code', $request->language_code)
                ->first();
            $en_content = $seo
                ->contents()
                ->where('language_code', 'en')
                ->first();

            $content = '';
            if ($exist_content) {
                $content = $exist_content;
            } else {
                $new_content = $seo->contents()->create([
                    'language_code' => $request->language_code,
                    'title' => $en_content->title,
                    'description' => $en_content->description,
                    'image' => $en_content->image,
                ]);
                $content = $new_content;
            }

            return redirect()->route('settings.seo.edit', [$seo->id, 'lang_query' => $content->language_code]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Seo content delete
     *
     * @return void
     */
    // public function seoContentDelete(Request $request)
    // {
    //     try {
    //         abort_if(! userCan('setting.update'), 403);

    //         $content = SeoPageContent::FindOrFail($request->content_id);
    //         $content->delete();

    //         flashSuccess(__('success'), 'page_translation_content_delete_successfully');

    //         return redirect()->route('settings.seo.edit', [$request->page_id, 'lang_query' => 'en']);
    //     } catch (\Exception $e) {
    //         flashError('An error occurred: '.$e->getMessage());

    //         return back();
    //     }
    // }

    /**
     * Seo content update
     *
     * @return void
     */
    public function seoUpdate(Request $request, SeoPageContent $content)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $request->validate(['title' => 'required', 'description' => 'required']);

            $content->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            if ($request->image != null && $request->hasFile('image')) {
                deleteFile($content->image);

                $path = 'images/seo';
                $image = uploadImage($request->image, $path);

                $content->update(['image' => $image]);
            }

            flashSuccess(__('page_meta_content_updated_successfully'));

            return redirect()->back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Working Process Settings update
     *
     * @param  SeoPageContent  $content
     * @return void
     */
    public function workingProcessUpdate(AdminWPUpdateRequest $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);
            session(['tab_part' => 'working_process']);

            $this->setting->update([
                'working_process_step1_title' => $request->working_process_step1_title,
                'working_process_step1_description' => $request->working_process_step1_description,
                'working_process_step2_title' => $request->working_process_step2_title,
                'working_process_step2_description' => $request->working_process_step2_description,
                'working_process_step3_title' => $request->working_process_step3_title,
                'working_process_step3_description' => $request->working_process_step3_description,
                'working_process_step4_title' => $request->working_process_step4_title,
                'working_process_step4_description' => $request->working_process_step4_description,
            ]);

            flashSuccess(__('work_process_content_updated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Generate sitemap
     *
     * @param  AdminWPUpdateRequest  $request
     * @param  SeoPageContent  $content
     * @return void
     */
    public function generateSitemap()
    {
        try {
            $jobs = Job::where('status', 'active')->get(['id', 'slug']);
            $employers = User::where('role', 'company')->get(['id', 'username']);

            $sitemap = Sitemap::create()
                ->add(Url::create('/home'))
                ->add(Url::create('/jobs'))
                ->add(Url::create('/candidates'))
                ->add(Url::create('/employers'))
                ->add(Url::create('/about'))
                ->add(Url::create('/contact'))
                ->add(Url::create('/login'))
                ->add(Url::create('/register'))
                ->add(Url::create('/faq'))
                ->add(Url::create('/plans'))
                ->add(Url::create('/posts'))
                ->add(Url::create('/posts'));

            foreach ($jobs as $job) {
                $jobUrl = '/job/'.$job->slug;
                $sitemap->add(Url::create($jobUrl));
            }

            foreach ($employers as $employer) {
                $employerUrl = '/employer/'.$employer->username;
                $sitemap->add(Url::create($employerUrl));
            }

            $sitemap->writeToFile(public_path('sitemap.xml'));
            flashSuccess('Sitemap regenerated');

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

        foreach ($employers as $employer) {
            $employerUrl = '/employer/'.$employer->username;
            $sitemap->add(Url::create($employerUrl));
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));
        flashSuccess('Sitemap regenerated');

        return back();
    }

    /**
     * Recaptcha Settings update
     *
     * @return void
     */
    public function recaptchaUpdate(Request $request)
    {
        $request->validate(['nocaptcha_key' => 'required', 'nocaptcha_secret' => 'required']);

        try {
            checkSetConfig('captcha.sitekey', $request->nocaptcha_key);
            checkSetConfig('captcha.secret', $request->nocaptcha_secret);
            checkSetConfig('captcha.active', $request->status ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('recaptcha_configuration_updated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Pusher Settings update
     *
     * @return void
     */
    public function pusherUpdate(Request $request)
    {
        $request->validate([
            'pusher_app_id' => 'required',
            'pusher_app_key' => 'required',
            'pusher_app_secret' => 'required',
            'pusher_port' => 'required',
            'pusher_schema' => 'required',
            'pusher_app_cluster' => 'required',
        ]);

        try {
            envReplace('PUSHER_APP_ID', $request->pusher_app_id);
            envReplace('PUSHER_APP_KEY', $request->pusher_app_key);
            envReplace('PUSHER_APP_SECRET', $request->pusher_app_secret);
            envReplace('PUSHER_HOST', $request->pusher_host);
            envReplace('PUSHER_PORT', $request->pusher_port);
            envReplace('PUSHER_SCHEME', $request->pusher_schema);
            envReplace('PUSHER_APP_CLUSTER', $request->pusher_app_cluster);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('pusher_configuration_updated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Google Analytics Settings update
     *
     * @return void
     */
    public function analyticsUpdate(Request $request)
    {
        try {
            $request->validate([
                'is_analytics_active' => 'nullable|boolean',
                'analytics_id' => 'required_if:is_analytics_active,1', // G-GTRVREE0F4
            ]);

            checkSetConfig('templatecookie.google_analytics', $request->analytics_id);
            checkSetConfig('templatecookie.google_analytics_status', $request->is_analytics_active ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess('Google Analytics Configuration updated!');

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Payperjob update
     *
     *
     * @return void
     */
    public function payperjobUpdate(AdminPayperjobSettingUpdateRequest $request)
    {
        try {
            // payper setting data
            $this->setting->update([
                'per_job_active' => $request->status ?? 0,
                'per_job_price' => $request->per_job_price,
                'highlight_job_price' => $request->highlight_job_price,
                'featured_job_price' => $request->featured_job_price,
                'highlight_job_days' => $request->highlight_job_days,
                'featured_job_days' => $request->featured_job_days,
            ]);

            // forget session data
            cache()->forget('highlight_job_days');
            cache()->forget('featured_job_days');

            flashSuccess(__('payperjob_setting_updated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Application job deadline limit update
     *
     * @return Response
     */
    public function systemJobdeadlineUpdate(Request $request)
    {
        try {
            $this->setting->update([
                'job_deadline_expiration_limit' => $request->job_deadline_expiration_limit,
            ]);

            flashSuccess(__('job_deadline_expiration_limit_updated_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Upgrade application
     *
     * @return Response
     */
    public function upgrade()
    {
        return view('backend.settings.pages.upgrade-guide');
    }

    /**
     * Upgrade applying
     *
     * @return Response
     */
    public function upgradeApply()
    {
        try {
            
            Artisan::call('migrate');
            $this->syncLanguageJson();
            // menu list cache clear
            Cache::forget('menu_lists');

            $this->moveEnvValueToConfig();
            $this->moveSocialEnvValueToConfig();
            $this->moveSslcEnvValueToConfig();
            $this->moveRecaptchaEnvValueToConfig();

            flashSuccess(__('application_upgrade_successfully'));

            return back();
        } catch (\Exception $e) {
            // flashError('An error occurred: '.$e->getMessage());
            flashSuccess(__('application_upgrade_successfully'));

            return back();
        }
    }

    private function syncLanguageJson()
    {
        try {
            $langData = LanguageData::get();

            foreach ($langData as $value) {
                $currentJsonPath = base_path('resources/lang/'.$value->code.'.json');
                $currentJson = json_decode(File::get($currentJsonPath), true);
                $databaseJson = json_decode($value->data, true);

                // Merge the current JSON with the database JSON, keeping existing keys
                $mergedJson = array_merge($currentJson, $databaseJson);

                // Save the merged JSON back to the database
                $value->update(['data' => json_encode($mergedJson)]);

                // Save the merged JSON back to the JSON file
                File::put($currentJsonPath, json_encode($mergedJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * PWA Data Update.
     *
     * @return void
     */
    public function pwaUpdate(Request $request)
    {
        abort_if(! userCan('setting.update'), 403);

        $request->validate([
            'app_pwa_icon' => ['nullable', 'mimes:png,jpg,jpeg'],
        ]);

        $setting = $this->setting;

        if ($request->hasFile('app_pwa_icon')) {
            $setting['app_pwa_icon'] = uploadFileToPublic($request->app_pwa_icon, 'app/logo');
            deleteFile($setting->app_pwa_icon);
        }

        // for pwa_enable
        $setting['pwa_enable'] = $request->pwa_enable;

        if ($request->pwa_enable) {
            updateManifest($setting);
        }

        $setting->save();
        SetupGuide::where('task_name', 'app_setting')->update(['status' => 1]);

        return back()->with('success', 'Website setting updated successfully!');
    }

    public function moveEnvValueToConfig()
    {
        $allEmptyProperties = array_filter(Config::get('templatecookie'), function ($value) {
            return $value === '';
        });

        foreach ($allEmptyProperties as $property => $value) {
            $envProperty = strtoupper($property);

            $envValue = env($envProperty);
            if ($envValue !== null && $envValue !== '') {
                // Update the value in Config::get('templatecookie') with the value from .env
                checkSetConfig('templatecookie.'.$property, $envValue);
            }
        }
    }

    public function moveSocialEnvValueToConfig()
    {
        // Update social login config
        $google_client_id = env('GOOGLE_CLIENT_ID');
        $google_client_secret = env('GOOGLE_CLIENT_SECRET');
        $google_login_active = env('GOOGLE_LOGIN_ACTIVE');

        $facebook_client_id = env('FACEBOOK_CLIENT_ID');
        $facebook_client_secret = env('FACEBOOK_CLIENT_SECRET');
        $facebook_login_active = env('FACEBOOK_LOGIN_ACTIVE');

        $twitter_client_id = env('TWITTER_CLIENT_ID');
        $twitter_client_secret = env('TWITTER_CLIENT_SECRET');
        $twitter_login_active = env('TWITTER_LOGIN_ACTIVE');

        $linkedin_client_id = env('LINKEDIN_CLIENT_ID');
        $linkedin_client_secret = env('LINKEDIN_CLIENT_SECRET');
        $linkedin_login_active = env('LINKEDIN_LOGIN_ACTIVE');

        $github_client_id = env('GITHUB_CLIENT_ID');
        $github_client_secret = env('GITHUB_CLIENT_SECRET');
        $github_login_active = env('GITHUB_LOGIN_ACTIVE');

        checkSetConfig('services.google.client_id', $google_client_id);
        checkSetConfig('services.google.client_secret', $google_client_secret);
        checkSetConfig('services.google.active', $google_login_active ? true : false);

        checkSetConfig('services.facebook.client_id', $facebook_client_id);
        checkSetConfig('services.facebook.client_secret', $facebook_client_secret);
        checkSetConfig('services.facebook.active', $facebook_login_active ? true : false);

        checkSetConfig('services.twitter.client_id', $twitter_client_id);
        checkSetConfig('services.twitter.client_secret', $twitter_client_secret);
        checkSetConfig('services.twitter.active', $twitter_login_active ? true : false);

        checkSetConfig('services.linkedin-openid.client_id', $linkedin_client_id);
        checkSetConfig('services.linkedin-openid.client_secret', $linkedin_client_secret);
        checkSetConfig('services.linkedin-openid.active', $linkedin_login_active ? true : false);

        checkSetConfig('services.github.client_id', $github_client_id);
        checkSetConfig('services.github.client_secret', $github_client_secret);
        checkSetConfig('services.github.active', $github_login_active ? true : false);
    }

    public function moveSslcEnvValueToConfig()
    {
        $ssl_id = env('STORE_ID');
        $ssl_password = env('STORE_PASSWORD');
        $ssl_active = env('SSLCOMMERZ_ACTIVE');
        $ssl_mode = env('SSLCOMMERZ_MODE');

        config(['sslcommerz.store.id' => $ssl_id]);
        config(['sslcommerz.store.password' => $ssl_password]);
        config(['sslcommerz.active' => $ssl_active ? true : false]);
        config(['sslcommerz.sandbox' => $ssl_mode == 'sandbox' ? true : false]);
    }

    public function moveRecaptchaEnvValueToConfig()
    {
        $recaptcha_sitekey = env('NOCAPTCHA_SITEKEY');
        $recaptcha_secret = env('NOCAPTCHA_SECRET');
        $recaptcha_active = env('NOCAPTCHA_ACTIVE');

        checkSetConfig('captcha.sitekey', $recaptcha_sitekey);
        checkSetConfig('captcha.secret', $recaptcha_secret);
        checkSetConfig('captcha.active', $recaptcha_active ? true : false);
    }

    public function systemInfo()
    {
        $data = (new SystemInfoService)->execute();

        return view('backend.settings.pages.system-info', ['data' => $data]);
    }

    public function landingPageUpdate(Request $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $setting = $this->setting;

            $setting->landing_page = $request->landing_page;

            $setting->save();

            return back()->with('success', 'Landing page updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }

    public function ad_setting()
    {
        try {
            $ads = (new AdListingService())->index();

            return view('backend.settings.pages.advertisement', compact('ads'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function update_ad_info(Request $request)
    {

        try {
            (new AdListingService())->update($request);
            flashSuccess('Advertisement code updated !');

            return redirect()->back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function update_ad_status(Request $request)
    {
        try {
            (new AdListingService())->update_ad_status($request);
            forgetCache('advertisements');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }
}
