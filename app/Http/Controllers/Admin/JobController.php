<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobFormRequest;
use App\Http\Traits\JobAble;
use App\Imports\JobsImport;
use App\Models\AppliedJob;
use App\Models\Benefit;
use App\Models\CandidateJobAlert;
use App\Models\Company;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobCategoryTranslation;
use App\Models\JobRole;
use App\Models\JobType;
use App\Models\SalaryType;
use App\Models\SearchCountry;
use App\Models\Skill;
use App\Models\State;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\JobApprovalNotification;
use App\Notifications\Website\Candidate\RelatedJobNotification;
use App\Services\Admin\Job\JobCreateService;
use App\Services\Admin\Job\JobListService;
use App\Services\Admin\Job\JobUpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Location\Entities\Country;

class JobController extends Controller
{
    use JobAble;

    public function __construct()
    {
        $this->middleware('access_limitation')->only(['destroy', 'clone']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            abort_if(!userCan('job.view'), 403);

            $jobs = (new JobListService())->execute($request);
            $job_categories = JobCategory::all()->sortBy('name');
            $experiences = Experience::all();
            $job_types = JobType::all();
            $companies = Company::with('user:id,name')->get(['id', 'user_id']);
            $edited_jobs = Job::edited()->count();
            $featured_jobs = Job::where('featured', 1)->get(['id', 'title']);
            $all_jobs = Job::get(['id', 'title']);

            return view('backend.Job.index', [
                'jobs' => $jobs,
                'job_categories' => $job_categories,
                'experiences' => $experiences,
                'job_types' => $job_types,
                'companies' => $companies,
                'edited_jobs' => $edited_jobs,
                'featured_jobs' => $featured_jobs,
                'all_jobs' => $all_jobs
            ]);
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            abort_if(!userCan('job.create'), 403);

            $data['countries'] = Country::all();
            $country = SearchCountry::where('name', 'Australia')->first();
            $data['states'] = State::where('country_id', $country->id)->get();
            $data['companies'] = Company::all();
            $data['job_category'] = JobCategory::all()->sortBy('name');
            $data['job_roles'] = JobRole::all()->sortBy('name');
            $data['experiences'] = Experience::all();
            $data['job_types'] = JobType::all();
            $data['salary_types'] = SalaryType::all();
            $data['educations'] = Education::all();
            $data['benefits'] = Benefit::whereNull('company_id')->get()->sortBy('name');
            $data['tags'] = Tag::all()->sortBy('name');
            $data['skills'] = Skill::all()->sortBy('name');

            return view('backend.Job.create', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    public function jobStatusChange(Job $job, Request $request)
    {
        try {
            abort_if(!userCan('job.update'), 403);

            $job->update([
                'status' => $request->status,
            ]);

            if ($request->status == 'active') {
                if ($job->company) {
                    Notification::send($job->company->user, new JobApprovalNotification($job));
                }

                $candidates = CandidateJobAlert::where('job_role_id', $job->role_id)->get();

                foreach ($candidates as $candidate) {
                    if ($candidate->candidate->received_job_alert) {
                        $candidate->candidate->user->notify(new RelatedJobNotification($job));
                    }
                }
            }

            flashSuccess(__('job_status_changed'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JobFormRequest $request)
    {
        try {
            abort_if(!userCan('job.create'), 403);
            (new JobCreateService())->execute($request);

            flashSuccess(__('job_created_successfully'));

            return redirect()->route('job.index');
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {
        try {
            abort_if(!userCan('job.view'), 403);

            return view('backend.Job.show', compact('job'));
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
        try {
            abort_if(!userCan('job.update'), 403);

            $data['companies'] = Company::all();
            $data['job_category'] = JobCategory::all()->sortBy('name');
            $data['job_roles'] = JobRole::all()->sortBy('name');
            $data['experiences'] = Experience::all();
            $data['job_types'] = JobType::all();
            $data['salary_types'] = SalaryType::all();
            $data['educations'] = Education::all();
            $data['benefits'] = Benefit::whereNull('company_id')->get()->sortBy('name');
            $data['tags'] = Tag::all()->sortBy('name');
            $job->load('tags', 'benefits', 'company');
            $data['job'] = $job;
            $data['lat'] = $job->lat ? floatval($job->lat) : floatval(setting('default_lat'));
            $data['long'] = $job->long ? floatval($job->long) : floatval(setting('default_long'));
            $data['skills'] = Skill::all()->sortBy('name');
            $country = SearchCountry::where('name', 'Australia')->first();
            $data['states'] = State::where('country_id', $country->id)->get();
            return view('backend.Job.edit', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(JobFormRequest $request, Job $job)
    {
        try {
            abort_if(!userCan('job.update'), 403);

            (new JobUpdateService())->execute($request, $job);

            flashSuccess(__('job_updated_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        try {
            abort_if(!userCan('job.delete'), 403);

            if ($job->delete()) {
                flashSuccess(__('job_deleted_successfully'));

                return back();
            } else {
                flashError(__('something_went_wrong'));

                return back();
            }
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->ids;
        Job::whereIn('id', $ids)->delete();
        flashSuccess(__('job_deleted_successfully'));

        return back();

        // Return response if needed
    }

    public function clone(Job $job)
    {
        try {
            $newJob = $job->replicate();
            $newJob->created_at = now();
            $newJob->slug = Str::slug($job->title) . '-' . time() . '-' . uniqid();
            $newJob->save();

            flashSuccess(__('job_cloned_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Edited Approval job list
     */
    public function editedJobList(Request $request)
    {
        try {
            abort_if(!userCan('job.view'), 403);

            $query = Job::latest()->edited();

            // keyword
            if ($request->title && $request->title != null) {
                $query->where('title', 'LIKE', "%$request->title%");
            }

            // status
            if ($request->status && $request->status != null) {
                if ($request->status != 'all') {
                    $query->where('status', $request->status);
                }
            }

            // job_category
            if ($request->job_category && $request->job_category != null) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('slug', $request->job_category);
                });
            }

            // experience
            if ($request->experience && $request->experience != null) {
                $query->whereHas('experience', function ($q) use ($request) {
                    $q->where('slug', $request->experience);
                });
            }

            // job_type
            if ($request->job_type && $request->job_type != null) {
                $query->whereHas('job_type', function ($q) use ($request) {
                    $q->where('slug', $request->job_type);
                });
            }

            // filter_by
            if ($request->filter_by && $request->filter_by != null) {
                $query->where('status', $request->filter_by);
            }

            $jobs = $query->with(['experience', 'job_type'])->paginate(15);
            $jobs->appends($request->all());

            $job_categories = JobCategory::all()->sortBy('name');
            $experiences = Experience::all();
            $job_types = JobType::all();

            return view('backend.Job.edited_jobs', compact('jobs', 'job_categories', 'experiences', 'job_types'));
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Show Edited job
     */
    public function editedShow(Job $job)
    {
        try {
            $parent_job = Job::FindOrFail($job->parent_job_id);

            return view('backend.Job.show_edited', compact('parent_job', 'job'));
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Show Edited job
     */
    public function editedApproved(Job $job)
    {
        try {
            $main_job = Job::FindOrFail($job->parent_job_id);

            $main_job->update([
                'title' => $job->title,
                'category_id' => $job->category_id,
                'role_id' => $job->role_id,
                'education_id' => $job->education_id,
                'experience_id' => $job->experience_id,
                'salary_mode' => $job->salary_mode,
                'custom_salary' => $job->custom_salary,
                'min_salary' => $job->min_salary,
                'max_salary' => $job->max_salary,
                'salary_type_id' => $job->salary_type_id,
                'deadline' => Carbon::parse($job->deadline)->format('Y-m-d'),
                'job_type_id' => $job->job_type_id,
                'vacancies' => $job->vacancies,
                'apply_on' => $job->apply_on,
                'apply_email' => $job->apply_email,
                'apply_url' => $job->apply_url,
                'description' => $job->description,
                'is_remote' => $job->is_remote,

                // map deatils
                'address' => $job->address,
                'neighborhood' => $job->neighborhood,
                'locality' => $job->locality,
                'place' => $job->place,
                'district' => $job->district,
                'postcode' => $job->postcode,
                'region' => $job->region,
                'country' => $job->country,
                'long' => $job->long,
                'lat' => $job->lat,
            ]);

            $job->delete();

            flashSuccess(__('job_changes_applied_successfully'));

            return redirect()->route('admin.job.edited.index');
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:csv,xlsx,xls',
            'company' => 'required|exists:companies,id',
        ]);

        try {
            Excel::import(new JobsImport($request->company), $request->import_file);

            flashSuccess('Jobs imported successfully');

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }

    public function appliedJobs()
    {
        $applied_jobs = AppliedJob::paginate(10);
        $companies = Company::with('user:id,name')->get(['id', 'user_id']);

        return view('backend.Job.applied_index', [
            'applied_jobs' => $applied_jobs,
            'companies' => $companies,
        ]);
    }

    public function appliedJobsShow(AppliedJob $applied_job)
    {
        return view('backend.Job.applied_job_show', [
            'applied_job' => $applied_job,
        ]);
    }


    public function featureJobs(){
        $featured_jobs = Job::where('featured', 1)->get(['id', 'title']);
        $all_jobs = Job::get(['id', 'title']);

        return view('backend.Job.feature', [
            'featured_jobs' => $featured_jobs,
            'all_jobs' => $all_jobs
        ]);
    }

    public function updateFeatured(Request $request)
    {
        // Validate the request
        $request->validate([
            'featured_jobs' => 'array'
        ]);

        Job::where('featured', 1)->update(['featured' => 0]);

        if ($request->has('featured_jobs')) {
            Job::whereIn('id', $request->featured_jobs)->update(['featured' => 1]);
        }

        return redirect()->back()->with('success', 'Featured jobs updated successfully!');
    }



    // upload old jobs

    public function fileUploadJobs()
    {

        $filePath = public_path('jobs_export.csv');

        // Open the file
        $file = fopen($filePath, 'r');

        // Read the header
        $header = fgetcsv($file);

        // Initialize an array to store the parsed data
        $dataArray = [];

        // Loop through the file and parse each row
        while ($row = fgetcsv($file)) {
            $data = array_combine($header, $row);
            $dataArray[] = $data;
        }

        fclose($file);


        $country = SearchCountry::where('name', 'Australia')->first();
        $states = State::where('country_id', $country->id)->get();
        $jobsCategory = JobCategoryTranslation::get();

        foreach ($dataArray as $data) {

         

                $company = User::where('name', $data['company'])->first();

                if (!$company) {
                    continue;
                }


                $state_id = null;
                $state_territory = $data['state_territory'];
                $location = $data['location'];
                $threshold = 50;
                foreach ($states as $state) {
                    $similarity = 0;
                    similar_text($state_territory, $state->name, $similarity);

                    if ($similarity >= $threshold) {
                        $state_id = $state->id;
                        break;
                    }
                }

                if (!$state_id) {
                    foreach ($states as $state) {
                        $similarity = 0;
                        similar_text($location, $state->name, $similarity);
                        if ($similarity >= $threshold) {
                            $state_id = $state->id;
                            break;
                        }
                    }
                }

                if (!$state_id) {
                    $state_id = '3909';
                }
                $region = State::where('id', $state_id)->first('name');


                $category_id = null;
                $threshold2 = 30;
                if (!$category_id) {
                    foreach ($jobsCategory as $category) {
                        $similarity = 0;
                        similar_text($data['title'], $category->name, $similarity);
                        if ($similarity >= $threshold2) {
                            $category_id = $category->job_category_id;
                            break;
                        }
                    }
                }
                if (!$category_id) {
                    $category_id = 1;
                }

                $highlight =  0;
                $featured = strtolower($data['featured']) == 'true' ? 1 : 0;

                $deadline = Carbon::parse($data['expiration_date'])->format('Y-m-d') ?? 'null';

                $apply_on = !empty($data['apply_url']) ? 'custom_url' : 'app';

                $experience_id = 4;
                $role_id = 2;
                $education_id = 5;
                $job_type_id = 1;
                $salary_type_id = 1;
                $salary_mode = 'custom';
                $custom_salary = 'Competitive';
                $vacancies = 1;
                $description = 'The job Title is' .  $data['title'] ?? 'Woker' . 'and the location is' . $data['location'] ?? 'Australia.';

                if($data['status'] == 'published'){
                    $status = 'active';
                }else if($data['status'] == 'draft'){
                    $status = 'pending';
                }else {
                    $status = 'expired';
                }

                $jobCreated = Job::create([
                    'title' => $data['title'],
                    'company_id' => $company->company->id,
                    'company_name' => $company->name,
                    'state_id' => $state_id,
                    'category_id' => $category_id,
                    'role_id' => $role_id,
                    'salary_mode' => $salary_mode,
                    'custom_salary' => $custom_salary,
                    'min_salary' => null,
                    'max_salary' => null,
                    'salary_type_id' => $salary_type_id,
                    'deadline' => $deadline,
                    'education_id' => $education_id,
                    'experience_id' => $experience_id,
                    'job_type_id' => $job_type_id,
                    'vacancies' => $vacancies,
                    'apply_on' => $apply_on,
                    'apply_email' => $data['apply_email'] ?? null,
                    'apply_url' => $data['apply_url'] ?? null,
                    'description' => $description,
                    'featured' => $featured,
                    'highlight' => $highlight,
                    'is_remote' => strtolower($data['remote']) == 'remote' ? 1 : 0,
                    'country' => 'Australia',
                    'region' => $region->name,
                    'exact_location' => $data['location'] ?? '',
                    'address' => $data['location'] ?? '',
                    'status' => $status,
                    'old_id' =>  $data['id']
                ]);


                // Location insert
                updateMap($jobCreated);
            
        }

        dd('done all');
    }


}
