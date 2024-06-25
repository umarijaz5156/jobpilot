<?php

namespace App\Http\Controllers\Api;

use App\Models\Job;
use App\Http\Traits\Jobable;
use Illuminate\Http\Request;
use F9Web\ApiResponseHelpers;
use App\Models\ApplicationGroup;
use App\Http\Controllers\Controller;
use App\Http\Traits\CompanyJobTrait;
use App\Models\CandidateResume;
use App\Models\CompanyBookmarkCategory;
use App\Models\Earning;
use App\Models\UserPlan;
use App\Services\API\Website\CompanyAccountProgress;
use App\Services\API\Website\Company\PostingJob\CloneJobService;
use App\Services\API\Website\Company\PostingJob\StoreJobService;
use App\Services\API\Website\Company\PostingJob\UpdateJobService;
use App\Services\API\Website\Company\PostingJob\PromoteJobService;
use App\Services\API\Website\Company\Bookmark\CandidateBookmarkService;
use App\Services\API\Website\Company\PostingJob\JobStatusUpdateService;
use App\Services\API\Website\Company\PostingJob\FetchEditJobDataService;
use App\Services\API\Website\Company\PostingJob\FetchPostJobDataService;
use App\Services\API\Website\Company\Bookmark\CreateBookmarkCategoryService;
use App\Services\API\Website\Company\Bookmark\FetchCandidateBookmarkService;
use App\Services\API\Website\Company\Bookmark\UpdateBookmarkCategoryService;
use App\Services\API\Website\Company\ApplicationGroup\CreateApplicationGroupService;
use App\Services\API\Website\Company\ApplicationGroup\DeleteApplicationGroupService;
use App\Services\API\Website\Company\ApplicationGroup\UpdateApplicationGroupService;
use App\Services\API\Website\PaymentService;
use PDF;

class CompanyController extends Controller
{
    use CompanyJobTrait, Jobable, ApiResponseHelpers;

    public function dashboard()
    {
        // $data['userplan'] = UserPlan::with('plan')->apiCompanyData()->firstOrFail();
        $data['openJobCount'] = auth('sanctum')->user()->company->jobs()->active()->count();

        // Recent 4 Jobs
        $data['recentJobs'] = auth('sanctum')->user()->company->jobs()
        ->select(['id', 'title', 'company_id', 'country', 'max_salary', 'min_salary', 'job_type_id', 'slug', 'deadline', 'status'])
        ->latest()->with('company:id', 'job_type:id,name')->withCount('appliedJobs')->take(4)->get();
        $data['savedCandidateCount'] = auth('sanctum')->user()->company->bookmarkCandidates()->count();

        return $this->respondWithSuccess([
            'data' => $data,
        ]);
    }

    public function createJob()
    {
        return $this->respondWithSuccess([
            'data' => (new FetchPostJobDataService)->execute()
        ]);
    }

    public function storeJob(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new StoreJobService)->execute($request)
        ]);
    }

    public function editJob(Job $job)
    {
        return $this->respondWithSuccess([
            'data' => (new FetchEditJobDataService)->execute($job)
        ]);
    }

    public function updateJob(Request $request, Job $job)
    {
        return $this->respondWithSuccess([
            'data' => (new UpdateJobService)->execute($request, $job)
        ]);
    }

    public function promoteJob(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new PromoteJobService)->execute($request)
        ]);
    }

    public function cloneJob(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new CloneJobService)->execute($request)
        ]);
    }

    public function changeJobStatus(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new JobStatusUpdateService)->execute($request)
        ]);
    }

    public function fetchBookmarkCategories()
    {
        return $this->respondWithSuccess([
            'data' => CompanyBookmarkCategory::where('company_id', auth('sanctum')->user()->company->id)->get()
        ]);
    }

    public function storeBookmarkCategories(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new CreateBookmarkCategoryService)->execute($request)
        ]);
    }

    public function editBookmarkCategories(CompanyBookmarkCategory $category)
    {
        return $this->respondWithSuccess([
            'data' => $category
        ]);
    }

    public function updateBookmarkCategories(Request $request, CompanyBookmarkCategory $category)
    {
        return $this->respondWithSuccess([
            'data' => (new UpdateBookmarkCategoryService())->execute($request, $category)
        ]);
    }

    public function deleteBookmarkCategories(CompanyBookmarkCategory $category)
    {
        $category->delete();

        return $this->respondOk( __('category_deleted_successfully'));
    }

    public function fetchBookmarkCandidates(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new FetchCandidateBookmarkService)->execute($request)
        ]);
    }

    public function bookmarkCandidate(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new CandidateBookmarkService())->execute($request)
        ]);
    }

    public function fetchApplicationGroup(Request $request)
    {
        $groups = $request->user()->company->applicationGroups()->get();

        return $this->respondWithSuccess([
            'data' => $groups
        ]);
    }

    public function storeApplicationGroup(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new CreateApplicationGroupService)->execute($request)
        ]);
    }

    public function updateApplicationGroup(Request $request, ApplicationGroup $group)
    {
        return $this->respondWithSuccess([
            'data' => (new UpdateApplicationGroupService)->execute($request, $group)
        ]);
    }

    public function deleteApplicationGroup(ApplicationGroup $group)
    {
        return $this->respondWithSuccess([
            'data' => (new DeleteApplicationGroupService)->execute($group)
        ]);
    }

    public function fetchAccountProgress()
    {
        return $this->respondWithSuccess([
            'data' => (new CompanyAccountProgress)->fetchAccountProgressData()
        ]);
    }

    public function submitAccountProgress(Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new CompanyAccountProgress)->submitAccountProgressData($request)
        ]);
    }

    public function plan()
    {
        $userPlan = UserPlan::with('plan')->apiCompanyData()->firstOrFail();
        $transactions = Earning::with('plan:id,label', 'manualPayment:id,name')->apiCompanyData()->latest()->paginate(8);

        return $this->respondWithSuccess([
            'data' => [
                'user_plan' => $userPlan,
                'transactions' => $transactions,
            ]
        ]);
    }

    public function planLimit()
    {
        $userPlan = UserPlan::with('plan')->firstOrFail();
        return $this->respondWithSuccess([
            'data' => $userPlan,
        ]);
    }

    public function payment($label, Request $request)
    {
        return $this->respondWithSuccess([
            'data' => (new PaymentService)->execute($label, $request)
        ]);
    }


    public function downloadInvoice($id)
    {
        $transaction = Earning::findOrFail($id);
        $data['transaction'] = $transaction->load('plan', 'company.user.contactInfo');
        $data['logo'] = setting()->dark_logo_url ?? asset('frontend/assets/images/logo/logo.png');

        $pdf = PDF::loadView('website.pages.company.invoice', $data)->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download("invoice_" . $transaction->order_id . ".pdf");
    }

    public function getSocialLinks(Request $request){

        $user = auth('sanctum')->user();

        if($user && $user->role == 'company')
        {
            return $this->respondWithSuccess([
                'data' => [
                    'social_media' => $user->socialInfo->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'social_media' => $item->social_media,
                            'url' => $item->url,
                        ];
                    }),
                ],
            ]);
        }
        else 
        {
            return $this->respondUnAuthenticated('Unauthenticated User');

        }

        
    }
}
