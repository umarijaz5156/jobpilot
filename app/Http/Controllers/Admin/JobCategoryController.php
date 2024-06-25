<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\JobsCategoryImport;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Language\Entities\Language;

class JobCategoryController extends Controller
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
            abort_if(! userCan('job_category.view'), 403);

            $jobCategories = JobCategory::latest()->paginate(20);
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.JobCategory.index', compact('jobCategories', 'app_language'));
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
            abort_if(! userCan('job_category.create'), 403);

            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $validate_array['image'] = 'nullable|image|max:1024';
            $validate_array['icon'] = 'required';
            $this->validate($request, $validate_array);

            // make slug user input title wise
            $userTitle = $request->name_en;
            $makeSlug = Str::slug($userTitle);

            // saving the data
            $category = new JobCategory();
            $category->icon = $request->icon;
            $category->slug = $makeSlug;

            // if ($request->hasFile('image')) {
            //     $image = uploadFileToPublic($request->image, 'images/jobCategory');
            //     $category->image = $image;
            // }

            if ($request->hasFile('image')) {
                $path = 'uploads/images/job-category';
                $image = uploadImage($request->image, $path, [50, 50]);

                $category->image = $image;
            }

            $category->save();

            foreach ($request->except(['_token', 'icon', 'image']) as $key => $value) {
                $category->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $category->save();
            }

            flashSuccess(__('category_created_successfully'));

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
    public function edit(JobCategory $jobCategory)
    {
        try {
            abort_if(! userCan('job_category.update'), 403);

            $jobCategories = JobCategory::latest()->paginate(20);
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.JobCategory.index', compact('jobCategory', 'jobCategories', 'app_language'));
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
    public function update(Request $request, JobCategory $jobCategory)
    {
        try {
            abort_if(! userCan('job_category.update'), 403);

            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $validate_array['image'] = 'nullable|image|max:1024';
            $validate_array['icon'] = 'required';
            $this->validate($request, $validate_array);

            // saving the data
            // if ($request->hasFile('image')) {
            //     $image = uploadFileToPublic($request->image, 'images/jobCategory');
            //     $jobCategory->image = $image;
            // }
            if ($request->hasFile('image')) {
                $path = 'uploads/images/job-category';
                $image = uploadImage($request->image, $path, [50, 50]);

                $jobCategory->image = $image;
            }

            $jobCategory->icon = $request->icon;
            $jobCategory->save();

            foreach ($request->except(['_token', 'icon', 'image', '_method']) as $key => $value) {
                $jobCategory->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $jobCategory->save();
            }

            flashSuccess(__('category_updated_successfully'));

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
    public function destroy(JobCategory $jobCategory)
    {
        try {
            abort_if(! userCan('job_category.delete'), 403);

            if ($jobCategory && $jobCategory->jobs->count()) {
                flashError(__('job_category_has_jobs'));

                return back();
            }

            deleteFile($jobCategory->image);
            $jobCategory->delete();

            flashSuccess(__('category_deleted_successfully'));

            return back();
        } catch (\Exception $e) {

            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }

    /**
     * Bulk data Import
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:csv,xlsx,xls',
        ]);

        try {
            Excel::import(new JobsCategoryImport(), $request->import_file);

            flashSuccess(__('job_category_imported_successfully'));

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }
}
