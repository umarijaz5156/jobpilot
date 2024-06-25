<?php

use App\Models\Advertisement;
use App\Models\Candidate;
use App\Models\Cms;
use App\Models\Company;
use App\Models\Cookies;
use App\Models\Job;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Intervention\Image\Facades\Image;
use Laravolt\Avatar\Facade as Avatar;
use Modules\Currency\Entities\Currency;
use Modules\Language\Entities\Language;
use Modules\Location\Entities\Country;
use Modules\Seo\Entities\Seo;
use Stevebauman\Location\Facades\Location;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Torann\GeoIP\Facades\GeoIP;

if (! function_exists('uploadImage')) {
    function uploadImage($file, $destinationPath, $fit = null, $quality = 60)
    {
        $image = Image::make($file);

        $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $fullPath = public_path($destinationPath.'/'.$fileName);

        if (! File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        if ($fit) {
            $image->fit($fit[0], $fit[1]);
        }

        $image->save($fullPath, $quality);

        // Return the relative path from the public directory
        return $destinationPath.'/'.$fileName;
    }
}

/**
 * image delete
 *
 * @param  string  $image
 * @return void
 */
if (! function_exists('deleteFile')) {
    function deleteFile(?string $image)
    {
        $imageExists = file_exists($image);

        if ($imageExists) {
            if ($imageExists != 'backend/image/default.png') {
                @unlink($image);
            }
        }
    }
}

/**
 * image delete
 *
 * @param  string  $image
 * @return void
 */
if (! function_exists('deleteImage')) {
    function deleteImage(?string $image)
    {
        $imageExists = file_exists($image);

        if ($imageExists) {
            if ($imageExists != 'backend/image/default.png') {
                @unlink($image);
            }
        }
    }
}

/**
 * @param  UploadedFile  $file
 * @param  null  $folder
 * @param  string  $disk
 * @param  null  $filename
 * @return false|string
 */
if (! function_exists('uploadOne')) {
    function uploadOne(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $name = ! is_null($filename) ? $filename : uniqid('FILE_').dechex(time());

        return $file->storeAs(
            $folder,
            $name.'.'.$file->getClientOriginalExtension(),
            $disk
        );
    }
}

/**
 * @param  null  $path
 * @param  string  $disk
 */
if (! function_exists('deleteOne')) {
    function deleteOne($path = null, $disk = 'public')
    {
        Storage::disk($disk)->delete($path);
    }
}

if (! function_exists('uploadFileToStorage')) {
    function uploadFileToStorage($file, string $path)
    {
        $file_name = $file->hashName();
        Storage::putFileAs($path, $file, $file_name);

        return $path.'/'.$file_name;
    }
}

if (! function_exists('uploadFileToPublic')) {
    function uploadFileToPublic($file, string $path)
    {
        if ($file && $path) {
            $url = $file->move('uploads/'.$path, $file->hashName());
        } else {
            $url = null;
        }

        return $url;
    }
}

// =====================================================
// ===================Env Function====================
// =====================================================
if (! function_exists('envReplace')) {
    function envReplace($name, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $name.'='.env($name),
                $name.'='.$value,
                file_get_contents($path)
            ));
        }

        if (file_exists(App::getCachedConfigPath())) {
            Artisan::call('config:cache');
        }
    }
}
if (! function_exists('replaceAppName')) {
    function replaceAppName($name, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            // Wrap the value in double quotes and replace the line
            $escapedValue = '"'.str_replace('"', '\"', $value).'"';
            file_put_contents($path, preg_replace(
                "/^$name=.*/m",
                "$name=$escapedValue",
                file_get_contents($path)
            ));
        }

        if (file_exists(App::getCachedConfigPath())) {
            Artisan::call('config:clear');
        }
    }
}

if (! function_exists('envUpdate')) {
    function envUpdate($key, $value)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        $newLine = "$key=$value";

        if (strpos($envContent, "$key=") !== false) {
            $envContent = preg_replace("/$key=.*/", $newLine, $envContent);
        } else {
            $envContent .= "\n".$newLine;
        }

        file_put_contents($envFile, $envContent);

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
}

if (! function_exists('error')) {
    function error($name, $class = 'is-invalid')
    {
        $errors = session()->get('errors', app(ViewErrorBag::class));

        return $errors->has($name) ? $class : '';
    }
}

if (! function_exists('checkSetConfig')) {
    function checkSetConfig($key, $value)
    {

        if ((config($key) != $value)) {

            setConfig($key, $value);
        }
    }
}

if (! function_exists('setConfig')) {
    function setConfig($key, $value)
    {
        Config::write($key, $value);
        sleep(2);
        if (file_exists(App::getCachedConfigPath())) {
            Artisan::call('config:cache');
        }

        return 'Configuration set successfully!';
    }
}

if (! function_exists('allowLaguageChanage')) {
    function allowLaguageChanage()
    {
        return Setting::first()->language_changing ? true : false;
    }
}

// ========================================================
// ===================Response Function====================
// ========================================================

