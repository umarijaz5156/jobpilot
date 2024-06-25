<?php

namespace Modules\Language\Http\Controllers;

use App\Models\LanguageData;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Modules\Language\Entities\Language;

class TranslationController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'setDefaultLanguage', 'transUpdate',
        ]);
    }

    public function transUpdate(Request $request)
    {
        try {
            if (! userCan('setting.update')) {
                return abort(403);
            }

            $language = Language::findOrFail($request->lang_id);

            $data = file_get_contents(base_path('resources/lang/'.$language->code.'.json'));

            $translations = json_decode($data, true);

            foreach ($translations as $key => $value) {
                if ($request->$key && gettype($request->$key) == 'string') {
                    $translations[$key] = $request->$key;
                } else {
                    $translations[$key] = $value;
                }
            }

            $updated = file_put_contents(base_path('resources/lang/'.$language->code.'.json'), json_encode($translations, JSON_UNESCAPED_UNICODE));
            $this->updateInDatabase($language->code);

            $updated ? flashSuccess(__('translations_updated_successfully')) : flashError();

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function autoTransSingle(Request $request)
    {
        try {
            $text = autoTransLation($request->lang, $request->text);

            return response()->json($text);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function langView($code)
    {
        try {
            if (! userCan('setting.update')) {
                return abort(403);
            }

            $path = base_path('resources/lang/'.$code.'.json');
            $language = Language::where('code', $code)->first();
            $originalTranslations = json_decode(file_get_contents($path), true);

            // Get the search keyword from the request
            $keyword = request('keyword');

            // Filter translations based on the keyword
            $translations = $originalTranslations;
            if (! empty($keyword)) {
                $translations = array_filter($translations, function ($value) use ($keyword) {
                    // You can customize the condition based on your requirements
                    return stripos($value, $keyword) !== false;
                });
            }

            // Set the number of items per page to 100
            $perPage = 100;

            // Create a paginator using the paginate method
            $page = request()->input('page', 1);
            $offset = ($page - 1) * $perPage;
            $slicedTranslations = array_slice($translations, $offset, $perPage);
            $translations = new LengthAwarePaginator($slicedTranslations, count($translations), $perPage, $page, ['path' => route('languages.view', ['code' => $code])]);

            return view('language::lang_view', compact('language', 'translations', 'keyword'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function changeLanguage($lang)
    {
        try {
            $get_lang = Language::where('code', $lang)->first();
            if (! $get_lang) {
                return back();
            }

            session(['current_lang' => $get_lang]);

            app()->setLocale($lang);

            // menu list cache clear
            Cache::forget('menu_lists');

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function setDefaultLanguage(Request $request)
    {
        try {
            if (config('templatecookie.default_language') != $request->code) {
                envReplace('APP_DEFAULT_LANGUAGE', $request->code);
            }

            if (session()->get('set_lang') != $request->code) {
                session()->put('set_lang', $request->code);
                app()->setLocale($request->code);
            }

            // menu list cache clear
            Cache::forget('menu_lists');

            return back()->with('success', 'Default Language Added Successful');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    private function updateInDatabase($code)
    {
        try {
            $value = LanguageData::where('code', $code)->first();

            $currentJsonPath = base_path('resources/lang/'.$value->code.'.json');
            $currentJson = json_decode(File::get($currentJsonPath), true);
            $databaseJson = json_decode($value->data, true);

            // Merge the current JSON with the database JSON, keeping existing keys
            $mergedJson = array_merge($databaseJson, $currentJson);

            // Save the merged JSON back to the database
            $value->update(['data' => json_encode($mergedJson)]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
