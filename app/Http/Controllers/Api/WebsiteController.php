<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Cms;
use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use App\Models\Candidate;
use App\Models\CmsContent;
use Illuminate\Http\Request;
use F9Web\ApiResponseHelpers;
use App\Http\Traits\JobableApi;
use Modules\Blog\Entities\Post;
use Modules\Plan\Entities\Plan;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\CandidateAble;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Faq\Entities\FaqCategory;
use Modules\Location\Entities\Country;
use Modules\Language\Entities\Language;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Job\JobListResource;
use App\Services\API\Website\JobApiService;
use App\Services\API\Website\ContactService;
use App\Services\API\Website\JobPageService;
use App\Http\Resources\Job\JobDetailResource;
use App\Http\Traits\ResetCvViewsHistoryTrait;
use App\Services\API\Website\HomePageService;
use Modules\Testimonial\Entities\Testimonial;
use App\Services\API\Website\CompanyListService;
use App\Services\API\Website\Post\CommentService;
use App\Services\API\Website\CandidateListService;
use App\Services\API\Website\CompanyDetailsService;
use App\Services\API\Website\Post\FetchPostService;
use App\Services\API\Website\EmployerDetailsService;
use App\Services\API\Website\Post\FetchPostDetailsService;

class WebsiteController extends Controller
{
    use ApiResponseHelpers, ResetCvViewsHistoryTrait, JobableApi, CandidateAble;

    public function home(): JsonResponse
    {
        $data = (new HomePageService)->execute();

        return $this->respondWithSuccess([
            'data' => $data,
        ]);
    }

    public function about(): JsonResponse
    {
        $testimonials = Testimonial::all();
        $states = [
            'live_jobs' => livejob(),
            'companies' => Company::count(),
            'candidates' => Candidate::count(),
        ];

        return $this->respondWithSuccess([
            'data' => [
                'testimonials' => $testimonials,
                'states' => $states,
            ],
        ]);
    }

