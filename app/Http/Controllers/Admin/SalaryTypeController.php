<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Language\Entities\Language;

class SalaryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salaryTypes = SalaryType::all();
        $app_language = Language::latest()->get(['code', 'name']);

        return view('backend.salaryType.index', compact('salaryTypes', 'app_language'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
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
            $skill = new SalaryType();
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

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($request)
    {
        $salaryType_id = SalaryType::find($request);
        $salaryTypes = SalaryType::all();
        $app_language = Language::latest()->get(['code', 'name']);

        return view('backend.salaryType.index', compact('salaryType_id', 'salaryTypes', 'app_language'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalaryType $salaryType)
    {
        // validation
        $app_language = Language::latest()->get(['code', 'name']);
        $validate_array = [];
        foreach ($app_language as $language) {
            $validate_array['name_'.$language->code] = 'required|string|max:255';
        }
        $this->validate($request, $validate_array);

        // saving the data
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            $salaryType->translateOrNew(str_replace('name_', '', $key))->name = $value;
            $salaryType->save();
        }

        flashSuccess(__('salary_type_updated_successfully'));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($request)
    {
        $salaryType = SalaryType::find($request);
        if ($salaryType && count($salaryType->job)) {
            flashError(__('organization_type_has_companies'));

            return back();
        }

        $salaryType->delete();

        flashSuccess(__('salary_type_deleted_successfully'));

        return redirect()->route('salaryType.index');
    }

    /**
     * Bulk data Import.
     *
     * @return \Illuminate\Http\Response
     */
    // public function bulkImport(Request $request)
    // {
    //     $request->validate([
    //         'import_file' => 'required|mimes:csv,xlsx,xls',
    //     ]);

    //     try {
    //         Excel::import(new OrganizationTypeImport(), $request->import_file);

    //         flashSuccess(__('organization_type_imported_successfully'));

    //         return back();
    //     } catch (\Throwable $th) {
    //         flashError($th->getMessage());

    //         return back();
    //     }
    // }
}
