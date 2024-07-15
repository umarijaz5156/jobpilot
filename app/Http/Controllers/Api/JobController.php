<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JobController extends Controller
{
    
    public function storeViaApi(Request $request)
    {
       
        $companyData = User::where('email',$request->company_email)->first();
        $company_id = $companyData->company->id;
        $company_name = $companyData->name;

        // Create the job
        $job = Job::create([
            'title' => $request->title,
            'company_id' => $company_id,
            'company_name' => $company_name,
            'category_id' => $request->category_id,
            'state_id' => $request->state_id,
            'role_id' => $request->role_id,
            'salary_mode' => $request->salary_mode,
            'custom_salary' => $request->custom_salary,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'salary_type_id' => $request->salary_type,
            'deadline' => Carbon::parse($request->deadline)->format('Y-m-d'),
            'education_id' => $request->education,
            'experience_id' => $request->experience,
            'job_type_id' => $request->job_type,
            'vacancies' => $request->vacancies,
            'apply_on' => $request->apply_on,
            'apply_email' => $request->apply_email ?? null,
            'apply_url' => $request->apply_url ?? null,
            'description' => $request->description,
            'featured' => $request->featured,
            'highlight' => $request->highlight,
            'featured_until' => $request->featured_until,
            'highlight_until' => $request->highlight_until,
            'is_remote' => $request->is_remote ?? 0,
            'status' => 'active',

        ]);

        $job->selectedCategories()->sync($request->categories);


        return response()->json(['success' => true, 'job' => $job], 201);
    }
}
