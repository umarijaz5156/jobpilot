<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use Illuminate\Http\Request;
use Modules\Language\Entities\Language;

class JobTypeController extends Controller
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

            $job_types = JobType::with('translations')->paginate(10);

            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.job_type.index', compact('job_types', 'app_language'));
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
            abort_if(! userCan('job_types.create'), 403);

            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $this->validate($request, $validate_array);

            // saving the data
            $job_type = new JobType();
            $job_type->save();

            foreach ($request->except('_token') as $key => $value) {
                $job_type->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $job_type->save();
            }

            flashSuccess(__('job_type_created_successfully'));

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
    public function edit(JobType $jobType)
    {
        try {
            abort_if(! userCan('job_types.update'), 403);

            $job_type = $jobType;
            $job_types = JobType::with('translations')->paginate(10);
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.job_type.index', compact('job_type', 'job_types', 'app_language'));
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
    public function update(Request $request, JobType $jobType)
    {
        try {
            abort_if(! userCan('job_types.update'), 403);

            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $this->validate($request, $validate_array);

            // saving the data
            foreach ($request->except(['_token', '_method']) as $key => $value) {
                $jobType->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $jobType->save();
            }

            flashSuccess(__('job_type_updated_successfully'));

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
    public function destroy(JobType $jobType)
    {
        try {
            abort_if(! userCan('job_types.delete'), 403);

            // check if the job_type has candidates
            if ($jobType && $jobType->jobs->count()) {
                flashError(__('job_type_has_job'));

                return back();
            }

            $jobType->delete();

            flashSuccess(__('job_type_deleted_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