/**
 * Response success data collection
 *
 * @param  object  $data
 * @param  string  $responseName
 * @return \Illuminate\Http\Response
 */
if (! function_exists('responseData')) {
    function responseData(?object $data, string $responseName = 'data')
    {
        return response()->json([
            'success' => true,
            $responseName => $data,
        ], 200);
    }
}

/**
 * Response success data collection
 *
 * @param  string  $msg
 * @return \Illuminate\Http\Response
 */
if (! function_exists('responseSuccess')) {
    function responseSuccess(string $msg = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $msg,
        ], 200);
    }
}

/**
 * Response error data collection
 *
 * @param  string  $msg
 * @param  int  $code
 * @return \Illuminate\Http\Response
 */
if (! function_exists('responseError')) {
    function responseError(string $msg = 'Something went wrong, please try again', int $code = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $msg,
        ], $code);
    }
}

/**
 * Response success flash message.
 *
 * @param  string  $msg
 * @return \Illuminate\Http\Response
 */
if (! function_exists('flashSuccess')) {
    function flashSuccess(string $msg)
    {
        session()->flash('success', $msg);
    }
}

/**
 * Response error flash message.
 *
 * @param  string  $msg
 * @return \Illuminate\Http\Response
 */
if (! function_exists('flashError')) {
    function flashError(?string $message = null)
    {
        if (! $message) {
            $message = __('something_went_wrong');
        }

        return session()->flash('error', $message);
    }
}

/**
 * Response warning flash message.
 *
 * @param  string  $msg
 * @return \Illuminate\Http\Response
 */
if (! function_exists('flashWarning')) {
    function flashWarning(?string $message = null, bool $custom = false)
    {
        if (! $message) {
            $message = __('something_went_wrong');
        }

        if ($custom) {
            return session()->flash('warning', $message);
        } else {
            return session()->flash('warning', $message);
        }
    }
}

// ========================================================
// ===================Others Function====================
// ========================================================
if (! function_exists('setting')) {
    function setting($fields = null, $append = false)
    {
        if ($fields) {
            $type = gettype($fields);

            if ($type == 'string') {
                $data = $append ? Setting::first($fields) : Setting::value($fields);
            } elseif ($type == 'array') {
                $data = Setting::first($fields);
            }
        } else {
            $data = loadSetting();
        }

        if ($append) {
            $data = $data->makeHidden(['dark_logo_url', 'light_logo_url', 'favicon_image_url', 'app_pwa_icon_url']);
        }
        forgetCache('setting_data');

        return $data;
    }
}

