<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Benefit;
use App\Models\BenefitTranslation;
use Illuminate\Http\Request;
use Modules\Language\Entities\Language;

class BenefitController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'destroy',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            abort_if(! userCan('benefits.view'), 403);

            $benefits = Benefit::all();
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.benefit.index', compact('benefits', 'app_language'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            abort_if(! userCan('benefits.create'), 403);

            // Validate form data
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $benefit_exists = BenefitTranslation::where('name', $request->input('name_'.$language->code))
                    ->where('locale', $language->code)
                    ->first();
                if ($benefit_exists) {
                    flashError(__('benefit_already_exists_in').' '.getLanguageByCode($language->code).' '.__('language'));

                    return back();
                }

                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $this->validate($request, $validate_array);

            // Create new benefit with translations
            $benefit = new Benefit();
            $benefit->save();

            foreach ($request->except('_token') as $key => $value) {
                $benefit->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $benefit->save();
            }

            flashSuccess(__('benefit_created_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Benefit $benefit)
    {
        try {
            abort_if(! userCan('benefits.update'), 403);

            $benefits = Benefit::all();
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.benefit.index', compact('benefit', 'benefits', 'app_language'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Benefit $benefit)
    {
        try {
            abort_if(! userCan('benefits.update'), 403);

            // Validate form data
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $benefit_exists = BenefitTranslation::where('name', $request->input('name_'.$language->code))
                    ->where('locale', $language->code)
                    ->where('benefit_id', '!=', $benefit->id)
                    ->exists();
                if ($benefit_exists) {
                    flashError(__('benefit_already_exists_in').' '.getLanguageByCode($language->code).' '.__('language'));

                    return back();
                }

                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $this->validate($request, $validate_array);

            // Update benefit with translations
            foreach ($request->except(['_token', '_method']) as $key => $value) {
                $benefit->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $benefit->save();
            }

            flashSuccess(__('benefit_updated_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Benefit $benefit)
    {
        try {
            abort_if(! userCan('benefits.delete'), 403);

            // Check if benefit has jobs
            if ($benefit && $benefit->job_benefit->count()) {
                flashError(__('benefit_has_jobs'));

                return back();
            }

            // Delete benefit
            $benefit->delete();

            flashSuccess(__('benefit_deleted_successfully'));

            return redirect()->route('benefit.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
