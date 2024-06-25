<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Language\Entities\Language;

class EducationController extends Controller
{
    public function index()
    {
        try {
            $educations = Education::all();
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.education.index', compact('educations', 'app_language'));
        } catch (\Exception $e) {
            Log::error('An error occurred: '.$e->getMessage());
            flashError('An error occurred while fetching education data.');

            return back();
        }
    }

    // public function store(Request $request)
    // {
    //     try {
    //         $app_language = Language::latest()->get(['code', 'name']);
    //         $validate_array = [];
    //         foreach ($app_language as $language) {
    //             $validate_array['name_'.$language->code] = 'required|string|max:255';
    //         }
    //         $this->validate($request, $validate_array);

    //         $education = new Education();
    //         $education->save();

    //         foreach ($request->except('_token') as $key => $value) {
    //             $education->save();
    //         }

    //         flashSuccess(__('education_created_successfully'));

    //         return back();
    //     } catch (\Exception $e) {
    //         Log::error('An error occurred: '.$e->getMessage());
    //         flashError('An error occurred while creating education.');

    //         return back();
    //     }
    // }
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
            $skill = new Education();
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

    public function edit(Education $education)
    {
        try {
            $educations = Education::all();
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.education.index', compact('education', 'educations', 'app_language'));
        } catch (\Exception $e) {
            Log::error('An error occurred: '.$e->getMessage());
            flashError('An error occurred while fetching education data for editing.');

            return back();
        }
    }

    public function update(Request $request, Education $education)
    {
        try {
            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $this->validate($request, $validate_array);

            // saving the data
            foreach ($request->except(['_token', '_method']) as $key => $value) {
                $education->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $education->save();
            }

            flashSuccess(__('education_update_successfully'));

            return back();
        } catch (\Exception $e) {
            Log::error('An error occurred: '.$e->getMessage());
            flashError('An error occurred while updating education.');

            return back();
        }
    }

    public function destroy(Education $education)
    {
        try {
            if ($education && count($education->job)) {
                flashError(__('education_has_companies'));

                return back();
            }

            $education->delete();
            flashSuccess(__('education_delete_successfully'));

            return redirect()->route('education.index');
        } catch (\Exception $e) {
            Log::error('An error occurred: '.$e->getMessage());
            flashError('An error occurred while deleting education.');

            return back();
        }
    }
}