// For pwa_enable start
if (! function_exists('updateManifest')) {
    function updateManifest($setting)
    {
        $manifest = [

            'name' => config('app.name'),
            'short_name' => config('app.name'),
            'start_url' => route('website.home'),
            'background_color' => $setting->frontend_primary_color,
            'description' => config('app.name'),
            'display' => 'fullscreen',
            'theme_color' => $setting->frontend_primary_color,
            'icons' => [
                [
                    'src' => $setting->app_pwa_icon_url,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
        ];

        file_put_contents(public_path('manifest.json'), json_encode($manifest));
    }
}
// For pwa_enable end

if (! function_exists('autoTransLation')) {
    function autoTransLation($lang, $text)
    {
        $tr = new GoogleTranslate($lang);
        $afterTrans = $tr->translate($text);

        return $afterTrans;
    }
}

/**
 * user permission check
 *
 * @param  string  $permission
 * @return bool
 */
if (! function_exists('userCan')) {
    function userCan($permission)
    {
        return auth('admin')->user()->can($permission);
    }
}

if (! function_exists('pdfUpload')) {
    function pdfUpload(?object $file, string $path): string
    {
        $filename = time().'.'.$file->extension();
        $filePath = public_path('uploads/'.$path);
        $file->move($filePath, $filename);

        return $filePath.$filename;
    }
}

if (! function_exists('remainingDays')) {

    function remainingDays($deadline)
    {
        $now = Carbon::now();
        $cDate = Carbon::parse($deadline);

        return $now->diffInDays($cDate);
    }
}

if (! function_exists('jobStatus')) {
    function jobStatus($deadline)
    {
        $now = Carbon::now();
        $cDate = Carbon::parse($deadline);

        if ($now->greaterThanOrEqualTo($cDate)) {
            return 'Expire';
        } else {
            return 'Active';
        }
    }
}

if (! function_exists('socialMediaShareLinks')) {

    function socialMediaShareLinks(string $path, string $provider)
    {
        switch ($provider) {
            case 'facebook':
                $share_link = 'https://www.facebook.com/sharer/sharer.php?u='.$path;
                break;
            case 'twitter':
                $share_link = 'https://twitter.com/intent/tweet?text='.$path;
                break;
            case 'pinterest':
                $share_link = 'http://pinterest.com/pin/create/button/?url='.$path;
                break;
            case 'linkedin':
                $share_link = 'https://www.linkedin.com/shareArticle?mini=true&url='.$path;
                break;
            case 'telegram':
                $share_link = 'https://t.me/share/url?url='.$path;
                break;
            case 'whatsapp':
                $share_link = 'https://api.whatsapp.com/send?text='.$path;
                break;
            case 'linkedin':
                $share_link = 'https://www.linkedin.com/sharing/share-offsite/?url='.$path;
                break;
            case 'mail':
                $share_link = 'mailto:?subject=Share this link&body='.$path;
                break;
            case 'skype':
                $share_link = 'https://web.skype.com/share?url='.$path;
                break;
        }

        return $share_link;
    }
}

if (! function_exists('livejob')) {

    function livejob()
    {
        $jobs = Job::withoutEdited()->openPosition();

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

        return $jobs->count();
    }
}

if (! function_exists('companies')) {

    function companies()
    {
        $companies = Company::count();

        return $companies;
    }
}

if (! function_exists('newjob')) {

    function newjob()
    {
        $newjobs = Job::where('status', 'active')->where('created_at', '>=', Carbon::now()->subDays(7)->toDateString())->count();

        return $newjobs;
    }
}

if (! function_exists('candidate')) {
    function candidate()
    {
        $candidates = Candidate::count();

        return $candidates;
    }
}

if (! function_exists('linkActive')) {
    function linkActive($route, $class = 'active')
    {
        return request()->routeIs($route) ? $class : '';
    }
}

if (! function_exists('candidateNotifications')) {
    function candidateNotifications()
    {
        return auth()->user()->notifications()->take(6)->get();
    }
}

if (! function_exists('candidateNotificationsCount')) {

    function candidateNotificationsCount()
    {

        return auth()->user()->notifications()->count();
    }
}

if (! function_exists('candidateUnreadNotifications')) {

    function candidateUnreadNotifications()
    {
        return auth()->user()->unreadNotifications()->count();
    }
}

if (! function_exists('companyNotifications')) {

    function companyNotifications()
    {

        return auth()->user()->notifications()->take(6)->get();
    }
}

if (! function_exists('companyNotificationsCount')) {
    function companyNotificationsCount()
    {
        return auth()->user()->notifications()->count();
    }
}

if (! function_exists('companyUnreadNotifications')) {

    function companyUnreadNotifications()
    {

        return auth()->user()->unreadNotifications()->count();
    }
}

if (! function_exists('defaultCurrencySymbol')) {
    function defaultCurrencySymbol()
    {
        return config('templatecookie.app_currency_symbol');
    }
}

if (! function_exists('currencyAmountShort')) {

    function currencyAmountShort($amount)
    {
        $num = $amount * getCurrencyRate();
        $units = ['', 'K', 'M', 'B', 'T'];
        for ($i = 0; $num >= 1000; $i++) {
            $num /= 1000;
        }

        return round($num, 0).$units[$i];
    }
}

/* Currency position
 *
 * @param String $date
 */
if (! function_exists('changeCurrency')) {
    function changeCurrency($amount)
    {
        if (session()->has('current_currency')) {
            $current_currency = session('current_currency');
            $symbol = $current_currency->symbol;
            $position = $current_currency->symbol_position;
        } else {
            $symbol = config('templatecookie.currency_symbol');
            $position = config('templatecookie.currency_symbol_position');
        }

        $converted_amount = round($amount * getCurrencyRate(), 2);

        if ($position == 'left') {
            return $symbol.' '.$converted_amount;
        } else {
            return $converted_amount.' '.$symbol;
        }
    }
}

/**
 * Remove the decimal numbers and shorten
 *
 * @param  number  $amount
 * @return string
 */
if (! function_exists('zeroDecimal')) {
    function zeroDecimal($amount)
    {
        $units = ['', 'K', 'M', 'B', 'T'];
        for ($i = 0; $amount >= 1000; $i++) {
            $amount /= 1000;
        }

        return round($amount, 0).$units[$i];
    }
}

/**
 * Currency exchange
 *
 * @param  $amount
 * @param  $from
 * @param  $to
 * @param  $round
 * @return number
 */
if (! function_exists('currencyExchange')) {
    function currencyExchange($amount, $from = null, $to = null, $round = 2)
    {
        $from = currentCurrencyCode();
        $to = config('templatecookie.currency', 'USD');

        $fromRate = Currency::whereCode($from)->first()->rate;
        $toRate = Currency::whereCode($to)->first()->rate;
        $rate = $toRate / $fromRate;

        return round($amount * $rate);
    }
}

// if (! function_exists('currencyConversion')) {
//     function currencyConversion($amount, $to = 'USD', $round = 2)
//     {
//         $to = $to;

//         $checkCurrency = Currency::where('code', $to)->first();

//         if ($amount && $checkCurrency) {
//             $total = $amount * $checkCurrency->rate;

//             return (int) round($total, $round);
//         }

//         return $amount;

//         // $from = $from ?? config('templatecookie.currency');
//         // $to = $to ?? 'USD';

//         // $checkCurrency = Currency::where('code', $to)->first();
//         // if ($amount && $checkCurrency) {

//         //     $fromRate = Currency::whereCode($from)->first()?->rate ?? 1;
//         //     $toRate = Currency::whereCode($to)->first()?->rate ?? 1;
//         //     $rate = $fromRate / $toRate;
//         //     $result = $amount / $rate;

//         //     return (int) round($amount * $rate, 2);
//         // }

//         // $from = $from ?? config('templatecookie.currency');
//         // $to = $to ?? 'USD';

//         // $checkCurrency = Currency::where('code', $to)->first();
//         // if ($amount && $checkCurrency) {

//         //     $fromRate = Currency::whereCode($from)->first()?->rate ?? 1;
//         //     $toRate = Currency::whereCode($to)->first()?->rate ?? 1;
//         //     $rate = $fromRate / $toRate;
//         //     $result = $amount / $rate;

//         //     return (int) round($amount * $rate, 2);
//         // }
//     }
// }

if (! function_exists('currencyConversion')) {
    function currencyConversion($amount, $to = 'USD', $round = 2)
    {
        $to = $to;

        // Check if the target currency is TL
        if ($to === 'TL') {
            // Assuming 1 USD = 9 TL, replace this value with the actual conversion rate
            $usdToTLRate = 32.27; // For example purposes, replace with actual rate
            $total = $amount * $usdToTLRate;
        } else {
            // Convert to USD or any other currency
            $checkCurrency = Currency::where('code', $to)->first();

            if ($amount && $checkCurrency) {
                $total = $amount * $checkCurrency->rate;
            } else {
                return $amount;
            }
        }

        return (int) round($total, $round);
    }
}

if (! function_exists('usdAmount')) {
    function usdAmount($amount)
    {
        if (session('currency_rate')) {
            return round($amount / session('currency_rate.rate'), 2);
        } else {
            return round($amount * 1 / Currency::whereCode(config('templatecookie.currency'))->first()->rate, 2);
        }
    }
}

/**
 * Currency rate store in session
 *
 * @return void
 */
if (! function_exists('currencyRateStore')) {
    function currencyRateStore()
    {
        if (session()->has('currency_rate')) {
            $currency_rate = session('currency_rate');
            $from = config('templatecookie.currency');
            $to = currentCurrencyCode();

            if ($currency_rate['from'] != $from || $currency_rate['to'] != $to) {
                $fromRate = Currency::whereCode($from)->first()->rate;
                $toRate = Currency::whereCode($to)->first()->rate;
                $rate = $fromRate / $toRate;
                session(['currency_rate' => ['from' => $from, 'to' => $to, 'rate' => $rate]]);
            }
        } else {
            $from = config('templatecookie.currency');
            $to = currentCurrencyCode();

            $fromRate = Currency::whereCode($from)->first()->rate;
            $toRate = Currency::whereCode($to)->first()->rate;
            $rate = $fromRate / $toRate;
            session(['currency_rate' => ['from' => $from, 'to' => $to, 'rate' => $rate]]);
        }
    }
}

/**
 * Get currency rate
 *
 * @return number
 */
if (! function_exists('getCurrencyRate')) {
    function getCurrencyRate()
    {
        if (session()->has('currency_rate')) {
            $currency_rate = session('currency_rate');
            $rate = $currency_rate['rate'];

            return $rate;
        } else {
            return 1;
        }
    }
}

/**
 * Get current Currency
 *
 * @return object
 */
if (! function_exists('currentCurrency')) {
    function currentCurrency()
    {
        return session('current_currency') ?? loadSystemCurrency();
    }
}

/**
 * Get current Currency code
 *
 * @return string
 */
if (! function_exists('currentCurrencyCode')) {
    function currentCurrencyCode()
    {
        if (session()->has('current_currency')) {
            $currency = session('current_currency');

            return $currency->code;
        }

        return config('templatecookie.currency');
    }
}

/**
 * Get current Currency symbol
 *
 * @return string
 */
if (! function_exists('currentCurrencySymbol')) {
    function currentCurrencySymbol()
    {
        if (session()->has('current_currency')) {
            $currency = session('current_currency');

            return $currency->symbol;
        }

        return config('templatecookie.currency_symbol');
    }
}

if (! function_exists('currentLanguage')) {

    function currentLanguage()
    {
        return session('current_lang');
    }
}

if (! function_exists('langDirection')) {

    function langDirection()
    {
        return currentLanguage()?->direction ?? Language::where('code', config('templatecookie.default_language'))->value('direction');
    }
}

if (! function_exists('metaData')) {

    function metaData($page)
    {
        $current_language = currentLanguage(); // current session language
        $language_code = $current_language ? $current_language->code : 'en'; // language code or default one
        $page = Seo::where('page_slug', $page)->first(); // get page
        $exist_content = $page ? $page->contents()->where('language_code', $language_code)->first() : null; // get page content orderBy page && language
        $content = '';
        if ($exist_content) {
            $content = $exist_content;
        } else {
            $content = $page->contents()?->where('language_code', 'en')->first() ?? '';
        }

        return $content; // return response
    }
}

if (! function_exists('storePlanInformation')) {

    function storePlanInformation()
    {
        session()->forget('user_plan');
        session(['user_plan' => isset(auth()->user()->company->userPlan) ? auth()->user()->company->userPlan : []]);
    }
}

if (! function_exists('formatTime')) {

    function formatTime($date, $format = 'F d, Y H:i A')
    {
        return Carbon::parse($date)->format($format);
    }
}

if (! function_exists('inspireMe')) {

    function inspireMe()
    {
        Artisan::call('inspire');

        return Artisan::output();
    }
}

if (! function_exists('getUnsplashImage')) {
    function getUnsplashImage()
    {
        $url = 'https://source.unsplash.com/random/1920x1280/?nature,landscape,mountains';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $a = curl_exec($ch); // $a will contain all headers
    }
}

if (! function_exists('adminNotifications')) {
    function adminNotifications()
    {
        return auth('admin')->user()->notifications()->take(10)->get();
    }
}

if (! function_exists('adminUnNotifications')) {

    function adminUnNotifications()
    {
        return auth('admin')->user()->unreadNotifications()->count();
    }
}

if (! function_exists('checkMailConfig')) {

    function checkMailConfig()
    {
        $status = config('mail.mailers.smtp.transport') && config('mail.mailers.smtp.host') && config('mail.mailers.smtp.port') && config('mail.mailers.smtp.username') && config('mail.mailers.smtp.password') && config('mail.mailers.smtp.encryption') && config('mail.from.address') && config('mail.from.name');

        ! $status ? flashError(__('mail_not_sent_for_the_reason_of_incomplete_mail_configuration')) : '';

        return $status ? 1 : 0;
    }
}

if (! function_exists('openJobs')) {

    function openJobs()
    {
        return Job::where('status', 'active')->where('deadline', '>=', Carbon::now()->toDateString())->count();
    }
}

if (! function_exists('updateMap')) {
    function updateMap($data)
    {
        if (empty(config('templatecookie.map_show'))) {
            session()->put('location', [
                'country' => session('selectedCountryId'),
                'region' => session('selectedStateId'),
                'district' => session('selectedCityId'),
                'lng' => session('selectedCityLong') ?? session('selectedStateLong') ?? session('selectedCountryLong'),
                'lat' => session('selectedCityLat') ?? session('selectedStateLat') ?? session('selectedCountryLat'),
            ]);
        }
        $location = session()->get('location');

        if ($location) {
            $region = '';
            $region = array_key_exists('region', $location) ? $location['region'] : '';
            $country = array_key_exists('country', $location) ? $location['country'] : '';
            if ($region == 'undefined') {
                $address = Str::slug($country);
                $region = '';
            } else {
                $address = Str::slug($region.'-'.$country);
            }

            $data->update([
                'address' => $address,
                'neighborhood' => array_key_exists('neighborhood', $location) ? $location['neighborhood'] : '',
                'locality' => array_key_exists('locality', $location) ? $location['locality'] : '',
                'place' => array_key_exists('place', $location) ? $location['place'] : '',
                'district' => array_key_exists('district', $location) ? $location['district'] : '',
                'postcode' => array_key_exists('postcode', $location) ? $location['postcode'] : '',
                'region' => $region ?? '',
                'country' => array_key_exists('country', $location) ? $location['country'] : '',
                'long' => array_key_exists('lng', $location) ? $location['lng'] : '',
                'lat' => array_key_exists('lat', $location) ? $location['lat'] : '',
                'exact_location' => array_key_exists('exact_location', $location) ? $location['exact_location'] : '',
            ]);
            session()->forget('location');
            session([
                'selectedCountryId' => null,
                'selectedStateId' => null,
                'selectedCityId' => null,
                'selectedCountryLong' => null,
                'selectedCountryLat' => null,
                'selectedStateLong' => null,
                'selectedStateLat' => null,
                'selectedCityLong' => null,
                'selectedCityLat' => null,
            ]);
        }

        return true;
    }
}

if (! function_exists('selected_country')) {
    function selected_country()
    {
        $selected_country = session()->get('selected_country');

        if ($selected_country) {
            $countries = loadAllCountries();
            $country = $countries->where('id', $selected_country)->first() ?? loadCountry();

        } else {
            $country = loadCountry();
        }

        return $country;

        // $selected_country = session()->get('selected_country');
        // $country = Country::find($selected_country) ?? Country::first();

        // return $country;
    }
}

if (! function_exists('get_file_size')) {
    function get_file_size($file)
    {
        if (file_exists($file)) {
            $file_size = File::size($file) / 1024 / 1024;

            return round($file_size, 4).' MB';
        }

        return '0 MB';
    }
}

/**
 * Increases or decreases the brightness of a color by a percentage of the current brightness.
 *
 * @param  string  $hexCode  Supported formats: `#FFF`, `#FFFFFF`, `FFF`, `FFFFFF`
 * @param  float  $adjustPercent  A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
 * @return string
 *
 * @author  maliayas
 */
if (! function_exists('adjustBrightness')) {
    function adjustBrightness($hexCode, $adjustPercent)
    {
        $hexCode = ltrim($hexCode, '#');

        if (strlen($hexCode) == 3) {
            $hexCode = $hexCode[0].$hexCode[0].$hexCode[1].$hexCode[1].$hexCode[2].$hexCode[2];
        }

        $hexCode = array_map('hexdec', str_split($hexCode, 2));

        foreach ($hexCode as &$color) {
            $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
            $adjustAmount = ceil($adjustableLimit * $adjustPercent);

            $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
        }

        return '#'.implode($hexCode);
    }
}

if (! function_exists('current_country_code')) {
    function current_country_code()
    {
        if (selected_country()) {
            $country_code = selected_country()->sortname;
        } else {
            $setting = loadSetting();

            if ($setting->app_country_type != 'multiple_base') {
                $country_code = Country::find($setting->app_country)->sortname;
            } else {
                return '';
            }
        }

        return $country_code;
    }
}

/**
 * Set ip wise country, currency and language
 *
 * @return void
 */
if (! function_exists('setLocationCurrency')) {
    function setLocationCurrency()
    {
        $ip = request()->ip();
        // $ip = '103.102.27.0'; // Bangladesh
        // $ip = '105.179.161.212'; // Mauritius
        // $ip = '197.246.60.160'; // Egypt
        // $ip = '107.29.65.61'; // United States"
        // $ip = '46.39.160.0'; // Czech Republic
        // $ip = "94.112.58.11"; // Czechia

        if ($ip && $ip != '127.0.0.1') {
            $geo = GeoIP::getLocation($ip);

            // Set the currency
            if (! session()->has('current_currency')) {
                $currency = Modules\Currency\Entities\Currency::where('code', $geo->currency)->first() ?? Modules\Currency\Entities\Currency::where('code', config('templatecookie.currency'))->first();

                if ($currency) {
                    session(['current_currency' => $currency]);
                } else {
                    session(['current_currency' => Modules\Currency\Entities\Currency::first()]);
                }
            }

            // Set the language
            if (! session()->has('current_lang')) {
                $path = base_path('resources/backend/dummy-data/country_currency_language.json');
                $country_language_currency = json_decode(file_get_contents($path), true);
                $key = array_search($geo->iso_code, array_column($country_language_currency, 'code'));
                $country_language_currency = $country_language_currency[$key];
                $lang_code = $country_language_currency['language']['code'];
                $language = Language::where('code', $lang_code)->first();

                if ($language) {
                    session(['current_lang' => $language]);
                } else {
                    session(['current_lang' => Language::where('code', config('templatecookie.default_language'))->first()]);
                }
            }

            // Set the country
            // $selected_country = session('country_code');
            $selected_country = session('selected_country');

            if (! session()->has('selected_country')) {
                // if (!session()->has('country_code')) {
                if ($selected_country != 'all') {
                    if ($ip) {
                        $current_user_data = Location::get($ip);
                    }
                    if ($current_user_data) {
                        $user_country = $current_user_data->countryName;
                        if ($user_country) {
                            $database_country = Country::where('name', $user_country)->where('status', 1)->first();
                            if ($database_country) {
                                // $selected_country = session()->get('country_code');
                                $selected_country = session()->get('selected_country');
                                if (! $selected_country) {
                                    // session()->put('country_code', $database_country->sortname);
                                    session()->put('selected_country', $database_country->id);

                                    return true;
                                }
                            }
                        }
                    }
                }
            } else {
                // $selected_country = session('country_code');
                $selected_country = session('selected_country');
            }
        }
    }
}

/**
 * @param  string  $date
 *                        Date format
 */
if (! function_exists('getLanguageByCode')) {
    function getLanguageByCode($code)
    {
        $languages = loadLanguage();

        return $languages->where('code', $code)->value('name');
    }
}

/**
 * @param  string  $date
 *                        Date format
 */
if (! function_exists('getLanguageByCodeInLookUp')) {
    function getLanguageByCodeInLookUp($code, $languages)
    {
        $language = $languages->where('code', $code)->first();

        return $language ? $language->name : '';
    }
}

/**
 * @param  string  $date
 *                        Date format
 */
if (! function_exists('currentLangCode')) {
    function currentLangCode()
    {

        if (session('current_lang')) {
            return session('current_lang')->code;
        } else {
            return loadSystemLanguageCode();
        }
    }
}

/**
 * @param  string  $date
 *                        Date format
 */
if (! function_exists('dateFormat')) {
    function dateFormat($date, $format = 'F Y')
    {
        return \Carbon\Carbon::createFromFormat($format, $date)->toDateTimeString();
    }
}

/* Currency position
    *
    * @param String $date
    */
if (! function_exists('currencyPosition')) {
    function currencyPosition($amount, $applyCurrencyRate = false, $current_currency = null)
    {
        if (! $current_currency) {
            $current_currency = currentCurrency();
        }
        $symbol = $current_currency->symbol;
        $position = $current_currency->symbol_position;

        if ($applyCurrencyRate) {
            $amount = $amount / getCurrencyRate();
            $amount = round($amount, 2);
        }

        if ($position == 'left') {
            return $symbol.' '.$amount;
        } else {
            return $amount.' '.$symbol;
        }

        return $amount;
    }
}

/**
 * Authenticate candidate
 */
if (! function_exists('currentCandidate')) {
    function currentCandidate()
    {
        return authUser()->candidate;
    }
}

/**
 * Authenticate candidate
 */
if (! function_exists('currentCompany')) {
    function currentCompany()
    {
        return authUser()->company;
    }
}

/**
 * Authenticate user
 */
if (! function_exists('authUser')) {
    function authUser()
    {
        return auth('user')->user();
    }
}

/* Get format number for currency
    *
    * @param String $path
    */
if (! function_exists('getFormattedNumber')) {
    function getFormattedNumber(
        $value,
        $currencyCode = 'USD',
        $locale = 'en_US',
        $style = NumberFormatter::DECIMAL,
        $precision = 0,
        $groupingUsed = true,
    ) {
        if (session()->has('current_lang')) {
            $locale = currentLanguage()->code.'_us' ?? $locale;
        }

        $currencyCode = currentCurrencyCode();
        $formatter = new NumberFormatter($locale, $style);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);
        $formatter->setAttribute(NumberFormatter::GROUPING_USED, $groupingUsed);
        if ($style == NumberFormatter::CURRENCY) {
            $formatter->setTextAttribute(NumberFormatter::CURRENCY_CODE, $currencyCode);
        }

        return $formatter->format($value / getCurrencyRate());
    }
}

/**
 * Checks jobs status like highlighted or featured
 *
 * @param  string  $date
 * @return bool
 */
if (! function_exists('isFuture')) {
    function isFuture($date = null): bool
    {

        if ($date) {
            return Carbon::parse($date)->isFuture();
        }

        return false;
    }
}

if (! function_exists('getEmailTemplateFormatFlagsByType')) {
    function getEmailTemplateFormatFlagsByType($type)
    {
        return \App\Http\Controllers\Admin\EmailTemplateController::getFormatterByType($type) ?? [];
    }
}

if (! function_exists('getFormattedTextByType')) {
    function getFormattedTextByType($type, $data = null)
    {
        return \App\Http\Controllers\Admin\EmailTemplateController::getFormattedTextByType($type, $data);
    }
}

/**
 * get formatted mail template
 *
 * @param  string  $type
 * @param  mixed  $data
 * @return string formatted mail content
 */
if (! function_exists('getFormattedMail')) {
    function getFormattedMail($type, $data)
    {
        return \App\Http\Controllers\Admin\EmailTemplatesController::formatMessage($type, $data);
    }
}

/**
 * get list of available format flags
 *
 * @param  string  $type
 * @return array list of available format flags
 */
if (! function_exists('getFormatFlagsByType')) {
    function getFormatFlagsByType($type)
    {
        return \App\Http\Controllers\Admin\EmailTemplatesController::getFormatByType($type)['search'] ?? [];
    }
}

/**
 * Create avatar image
 *
 * @param  string  $name
 * @param  string  $path
 * @return bool
 */

//  if (! function_exists('createAvatar')) {
//     function createAvatar($name = null, $path = 'uploads/images'): string
//     {
//         if (! File::exists($path)) {
//             File::makeDirectory($path, $mode = 0777, true, true);
//         }

//         $name = $name ? $name.'_'.time().'_'.uniqid() : time().'_'.uniqid();
//         Avatar::create($name)->setDimension(250, 250)->save("{$path}/{$name}.png", 100);

//         return "{$path}/{$name}.png";
//     }
// }

if (! function_exists('createAvatar')) {
    function createAvatar($name, $path, $setDimension = null, $quality = 60): string
    {
        $mainAvatar = Avatar::create($name);

        if (! File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }

        $name = $name ? $name.'_'.time().'_'.uniqid() : time().'_'.uniqid();

        if ($setDimension) {
            $mainAvatar->setDimension($setDimension[0], $setDimension[1]);
        }

        // Avatar::create($name)->setDimension($setDimension[0], $setDimension[1])->save("{$path}/{$name}.jpg", $quality);
        $mainAvatar->save($path.'/'.$name.'jpg', $quality);
        // Avatar::create($name)->setDimension($setDimension[0], $setDimension[1])->save("{$path}/{$name}.jpg", $quality);

        // dd("{$path}/{$name}.jpg", $quality);

        // return "{$path}/{$name}.jpg";
        return $path.'/'.$name.'jpg';
    }
}

if (! function_exists('generateReference')) {
    function generateReference(?string $transactionPrefix = null)
    {
        if ($transactionPrefix) {
            return $transactionPrefix.'_'.uniqid(time());
        }

        return 'flw_'.uniqid(time());
    }
}

/**
 * URL match helper
 *
 * @param  string  $current_url
 * @param  string  $value_url
 * @return bool
 */
if (! function_exists('urlMatch')) {
    function urlMatch(?string $current_url = null, ?string $value_url = null)
    {
        if ($current_url == $value_url) {
            return true;
        }

        return false;
    }
}

if (! function_exists('currentLocation')) {
    function currentLocation()
    {
        $ip = request()->ip();
        // $ip = '103.102.27.0'; // Bangladesh
        // $ip = '105.179.161.212'; // Mauritius
        // $ip = '110.33.122.75'; // AUD
        // $ip = '5.132.255.255'; // SA
        // $ip = '107.29.65.61'; // United States"
        // $ip = '46.39.160.0'; // Czech Republic
        // $ip = "94.112.58.11"; // Czechia

        $current_user_data = Location::get($ip);

        if ($ip && $current_user_data && $current_user_data->countryName) {
            return $current_user_data->countryName;
        }

        return null;
    }
}

if (! function_exists('loadCms')) {
    function loadCms()
    {
        return Cache::remember('Cms', now()->addDays(2), function () {
            return Cms::first();
        });
    }
}

if (! function_exists('loadCurrency')) {
    function loadCurrency()
    {
        return Cache::remember('Currency', now()->addDays(2), function () {
            return Currency::first();
        });
    }
}

if (! function_exists('loadSetting')) {
    function loadSetting()
    {
        return Cache::remember('setting', now()->addDays(2), function () {
            return Setting::first();
        });
    }
}

if (! function_exists('loadLanguage')) {
    function loadLanguage()
    {
        return Cache::remember('languages', now()->addDays(2), function () {
            return Language::all();
        });
    }
}

if (! function_exists('loadCookies')) {
    function loadCookies()
    {
        return Cache::remember('cookies', now()->addDays(30), function () {
            return Cookies::first();
        });
    }
}

if (! function_exists('loadDefaultLanguage')) {
    function loadDefaultLanguage()
    {
        return Cache::remember('default_language', now()->addDays(30), function () {
            return Language::where('code', config('templatecookie.default_language'))->first();
        });
    }
}

if (! function_exists('loadCountry')) {
    function loadCountry()
    {
        return Cache::remember('country', now()->addDays(30), function () {
            return Country::first();
        });
    }
}

if (! function_exists('loadAllCountries')) {
    function loadAllCountries()
    {
        return Cache::remember('countries', now()->addDays(30), function () {
            return Country::all();
        });
    }
}

if (! function_exists('loadActiveCountries')) {
    function loadActiveCountries()
    {
        return Cache::remember('active_countries', now()->addDays(30), function () {
            return Country::select('id', 'name', 'slug', 'icon')->active()->get();
        });
    }
}

if (! function_exists('loadAllCurrencies')) {
    function loadAllCurrencies()
    {
        return Cache::remember('currencies', now()->addDays(30), function () {
            return Currency::all();
        });
    }
}

/**
 * Get the system currency
 *
 * @return object
 */
if (! function_exists('loadSystemCurrency')) {
    function loadSystemCurrency()
    {
        return Cache::remember('systemCurrency', now()->addDays(30), function () {
            // return Modules\Currency\Entities\Currency::where('code', config('jobpilot.currency'))->first();
            return Modules\Currency\Entities\Currency::where('code', config('templatecookie.currency'))->first();
        });
    }
}

/**
 * Get the system currency
 *
 * @return object
 */
if (! function_exists('loadSystemLanguageCode')) {
    function loadSystemLanguageCode()
    {
        return Cache::remember('systemLanguageCode', now()->addDays(30), function () {
            return Language::where('code', config('templatecookie.default_language'))->value('code');
        });
    }
}

/**
 * Getting cache information
 *
 * @return string
 */
if (! function_exists('getCache')) {
    function getCache($name)
    {
        return Cache::get($name);
    }
}

/**
 * Getting cache information
 *
 * @return string
 */
if (! function_exists('forgetCache')) {
    function forgetCache($name)
    {
        Cache::forget($name);
        loadSetting();

        return true;
    }
}

/**
 * @param  $fullName
 * @return string
 */
if (! function_exists('maskFullName')) {
    function maskFullName($fullName)
    {
        $nameParts = explode(' ', $fullName); // Split the full name into parts
        $maskedName = '';

        foreach ($nameParts as $part) {
            $initial = substr($part, 0, 1); // Get the first letter
            $rest = substr($part, 1); // Get the rest of the letters
            $maskedName .= $initial.str_repeat('*', strlen($rest)).' '; // Replace the rest with asterisks
        }

        return rtrim($maskedName); // Remove trailing space and return the masked name
    }
}

if (! function_exists('advertisementCode')) {
    function advertisementCode($page_slug)
    {

        $ads = loadAdvertisements();
        $code = '';
        $ad = $ads->where('page_slug', $page_slug)->where('status', 1)->first();
        if ($ad) {
            $code = $ad->ad_code;
        }

        return $code;
    }
}

if (! function_exists('advertisement_status')) {
    function advertisement_status($page_slug)
    {
        $ad_status = 0;
        $ad_status = Advertisement::where('page_slug', '=', $page_slug)->value('status');

        return $ad_status;
    }
}

if (! function_exists('loadAdvertisements')) {
    function loadAdvertisements()
    {
        return Cache::remember('advertisements', now()->addDays(30), function () {
            return Advertisement::all();
        });
    }
}
