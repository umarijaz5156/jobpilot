<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Job\JobListResource;
use App\Http\Traits\JobableApi;
use App\Models\CandidateResume;
use App\Models\Job;
use App\Notifications\Website\Candidate\ApplyJobNotification;
use App\Notifications\Website\Candidate\BookmarkJobNotification;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class CandidateJobsController extends Controller
{
    use ApiResponseHelpers, JobableApi;

    // get all applied jobs of candidate
    public function appliedjobs(Request $request)
    {
        $candidate = Candidate::where('user_id', auth('sanctum')->id())->first();
        if (empty($candidate)) {

            $candidate = new Candidate();
            $candidate->user_id = auth('sanctum')->id();
            $candidate->save();
        }

        $paginate = $request->has('paginate') ? $request->paginate : 12;

        $appliedJobs = $candidate->appliedJobs()->withCount('allAppliedJobs')->paginate($paginate)->withQueryString()->through(function($data){
            $salary = $data->salary_mode == 'range' ? currencyAmountShort($data->min_salary) . ' - ' . currencyAmountShort($data->max_salary) . ' ' . currentCurrencyCode() : $data->custom_salary;

            return [
                'title' => $data->title,
                'slug' => $data->slug,
                'job_details' => route('website.job.details', $data->slug),
                'company_name' => $data->company && $data->company->user ? $data->company->user->name : '',
                'company_logo' => $data->company->logo_url,
                'job_type' => $data->job_type->name,
                'job_role' => $data->role->name,
                'country' => $data->country,
                'deadline' => $data->deadline,
                'salary' => $salary,
                'salary_mode' => $data->salary_mode,
                'min_salary' => $data->min_salary,
                'max_salary' => $data->max_salary,
                'is_featured' => $data->featured,
                'is_highlighted' => $data->highlight,
                'is_remote' => $data->is_remote,
                'status' => $data->status,
                'applied_human_time' => $data->pivot->created_at->diffForHumans(),
                'applied_at' => $data->pivot->created_at->format('d M Y h:i A'),
            ];
        });

        return $this->respondWithSuccess([
            'data' => $appliedJobs,
        ]);
    }

    // get all favorite or bookmarked jobs of candidate
    public function favoritejobs()
    {

        $candidate = Candidate::where('user_id', auth('sanctum')->id())->first();
        if (empty($candidate)) {

            $candidate = new Candidate();
            $candidate->user_id = auth('sanctum')->id();
            $candidate->save();
        }

        $appliedJobs = $candidate->bookmarkJobs()->paginate(8);
        return $this->respondWithSuccess([
            // 'data' => $appliedJobs,
            'data' => JobListResource::collection($appliedJobs)->response()->getData(),
        ]);

    }

    // save or remove favorite or bookmarked jobs of candidate
    public function bookmarkedJob(Job $job)
    {
        $check = $job->bookmarkJobs()->toggle(auth('sanctum')->user()->candidate->id);

        if ($check['attached']) {

            $user = auth('sanctum')->user();
            // make notification to company candidate bookmark job
            Notification::send($job->company->user, new BookmarkJobNotification($user, $job));
            // make notification to candidate for notify
            if (auth('sanctum')->user()->recent_activities_alert) {
                Notification::send(auth('sanctum')->user(), new BookmarkJobNotification($user, $job));
            }
        }
        $check['attached'] ? $message = 'Job added to favorite list' : $message = 'Job removed from favorite list';

        return $this->respondWithSuccess([
            'data' => [
                'message' => $message,
                'status' => $check['attached'] ? true : false,
            ]
        ]);

    }

    public function jobApply(Request $request)
    {
        $request->validate([
            'resume_id' => 'required',
            'cover_letter' => 'required|max:2000',
        ], [
            'resume_id.required' => 'Please select resume',
            'cover_letter.required' => 'Please enter cover letter',
        ]);

        // if (auth('sanctum')->user()->candidate->profile_complete != 0) {
        //     return response()->json(
        //         ['message' => __('complete_your_profile_before_applying_to_jobs_add_your_information_resume_and_profile_picture_for_a_better_chance_of_getting_hired')], 500
        //     );
        // }

        if (!CandidateResume::where('id', $request->resume_id)->where('candidate_id', auth('sanctum')->user()->candidate->id)->exists()) {
            return $this->respondError('You can not apply on this job. Because this resume is not yours');
        }

        $candidate = auth('sanctum')->user()->candidate;
        $job = Job::find($request->job_id);

        if ($job->apply_on != 'app') {
            return $this->respondError('You can not apply on this job. Because this job is not for apply on website');
        }

        DB::table('applied_jobs')->insert([
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
            'cover_letter' => $request->cover_letter,
            'candidate_resume_id' => $request->resume_id,
            'application_group_id' => $job->company->applicationGroups->where('is_deleteable', false)->first()->id ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // make notification to candidate and company for notify
        $job->company->user->notify(new ApplyJobNotification(auth('sanctum')->user(), $job->company->user, $job));

        if (auth('sanctum')->user()->recent_activities_alert) {
            auth('sanctum')->user()->notify(new ApplyJobNotification(auth('sanctum')->user(), $job->company->user, $job));
        }

        return $this->respondWithSuccess([
            'data' => [
                'message' => "Your job application submitted successfully!",
                'status' => true,
            ]
        ]);
    }

    public function jobAlerts(Request $request)
    {
        $paginate = $request->has('paginate') ? $request->paginate : 8;
        $notifications = auth('sanctum')->user()->notifications()->where('type', 'App\Notifications\Website\Candidate\RelatedJobNotification')->paginate($paginate);
        $notifications->getCollection()->transform(function ($item) {
            // Your code here
            if (isset($item->data['job_id'])) {
                $job = Job::with(['job_type', 'company'])->find($item->data['job_id']);
                if ($job) {
                    $salary = $job->salary_mode == 'range' ? currencyAmountShort($job->min_salary) . ' - '
                    . currencyAmountShort($job->max_salary) . ' ' . currentCurrencyCode() : $job->custom_salary;
                    return [
                        'title' => $job->title,
                        'slug' => $job->slug,
                        'job_type' => $job->job_type?->name,
                        'salary' => $salary,
                        'deadline' => $job->deadline,
                        'country' => $job->country,
                        'company_name' => $job->company && $job->company->user ? $job->company->user->name : '',
                        'company_logo' => $job->company->logo_url,
                    ];
                }
            }
        });

        return $this->respondWithSuccess([
            'data' => [
                'notifications' => $notifications,
                'message' => "Job alert retried successful!",
            ],
        ]);
    }
}
