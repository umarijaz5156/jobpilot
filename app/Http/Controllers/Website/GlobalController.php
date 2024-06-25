<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GlobalController extends Controller
{
    public function changeLanguage($lang)
    {
        try {
            session()->put('set_lang', $lang);
            app()->setLocale($lang);

            // menu list cache clear
            Cache::forget('menu_lists');

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function fetchCurrentTranslatedText()
    {
        try {
            $json = base_path('resources/lang/'.app()->getLocale().'.json');

            if (file_exists($json)) {
                $json = json_decode(file_get_contents($json), true);
            } else {
                $json = [];
            }

            return $json;
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function checkUsername(Request $request, $name)
    {
        try {
            if (! auth('user')->check()) {
                abort(404);
            }

            if ($request->type == 'company_username') {
                $username_exists = User::where('username', $name)
                    ->where('id', '!=', auth()->id())
                    ->exists();

                return $username_exists ? 'true' : 'false';
            }

            abort(404);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Ckeditor image upload
     *
     * @return array
     */
    public function ckeditorImageUpload(Request $request)
    {
        try {
            if ($request->hasFile('upload')) {
                $originName = $request->file('upload')->getClientOriginalName();
                $fileName = pathinfo($originName, PATHINFO_FILENAME);
                $extension = $request->file('upload')->getClientOriginalExtension();
                $fileName = $fileName.'_'.time().'.'.$extension;
                $request->file('upload')->move(public_path('uploads/ckeditor'), $fileName);

                $url = asset('uploads/ckeditor/'.$fileName);

                return response()->json([
                    'uploaded' => true,
                    'url' => $url,
                ]);
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function migrateData()
    {
        try {
            \Artisan::call('migrate');

            session()->flash('success', 'Migrated Successfully');

            return redirect()->route('website.home');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function optimizeClear()
    {
        try {
            \Artisan::call('optimize:clear');

            flashSuccess('Cache cleared successfully');

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
