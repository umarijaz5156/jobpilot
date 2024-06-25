<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use App\Models\JobRole;
use App\Models\JobType;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profession;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use F9Web\ApiResponseHelpers;
use App\Http\Controllers\Controller;
use Modules\Location\Entities\Country;
use Modules\Currency\Entities\Currency;
use Modules\Language\Entities\Language;

class AttributeController extends Controller
{
    use ApiResponseHelpers;

    public function countries(){
        $countries = Country::all()->transform(function($country){
            return [
                'id' => $country->id,
                'name' => $country->name,
                'sortname' => $country->sortname,
                'status' => $country->status,
            ];
        });

        return $this->respondWithSuccess([
            'data' => $countries
        ]);
    }

    public function changeCountry($countryId)
    {
        try {
            session()->put('selected_country', $countryId);

            return $this->respondWithSuccess([
                'data' => [
                    'message' => 'Country changed successfully',
                    'current_country' => Country::find($countryId)
                ]
            ]);
            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function currencies(){
        $currencies = Currency::all()->transform(function($currency){
            return [
                'id' => $currency->id,
                'name' => $currency->name,
                'code' => $currency->code,
                'symbol' => $currency->symbol,
            ];
        });

        return $this->respondWithSuccess([
            'data' => $currencies
        ]);
    }

    public function changeCurrency($code)
    {
        try {
            $currency = Currency::where('code', $code)->first();
            session(['current_currency' => $currency]);
            currencyRateStore();

            return $this->respondWithSuccess([
                'data' => [
                    'message' => 'Currency changed successfully',
                    'current_currency' => $currency
                ]
            ]);
        } catch (\Exception $e) {
            return $this->respondError('An error occurred: '. $e->getMessage());
        }
    }

    public function languageList(){
        $current_language = currentLanguage() ? currentLanguage() : Language::where('code', config('zakirsoft.default_language'))->first();

        return $this->respondWithSuccess([
            'data' => [
                'current_language' => $current_language,
                'languages' => Language::all()
            ]
        ]);
    }

    public function changeLanguage($code){
        $language = Language::where('code', $code)->first();

        if (!$language) {
            return $this->respondError('language_not_found');
        }

        session(['current_lang' => $language]);
        app()->setLocale($language);

        return $this->respondWithSuccess([
            'data' => [
                'message' => 'Language changed successfully',
                'current_language' => $language
            ]
        ]);
    }

    public function categories(){
        $categories = JobCategory::all()->transform(function($category){
            return [
                'id' => $category->id,
                'name' => $category->name,
                'image' => $category->image_url,
                'is_svg' => str_contains($category->image, '.svg'),
            ];
        });

        return $this->respondWithSuccess([
            'data' => $categories
        ]);
    }

    public function job_roles(){
        $job_roles = JobRole::all()->transform(function($role){
            return [
                'id' => $role->id,
                'name' => $role->name,
            ];
        });

        return $this->respondWithSuccess([
            'data' => $job_roles
        ]);
    }

    public function experiences(){
        $experiences = Experience::all()->transform(function($experience){
            return [
                'id' => $experience->id,
                'name' => $experience->name,
            ];
        });

        return $this->respondWithSuccess([
            'data' => $experiences
        ]);
    }

    public function educations(){
        $educations = Education::all()->transform(function($education){
            return [
                'id' => $education->id,
                'name' => $education->name,
            ];
        });

        return $this->respondWithSuccess([
            'data' => $educations
        ]);
    }

    public function job_types(){
        $job_types = JobType::all()->transform(function($job_type){
            return [
                'id' => $job_type->id,
                'name' => $job_type->name,
            ];
        });

        return $this->respondWithSuccess([
            'data' => $job_types
        ]);
    }

    public function professions(){
        $professions = Profession::all()->transform(function($profession){
            return [
                'id' => $profession->id,
                'name' => $profession->name,
            ];
        });

        return $this->respondWithSuccess([
            'data' => $professions
        ]);
    }

    public function popular_tags(){
        $tags = Tag::popular()
            ->withCount('tags')
            ->latest('tags_count')
            ->get()
            ->take(10)
            ->transform(function($tag){
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'jobs_count' => $tag->tags_count,
                    'tags_count' => $tag->tags_count,
                ];
            });

        return $this->respondWithSuccess([
            'data' => $tags,
        ]);
    }

    public function currentSession(){
        return $this->respondWithSuccess([
            'data' => [
                'current_country' => selected_country(),
                'current_currency' => currentCurrency(),
                'current_language' => currentLanguage() ? currentLanguage() : loadDefaultLanguage(),
            ]
        ]);
    }

    public function fetchTranslations(){
        $languages = Language::all();

        return $this->respondWithSuccess([
            'data' => [
                'default_language_code' => config('templatecookie.default_language'),
                'languages' => $languages->map(function($language){
                    return [
                        'name' => $language->name,
                        'code' => $language->code,
                    ];
                }),
                'translations' => $languages->map(function($language){
                    return [
                        'code' => $language->code,
                        'translations' => \File::json(resource_path("lang/$language->code.json")),
                    ];
                })
            ]
        ]);
    }
}