    public function candidates(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new CandidateListService)->execute($request),
        ]);
    }

    public function companies(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new CompanyListService)->execute($request),
        ]);
    }

    public function companyDetails($username)
    {
        return $this->respondWithSuccess([
            'data' => (new CompanyDetailsService)->execute($username),
        ]);
    }

    public function candidateDetails($username)
    {
        $candidate = User::where('username', $username)
            ->with('candidate', 'contactInfo', 'socialInfo')
            ->firstOrFail();

        $candidate = User::where('username', $username)
            ->with(['contactInfo', 'socialInfo', 'candidate' => function ($query) {
                $query->with('experiences', 'educations', 'experience', 'education', 'profession','languages:id,name', 'skills');
            }])
            ->firstOrFail();

        $candidate->candidate->birth_date = Carbon::parse($candidate->candidate->birth_date)->format('d F, Y');

        $languages = $candidate->candidate->languages()->pluck('name')->toArray();
        $candidate_languages = $languages ? implode(", ", $languages) : '';

        $skills = $candidate->candidate->skills->pluck('name');
        $candidate_skills = $skills ? implode(", ", json_decode(json_encode($skills))) : '';

        return $this->respondWithSuccess([
            'data' => [
                'candidate' => $candidate,
                'skills' => $candidate_skills,
                'languages' => $candidate_languages,
            ],
        ]);
    }

    public function jobs(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new JobApiService)->execute($request),
        ]);
    }


    public function posts(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new FetchPostService)->execute($request),
        ]);
    }

    public function postDetails($slug)
    {
        return $this->respondWithSuccess([
            'data' => (new FetchPostDetailsService)->execute($slug),
        ]);
    }

    // get jobs If candidate is logged in
    public function ifLoggedinCadidateJobs(Request $request)
    {
      return $this->respondWithSuccess([
        'data' => (new JobPageService)->execute($request),
      ]);
    }

    // get jobs If candidate is logged in
    public function ifLoggedinCadidateJobDetails(Job $job)
    {
        if ($job->status == 'pending') {
            if (!auth('admin')->check()) {
                if(!auth()->check()){
                    $this->respondNotFound('Job not found');
                }

                if (auth()->user()->role != 'company') {
                    $this->respondNotFound('Job not found');
                }

                if (auth()->user()->company->id != $job->company_id) {
                    $this->respondNotFound('Job not found');
                }
            }
        }

        $data = $this->getJobDetails($job);

        $data['job'] = new JobDetailResource($data['job']);
        $data['related_jobs'] = JobListResource::collection($data['related_jobs']);

        return $this->respondWithSuccess([
            'data' => $data,
        ]);
    }


    public function jobDetails(Job $job)
    {
        if ($job->status == 'pending') {
            if (!auth('admin')->check()) {
                if(!auth()->check()){
                    $this->respondNotFound('Job not found');
                }

                if (auth()->user()?->role != 'company') {
                    $this->respondNotFound('Job not found');
                }

                if (auth()->user()?->company?->id != $job->company_id) {
                    $this->respondNotFound('Job not found');
                }
            }
        }

        $data = $this->getJobDetails($job);

        $data['job'] = new JobDetailResource($data['job']);
        $data['related_jobs'] = JobListResource::collection($data['related_jobs']);

        return $this->respondWithSuccess([
            'data' => $data,
        ]);
    }

    public function pricing(): JsonResponse
    {
        $data = Plan::active()->get();

        return $this->respondWithSuccess([
            'data' => $data,
        ]);
    }

    public function faq(): JsonResponse
    {
        $data = FaqCategory::with(['faqs' => function ($q) {
            $q->where('code', currentLangCode());
        }])->get();

        return $this->respondWithSuccess([
            'data' => $data,
        ]);
    }

    public function privacyPolicy(): JsonResponse
    {
        $privacy_page_default = Cms::select('privary_page')->first();
        $cms_content = CmsContent::query();

        $privacy_page = null;

        //check session current language
        $current_language = currentLanguage() ? currentLanguage() : '';

        //if has session current language
        if ($current_language) {

            $exist_cms_content =  $cms_content->where('translation_code', $current_language->code)->where('page_slug', 'privacy_page')->first();

            if ($exist_cms_content) {
                $privacy_page = $exist_cms_content->text;
            }
        } else { //else push default one

            $exist_cms_content_en =  $cms_content->where('translation_code', 'en')->where('page_slug', 'privacy_page')->first();

            if ($exist_cms_content_en) {

                $privacy_page = $exist_cms_content_en->text;
            } else {

                $privacy_page = $privacy_page_default->privary_page;
            }
        }

        return $this->respondWithSuccess([
            'data' => [
                'content' => $privacy_page,
                'default_content' => $privacy_page_default->privary_page
            ]
        ]);
    }

    public function termsCondition(): JsonResponse
    {
        $terms_condition = Cms::select('terms_page')->first();
        $cms_content = CmsContent::query();

        $terms_page = null;

        //check session current language
        $current_language = currentLanguage() ? currentLanguage() : '';
        if ($current_language) {

            $exist_cms_content =  $cms_content->where('translation_code', $current_language->code)->where('page_slug', 'terms_condition_page')->first();

            if ($exist_cms_content) {
                $terms_page = $exist_cms_content->text;
            }
        } else { //else push default one

            $exist_cms_content_en =  $cms_content->where('translation_code', 'en')->where('page_slug', 'terms_condition_page')->first();

            if ($exist_cms_content_en) {

                $terms_page = $exist_cms_content_en->text;
            } else {

                $terms_page = $terms_condition->terms_page;
            }
        }

        return $this->respondWithSuccess([
            'data' => [
                'content' => $terms_page,
                'default_content' => $terms_condition->terms_page
            ]
        ]);
    }

    public function refundPolicy(): JsonResponse
    {
        $page_name = "refund_page";
        $page_default = Cms::select($page_name)->first();
        $cms_content = CmsContent::query();

        $page = null;

        //check session current language
        $current_language = currentLanguage() ? currentLanguage() : '';

        //if has session current language
        if ($current_language) {

            $exist_cms_content =  $cms_content->where('translation_code', $current_language->code)->where('page_slug', $page_name)->first();

            if ($exist_cms_content) {
                $page = $exist_cms_content->text;
            }
        } else { //else push default one

            $exist_cms_content_en =  $cms_content->where('translation_code', 'en')->where('page_slug', $page_name)->first();

            if ($exist_cms_content_en) {

                $page = $exist_cms_content_en->text;
            } else {

                $page = $page_default->$page_name;
            }
        }

        return $this->respondWithSuccess([
            'data' => [
                'content' => $page,
                'default_content' => $page_default->$page_name,
            ]
        ]);
    }

    public function contact(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new ContactService)->execute($request),
        ]);

    }

    public function comment(Post $post, Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new CommentService)->execute($post, $request),
        ]);
    }

    public function pageList(){
        $pages = [
            'blog' => route('website.posts'),
            'about' => route('website.about'),
            'contact' => route('website.contact'),
            'faq' => route('website.faq'),
            'privacy_policy' => route('website.privacyPolicy'),
            'term_condition' => route('website.termsCondition'),
            'refund_policy' => route('website.refundPolicy'),
        ];

        return $this->respondWithSuccess([
            'data' => $pages,
        ]);
    }

    public function countries()
    {
        return $countries = Country::all();

        return $this->respondWithSuccess([
            'data' => $countries,
        ]);
    }

    public function getAllNotification(Request $request)
    {
        $user = auth('sanctum')->user();

        if($user)
        {
            $notifications = $user->notifications()->paginate(10);

            return $this->respondWithSuccess([
                'data' => $notifications,
            ]);
        }
        else{
            return $this->respondUnAuthenticated('Unauthenticated User');
        }

    }
}
