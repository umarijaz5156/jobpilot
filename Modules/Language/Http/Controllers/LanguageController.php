<?php

namespace Modules\Language\Http\Controllers;

use App\Models\LanguageData;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Language\Entities\Language;

class LanguageController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only(['destroy', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        try {
            if (! userCan('setting.view')) {
                return abort(403);
            }

            $languagesList = Language::get();

            return view('language::index', compact('languagesList'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        try {
            if (! userCan('setting.update')) {
                return abort(403);
            }
            // if (!enableModule('language')) {
            //     return abort(404);
            // }
            $path = base_path('Modules/Language/Resources/json/languages.json');
            $translations = json_decode(file_get_contents($path), true);

            return view('language::create', compact('translations'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            if (! userCan('setting.update')) {
                return abort(403);
            }
            $request->validate(
                [
                    'name' => 'required|unique:languages,name',
                    'code' => 'required|unique:languages,code',
                    'icon' => 'required|unique:languages,icon',
                    'direction' => 'required',
                ],
                [
                    'name.required' => 'You must select a language',
                    'code.required' => 'You must select a language code',
                    'icon.required' => 'You must select a flag',
                    'direction.required' => 'You must select a direction',
                    'name.unique' => 'This language already exists',
                    'code.unique' => 'This code already exists',
                    'icon.unique' => 'This flag already exists',
                ],
            );

            $language = Language::create([
                'name' => $request->name,
                'code' => $request->code,
                'icon' => $request->icon,
                'direction' => $request->direction,
            ]);

            if ($language) {
                $baseFile = base_path('resources/lang/en.json');
                $fileName = base_path('resources/lang/'.Str::slug($request->code).'.json');
                copy($baseFile, $fileName);

                $this->createLanguageData($request->code);

                flashSuccess(__('language_created_successfully_please_translate_the_language_as_per_your_need'));

                return redirect()->route('languages.index', $language->code);
            } else {
                flashError();

                return back();
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show($id)
    {
        try {
            return view('language::show');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit(Language $language)
    {
        try {
            if (! userCan('setting.update')) {
                return abort(403);
            }

            $path = base_path('Modules/Language/Resources/json/languages.json');
            $translations = json_decode(file_get_contents($path), true);

            return view('language::edit', compact('translations', 'language'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function update(Request $request, Language $language)
    {
        try {
            // validation
            $request->validate(
                [
                    'name' => "required|unique:languages,name,{$language->id}",
                    'code' => "required|unique:languages,code,{$language->id}",
                    'icon' => "required|unique:languages,icon,{$language->id}",
                    'direction' => 'required',
                ],
                [
                    'name.required' => 'You must select a language',
                    'code.required' => 'You must select a code',
                    'icon.required' => 'You must select a flag',
                    'direction.required' => 'You must select a direction',
                    'name.unique' => 'This language already exists',
                    'code.unique' => 'This code already exists',
                    'icon.unique' => 'This flag already exists',
                ],
            );
            $code = $language->code;

            // rename file
            $oldFile = $language->code.'.json';
            $oldName = base_path('resources/lang/'.$oldFile);
            $newFile = Str::slug($request->code).'.json';
            $newName = base_path('resources/lang/'.$newFile);

            rename($oldName, $newName);

            // update
            $updated = $language->update([
                'name' => $request->name,
                'code' => $request->code,
                'icon' => $request->icon,
                'direction' => $request->direction,
            ]);

            $data = LanguageData::where('code', $code)->first();
            $data->code = $request->code;
            $data->save();

            $updated ? flashSuccess(__('language_updated_successfully')) : flashError();

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy(Language $language)
    {
        try {
            if (! userCan('setting.update')) {
                return abort(403);
            }

            if (config('templatecookie.default_language') == $language->code) {
                flashError("You can't delete default language");

                return back();
            }

            // delete file
            if (File::exists(base_path('resources/lang/'.$language->code.'.json'))) {
                File::delete(base_path('resources/lang/'.$language->code.'.json'));
            }

            $deleted = $language->delete();
            $languageData = LanguageData::where('code', $language->code)
                ->first();
            if ($languageData) {

                $languageData->delete();
            }

            $deleted ? flashSuccess(__('language_deleted_successfully')) : flashError();

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function setLanguage(Request $request)
    {
        try {
            if (config('templatecookie.default_language') != $request->code) {
                envReplace('APP_DEFAULT_LANGUAGE', $request->code);
                forgetCache('default_language');
                forgetCache('systemLanguageCode');
            }

            // menu list cache clear
            Cache::forget('menu_lists');

            flashSuccess(__('default_language_set_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function syncLanguage(Language $language)
    {
        try {
            // English Language Translations
            $en_path = base_path('resources/lang/en.json');
            $en_translations = json_decode(file_get_contents($en_path), true);

            // Sync Language Translations
            $lang_path = base_path("resources/lang/{$language->code}.json");
            $lang_translations = json_decode(file_get_contents($lang_path), true);

            // Unique Values between English and Sync Language
            $unique_values = collect($en_translations)->diffKeys($lang_translations);

            if (! count($unique_values)) {
                flashWarning(__('nothing_to_sync'));

                return back();
            }

            // Merge Unique Values with Sync Language
            $translations = array_merge($lang_translations, json_decode($unique_values, true));

            // Update sync language json
            file_put_contents(base_path("resources/lang/{$language->code}.json"), json_encode($translations, JSON_UNESCAPED_UNICODE));

            $this->syncLanguageJson($language->code);

            flashSuccess(__('language_sync_successfully'));

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }

    private function createLanguageData($code)
    {
        try {
            $currentJsonPath = base_path('resources/lang/'.$code.'.json');
            $currentJson = json_decode(File::get($currentJsonPath), true);

            LanguageData::create([
                'code' => $code,
                'data' => json_encode($currentJson),
            ]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    private function syncLanguageJson($code)
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

            // Save the merged JSON back to the JSON file
            File::put($currentJsonPath, json_encode($mergedJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
