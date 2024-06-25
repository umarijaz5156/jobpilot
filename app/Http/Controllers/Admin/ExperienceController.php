<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Language\Entities\Language;

class ExperienceController extends Controller
{
    public function index()
    {
        try {
            $experiences = Experience::all();
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.experience.index', compact('experiences', 'app_language'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function store(Request $request)
    {
        try {
            abort_if(! userCan('skills.create'), 403);

            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $this->validate($request, $validate_array);

            // saving the data
            $skill = new Experience();
            $skill->slug = Str::slug($request->name_en);
            $skill->save();

            foreach ($request->except('_token') as $key => $value) {
                $skill->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $skill->save();
            }

            flashSuccess(__('skill_created_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function edit(Experience $experience)
    {
        try {
            $experiences = Experience::all();
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.experience.index', compact('experience', 'experiences', 'app_language'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function update(Request $request, Experience $experience)
    {
        try {
            //  validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $this->validate($request, $validate_array);

            // saving the data
            foreach ($request->except(['_token', '_method']) as $key => $value) {
                $experience->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $experience->save();
            }
            flashSuccess(__('experience_update_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function destroy(Experience $experience)
    {
        try {
            if ($experience && count($experience->job)) {
                flashError(__('experience_has_companies'));

                return back();
            }
            $experience->delete();
            flashSuccess(__('experience_delete_successfully'));

            return redirect()->route('experience.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
