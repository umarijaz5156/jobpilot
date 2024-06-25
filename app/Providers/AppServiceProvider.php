<?php

namespace App\Providers;

use App\Http\Traits\GetMenuTrait;
use App\Models\Cms;
use App\Models\Cookies;
use App\Models\Page;
use App\Models\WebsiteSetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Modules\Currency\Entities\Currency;
use Modules\Language\Entities\Language;
use Modules\Location\Entities\Country;
use Modules\SetupGuide\Entities\SetupGuide;

class AppServiceProvider extends ServiceProvider
{
    use GetMenuTrait;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // if($this->app->environment('production')) {
        // \URL::forceScheme('https');
        // }
        Paginator::useBootstrap();

        if (! app()->runningInConsole()) {

            //Caching global Variables
            $expirationTime = Carbon::now()->addDays(30);

            $websiteSetting = Cache::remember('website_setting', $expirationTime, function () {
                return WebsiteSetting::first();
            });

            $cmsData = Cache::remember('cms_setting', $expirationTime, function () {
                return Cms::first();
            });

            $appSetup = Cache::remember('app_setup', $expirationTime, function () {
                return SetupGuide::orderBy('status', 'asc')->get();
            });

            $languages = Cache::remember('languages', $expirationTime, function () {
                return loadLanguage();
            });

            $headerCountries = Cache::remember('header_countries', $expirationTime, function () {
                return Country::select('id', 'name', 'slug', 'icon')->active()->get();
            });

            $headerCurrencies = Cache::remember('header_currencies', $expirationTime, function () {
                return Currency::all();
            });

            $pages = Page::all();

            $cookies = Cache::remember('cookies_data', $expirationTime, function () {
                return Cookies::first();
            });

            $default_language = Cache::remember('default_language_data', $expirationTime, function () {
                return Language::where('code', config('templatecookie.default_language'))->first();
            });

            $setting = Cache::remember('setting_data', $expirationTime, function () {
                return loadSetting();
            });

            view()->share('defaultLanguage', $default_language);
            view()->share('cookies', $cookies);
            view()->share('website_setting', $websiteSetting);
            view()->share('cms_setting', $cmsData);

            view()->share('appSetup', $appSetup);

            view()->share('setting', $setting);
            // view()->share('currency_symbol', config('jobpilot.currency_symbol'));
            view()->share('currency_symbol', config('templatecookie.currency_symbol'));

            view()->share('languages', $languages);
            view()->share('headerCountries', $headerCountries);
            view()->share('headerCurrencies', $headerCurrencies);
            view()->share('custom_pages', $pages);

            // menu data
            view()->composer('frontend.partials.header', function ($view) {
                $view->with('public_menu_lists', $this->publicMenu());
                $view->with('company_menu_lists', $this->companyMenu());
                $view->with('candidate_menu_lists', $this->candidateMenu());
            });

            if ($setting) {
                if ($setting->commingsoon_mode) {
                    session()->put('commingsoon_mode', $setting->commingsoon_mode);
                }
            }
        }

        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });

            return $this;
        });
    }
}
