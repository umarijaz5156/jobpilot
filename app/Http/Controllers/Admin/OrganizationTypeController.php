<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\OrganizationTypeImport;
use App\Models\OrganizationType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Language\Entities\Language;

class OrganizationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $organizationTypes = OrganizationType::all();
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.organizationType.index', compact('organizationTypes', 'app_language'));
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
            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $this->validate($request, $validate_array);

            // saving the data
            $organization_type = new OrganizationType();
            $organization_type->save();

            foreach ($request->except('_token') as $key => $value) {
                $organization_type->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $organization_type->save();
            }

            flashSuccess(__('organization_type_created_successfully'));

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
    public function edit(OrganizationType $organizationType)
    {
        try {
            $organizationTypes = OrganizationType::all();
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.organizationType.index', compact('organizationType', 'organizationTypes', 'app_language'));
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
    public function update(Request $request, OrganizationType $organizationType)
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
                $organizationType->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $organizationType->save();
            }

            flashSuccess(__('organization_type_updated_successfully'));

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
    public function destroy(OrganizationType $organizationType)
    {
        try {
            if ($organizationType && count($organizationType->companies)) {
                flashError(__('organization_type_has_companies'));

                return back();
            }
            $organizationType->delete();

            flashSuccess(__('organization_type_deleted_successfully'));

            return redirect()->route('organizationType.index');

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Bulk data Import.
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:csv,xlsx,xls',
        ]);

        try {
            Excel::import(new OrganizationTypeImport(), $request->import_file);

            flashSuccess(__('organization_type_imported_successfully'));

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }
}
