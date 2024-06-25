<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobCreateRequest;
use App\Http\Traits\HasCompanyApplication;
use App\Http\Traits\JobAble;
use App\Models\Benefit;
use App\Models\Candidate;
use App\Models\cms;
use App\Models\CompanyBookmarkCategory;
use App\Models\CompanyQuestion;
use App\Models\Earning;
use App\Models\Education;
use App\Models\Experience;
use App\Models\IndustryType;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobRole;
use App\Models\JobType;
use App\Models\ManualPayment;
use App\Models\OrganizationType;
use App\Models\PaymentSetting;
use App\Models\SalaryType;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\TeamSize;
use App\Models\User;
use App\Models\UserPlan;
use App\Notifications\Website\Company\CandidateBookmarkNotification;
use App\Services\Midtrans\CreateSnapTokenService;
use App\Services\Website\Company\CompanyAccountProgressService;
use App\Services\Website\Company\CompanyPromoteJobService;
use App\Services\Website\Company\CompanySettingUpdateService;
use App\Services\Website\Company\CompanyStoreService;
use App\Services\Website\Company\CompanyUpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Modules\Currency\Entities\Currency;
use Modules\Location\Entities\Country;
use PDF;

class CompanyController extends Controller
{
    use HasCompanyApplication, JobAble;

    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'settingUpdateInformation',
        ]);
    }

    /**
     * Company Dashboard
     *
     * @return Response
     */
    public function dashboard()
    {
        try {
            $data['userplan'] = UserPlan::with('plan')
                ->companyData()
                ->firstOrFail();
            $data['openJobCount'] = auth()
                ->user()
                ->company->jobs()
                ->active()
                ->count();
            $data['pendingJobCount'] = auth()
                ->user()
                ->company->jobs()
                ->pending()
                ->count();

            // Recent 4 Jobs
            $data['recentJobs'] = auth()
                ->user()
                ->company->jobs()
                ->latest()
                ->take(4)
                ->with('company.user', 'job_type')
                ->withCount('appliedJobs')
                ->get();
            $data['savedCandidates'] = auth()
                ->user()
                ->company->bookmarkCandidates()
                ->count();

            return view('frontend.pages.company.dashboard', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company my jobs
     *
     * @return Response
     */
    public function myjobs(Request $request)
    {
        try {
            $query = currentCompany()
                ->jobs()
                ->withCount('appliedJobs')
                ->withoutEdited();

            // status search
            if ($request->has('status') && $request->status != null) {
                $query->where('status', $request->status);
            }

            // status search
            if ($request->has('apply_on') && $request->apply_on != null) {
                $query->where('apply_on', $request->apply_on);
            }

            $myJobs = $query
                ->with('job_type:id')
                ->latest()
                ->paginate(12)
                ->withQueryString();

            foreach ($myJobs as $job) {
                if ($job->days_remaining < 1) {
                    $job->update([
                        'status' => 'expired',
                        'deadline' => null,
                    ]);
                }
            }

            return view('frontend.pages.company.myjobs', compact('myJobs'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company Edited Pending job list
     *
     * @Return response
     */
    public function pendingEditedJobs()
    {
        try {
            if (setting('edited_job_auto_approved')) {
                abort(404);
            }

            $query = currentCompany()
                ->jobs()
                ->withCount('appliedJobs')
                ->edited();

            $myJobs = $query
                ->with('job_type:id')
                ->paginate(12)
                ->withQueryString();

            foreach ($myJobs as $job) {
                if ($job->days_remaining < 1) {
                    $job->update([
                        'status' => 'expired',
                        'deadline' => null,
                    ]);
                }
            }

            return view('frontend.pages.company.edited-jobs', compact('myJobs'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company all notifications
     *
     * @return Response
     */
    public function allNotification()
    {
        try {
            $notifications = auth()
                ->user()
                ->notifications()
                ->paginate(20);

            return view('frontend.pages.company.all-notifications', compact('notifications'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company payperjob
     *
     * @return Response
     */
    public function payPerJob()
    {
        try {
            if (! setting('per_job_active')) {
                abort(404);
            }

            $data['jobCategories'] = JobCategory::all()->sortBy('name');
            $data['roles'] = JobRole::all()->sortBy('name');
            $data['experiences'] = Experience::all();
            $data['educations'] = Education::all();
            $data['job_types'] = JobType::all();
            $data['salary_types'] = SalaryType::all();
            $data['tags'] = Tag::all()->sortBy('name');
            $data['setting'] = loadSetting();
            $all_benefits = Benefit::all()->sortBy('name');
            $data['questions'] = currentCompany()
                ->questions()
                ->where('reuse', true)
                ->get();
            $non_company_benefits = $all_benefits->whereNull('company_id');
            $company_benefits = $all_benefits->where('company_id', currentCompany()->id);
            $data['benefits'] = $non_company_benefits->merge($company_benefits);

            return view('frontend.pages.company.pay-per-job', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company payperjob store
     *
     * @return Response
     */
    public function storePayPerJob(JobCreateRequest $request)
    {
        try {

            $location = session()->get('location');
            if (! $location) {
                $request->validate([
                    'location' => 'required',
                ]);
            }

            if ($request->apply_on === 'custom_url') {
                $request->validate([
                    'apply_url' => 'required|url',
                ]);
            }
            if ($request->apply_on === 'email') {
                $request->validate([
                    'apply_email' => 'required|email',
                ]);
            }

            session(['job_total_amount' => $request->total_price_perjob]);
            session(['job_request' => $request->all()]);

            return redirect()->route('company.payperjob.payment');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company payperjob payment
     *
     * @return Response
     */
    public function payPerJobPayment()
    {
        try {
            abort_if(auth('user')->check() && authUser()->role == 'candidate', 404);

            // session data storing
            $job_total_amount = session('job_total_amount') ?? 100;
            session(['job_payment_type' => 'per_job']);

            if ($job_total_amount < 1) {
                session(['payperjob_code' => uniqid()]);

                return to_route('purchase.zero.pricing.job', session('payperjob_code'));
            }

            session(['stripe_amount' => currencyConversion($job_total_amount) * 100]);
            session(['razor_amount' => currencyConversion($job_total_amount, 'INR', 1) * 100]);
            session(['ssl_amount' => currencyConversion($job_total_amount, 'BDT', 1)]);

            $payment_setting = PaymentSetting::first();
            $manual_payments = ManualPayment::whereStatus(1)->get();

            // midtrans snap token
            if (config('templatecookie.midtrans_active') && config('templatecookie.midtrans_merchat_id') && config('templatecookie.midtrans_client_key') && config('templatecookie.midtrans_server_key')) {
                $usd = $job_total_amount;
                $checkCurrency = Currency::where('code', 'IDR')->first();
                if ($usd && $checkCurrency) {
                    $fromRate = Currency::whereCode(config('templatecookie.currency'))->first()->rate;
                    $toRate = $checkCurrency->rate;
                    $rate = $fromRate / $toRate;
                    $amount = round($usd / $rate, 2);
                }

                $order['order_no'] = uniqid();
                $order['total_price'] = $amount;

                $midtrans = new CreateSnapTokenService($order);
                $snapToken = $midtrans->getSnapToken();

                session([
                    'midtrans_details' => [
                        'order_no' => $order['order_no'],
                        'total_price' => $order['total_price'],
                        'snap_token' => $snapToken,
                    ],
                ]);

                session([
                    'order_payment' => [
                        'payment_provider' => 'midtrans',
                        'amount' => $amount,
                        'currency_symbol' => 'Rp',
                        'usd_amount' => $usd,
                    ],
                ]);
            }

            // Flutterwave Amount
            if (config('templatecookie.flw_public_key') && config('templatecookie.flw_secret') && config('templatecookie.flw_secret_hash') && config('templatecookie.flw_active')) {
                $flutterwave_amount = currencyConversion($job_total_amount, 'NGN', 1);
            }

            return view('frontend.pages.company.payperjob_pricing', [
                'payment_setting' => $payment_setting,
                'mid_token' => $snapToken ?? null,
                'manual_payments' => $manual_payments,
                'job_total_amount' => $job_total_amount,
                'job_total_amount' => $job_total_amount,
                'flutterwave_amount' => $flutterwave_amount ?? null,
            ]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company create job page
     *
     * @return Response
     */
    public function createJob()
    {
        try {
            // Check if user has reached the job limit
            storePlanInformation();
            $userPlan = session('user_plan');

            if ((int) $userPlan->job_limit < 1) {
                session()->flash('error', __('you_have_reached_your_plan_limit_please_upgrade_your_plan'));

                return redirect()->route('company.plan');
            }

            $data['jobCategories'] = JobCategory::all()->sortBy('name');
            $data['roles'] = JobRole::all()->sortBy('name');
            $data['experiences'] = Experience::all();
            $data['educations'] = Education::all();
            $data['job_types'] = JobType::all();
            $data['salary_types'] = SalaryType::all();
            $data['tags'] = Tag::all()->sortBy('name');
            $data['setting'] = loadSetting();
            $all_benefits = Benefit::all()->sortBy('name');
            $data['questions'] = Auth::user()
                ->company->questions()
                ->where('reuse', true)
                ->get();
            $non_company_benefits = $all_benefits->whereNull('company_id');
            $company_benefits = $all_benefits->where('company_id', currentCompany()->id);
            $data['benefits'] = $non_company_benefits->merge($company_benefits);
            $data['skills'] = Skill::all()->sortBy('name');

            return view('frontend.pages.company.postjob', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company store job
     *
     * @return Response
     */
    public function storeJob(JobCreateRequest $request)
    {
        try {
            $jobCreated = (new CompanyStoreService())->execute($request);

            flashSuccess(__('job_created_successfully'));

            return redirect()->route('company.job.promote.show', $jobCreated->slug);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * job edit
     *
     * @return Response
     */
    public function editJob(Job $job)
    {
        try {
            $data['jobCategories'] = JobCategory::all()->sortBy('name');
            $data['roles'] = JobRole::all()->sortBy('name');
            $data['experiences'] = Experience::all();
            $data['educations'] = Education::all();
            $data['job_types'] = JobType::all();
            $data['salary_types'] = SalaryType::all();
            $data['tags'] = Tag::all()->sortBy('name');
            $data['start_day'] = $job->created_at->diffInDays();
            $data['end_day'] = $data['start_day'] + setting('job_deadline_expiration_limit');
            $data['skills'] = Skill::all()->sortBy('name');
            $job->load('tags', 'benefits');
            $data['job'] = $job;

            $all_benefits = Benefit::all()->sortBy('name');
            $non_company_benefits = $all_benefits->whereNull('company_id');
            $company_benefits = $all_benefits->where('company_id', currentCompany()->id);
            $data['benefits'] = $non_company_benefits->merge($company_benefits);
            $data['questions'] = Auth::user()
                ->company->questions()
                ->where('reuse', true)
                ->get();

            return view('frontend.pages.company.editjob', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * job update
     *
     * @return Response
     */
    public function updateJob(JobCreateRequest $request, Job $job)
    {
        try {
            (new CompanyUpdateService())->execute($request, $job);

            return redirect()->route('company.myjob');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show promote job page
     *
     * @return Response
     */
    public function showPromoteJob(Job $job)
    {
        try {
            return view('frontend.pages.company.job-created-success', [
                'jobCreated' => $job,
            ]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company promote job page
     *
     * @return Response
     */
    public function jobPromote(Job $job)
    {
        try {
            if (! auth('user')->check() || authUser()->role != 'company') {
                return abort(403);
            }

            return view('frontend.pages.company.promote-job', [
                'jobCreated' => $job,
            ]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company promote job
     *
     * @return Response
     */
    public function promoteJob(Request $request, Job $jobCreated)
    {
        try {
            (new CompanyPromoteJobService())->execute($request, $jobCreated);

            flashSuccess(__('job_promote_successfully'));

            return redirect()->route('website.job.details', $jobCreated->slug);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company bookmark candidate page
     *
     * @return Response
     */
    public function bookmarks(Request $request)
    {
        try {
            $query = currentCompany()->bookmarkCandidates();

            if ($request->category != 'all' && $request->has('category') && $request->category != null) {
                $query->wherePivot('category_id', $request->category);
            }

            $bookmarks = $query
                ->with('profession')
                ->paginate(12)
                ->withQueryString();
            $categories = CompanyBookmarkCategory::where('company_id', auth()->user()->company->id)->get();

            return view('frontend.pages.company.bookmark', compact('bookmarks', 'categories'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company bookmark candidate
     *
     * @return Response
     */
    public function companyBookmarkCandidate(Request $request, Candidate $candidate)
    {
        try {
            $company = currentCompany();

            if ($request->cat) {
                $user_plan = $company->userPlan;

                if (isset($user_plan) && $user_plan->candidate_cv_view_limit <= 0) {
                    return response()->json([
                        'message' => __('you_have_reached_your_limit_for_viewing_candidate_cv_please_upgrade_your_plan'),
                        'success' => false,
                        'redirect_url' => route('website.plan'),
                    ]);
                }

                isset($user_plan) ? $user_plan->decrement('candidate_cv_view_limit') : '';
            }

            $check = $company->bookmarkCandidates()->toggle($candidate->id);

            if ($check['attached'] == [$candidate->id]) {
                DB::table('bookmark_company')
                    ->where('company_id', currentCompany()->id)
                    ->where('candidate_id', $candidate->id)
                    ->update(['category_id' => $request->cat]);

                // make notification to candidate
                $user = Auth::user('user');
                if ($candidate->user->shortlisted_alert) {
                    Notification::send($candidate->user, new CandidateBookmarkNotification($user, $candidate));
                }
                // notify to company
                Notification::send(auth()->user(), new CandidateBookmarkNotification($user, $candidate));

                flashSuccess(__('candidate_added_to_bookmark_list'));
            } else {
                flashSuccess(__('candidate_removed_from_bookmark_list'));
            }

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company setting page
     *
     * @param  Request  $request
     * @param  Candidate  $candidate
     * @return Response
     */
    public function setting()
    {
        try {
            $data['user'] = User::with('company', 'contactInfo', 'socialInfo')->findOrFail(auth('user')->id());
            $data['socials'] = $data['user']->socialInfo;
            $data['contact'] = $data['user']->contactInfo;
            $data['organization_types'] = OrganizationType::all()->sortBy('name');
            $data['industry_types'] = IndustryType::all()->sortBy('name');
            $data['team_sizes'] = TeamSize::all();

            return view('frontend.pages.company.setting', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company setting update
     *
     * @return Response
     */
    public function settingUpdateInformation(Request $request)
    {
        try {
            (new CompanySettingUpdateService())->update($request);

            flashSuccess(__('profile_updated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company Plan
     *
     * @return \Illuminate\Http\Response
     */
    public function plan()
    {
        try {
            $current_language = currentLanguage();
            $current_language_code = $current_language ? $current_language->code : config('templatecookie.default_language');
            $userplan = UserPlan::with([
                'plan' => function ($q) use ($current_language_code) {
                    $q->with([
                        'descriptions' => function ($q) use ($current_language_code) {
                            $q->where('locale', $current_language_code);
                        },
                    ]);
                },
            ])
                ->companyData()
                ->firstOrFail();
            $transactions = Earning::with('plan:id,label', 'manualPayment:id,name')
                ->companyData()
                ->latest()
                ->paginate(6);

            return view('frontend.pages.company.plan', compact('userplan', 'transactions', 'current_language', 'current_language_code'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Download Transaction Invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTransactionInvoice(Earning $transaction)
    {
        try {
            $transaction = $transaction->load('plan', 'company.user.contactInfo');
            $pdf = PDF::loadView('frontend.pages.invoice.download-invoice', compact('transaction'))->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download('invoice_'.$transaction->order_id.'.pdf');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * View Transaction Invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function viewTransactionInvoice(Earning $transaction)
    {
        try {
            if (currentCompany()->id != $transaction->company_id) {
                abort(404);
            }

            $transaction = $transaction->load('plan', 'company.user.contactInfo');

            return view('frontend.pages.invoice.preview-invoice', compact('transaction'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Account Progress
     *
     * @return \Illuminate\Http\Response
     */
    public function accountProgress()
    {
        try {
            $data['user'] = User::with('company', 'contactInfo', 'socialInfo')->findOrFail(auth()->user()->id);
            $data['countries'] = Country::all();
            $data['industry_types'] = IndustryType::all()->sortBy('name');
            $data['organization_types'] = OrganizationType::all()->sortBy('name');
            $data['team_sizes'] = TeamSize::all();
            $title = cms::first()->account_setup_title;
            $subtitle = cms::first()->account_setup_subtitle;
            $data['title'] = $title;
            $data['subtitle'] = $subtitle;
            $data['socials'] = $data['user']->socialInfo;

            if (request()->has('complete')) {
                return view('frontend.pages.company.account-progress.complete', compact('title', 'subtitle'));
            }

            return view('frontend.pages.company.account-progress', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Profile Complete Progress
     *
     * @return \Illuminate\Http\Response
     */
    public function profileCompleteProgress(Request $request)
    {
        try {
            return (new CompanyAccountProgressService())->execute($request);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Make Job Expire
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function makeJobExpire(Job $job)
    {
        try {
            $job->update(['status' => 'expired']);

            flashSuccess(__('job_status_now_expire'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Make Job Active
     *
     * @return \Illuminate\Http\Response
     */
    public function makeJobActive(Job $job)
    {
        try {

            if ($job->deadline < now()) {

                flashWarning('Deadline expired');
            } else {

                $job->update(['status' => 'active']);

                flashSuccess('Job Status Now Active');

            }

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Bookmark Categories
     *
     * @return \Illuminate\Http\Response
     */
    public function bookmarkCategories(Request $request)
    {
        try {
            $query = CompanyBookmarkCategory::where('company_id', auth()->user()->company->id);
            $categories = $query->paginate(12);
            $dataCount = CompanyBookmarkCategory::where('company_id', auth()->user()->company->id)->count();

            if ($request->ajax) {
                return response()->json($query->get());
            }

            return view('frontend.pages.company.bookmark-category', compact('categories', 'dataCount'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Bookmark Category Store
     *
     * @return \Illuminate\Http\Response
     */
    public function bookmarkCategoriesStore(Request $request)
    {
        try {
            $request->validate(['name' => 'required| min:2']);

            CompanyBookmarkCategory::create([
                'company_id' => auth()->user()->company->id,
                'name' => $request->name,
            ]);

            flashSuccess(__('category_created_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Bookmark Category Edit
     *
     * @return \Illuminate\Http\Response
     */
    public function bookmarkCategoriesEdit(CompanyBookmarkCategory $category)
    {
        try {
            $categories = CompanyBookmarkCategory::where('company_id', auth()->user()->company->id)->paginate(12);
            $dataCount = CompanyBookmarkCategory::where('company_id', auth()->user()->company->id)->count();

            return view('frontend.pages.company.bookmark-category', compact('categories', 'dataCount', 'category'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Bookmark Category Update
     *
     * @return \Illuminate\Http\Response
     */
    public function bookmarkCategoriesUpdate(Request $request, CompanyBookmarkCategory $category)
    {
        try {
            $category->update(['name' => $request->name]);

            flashSuccess(__('category_updated_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Bookmark Category Delete
     *
     * @return \Illuminate\Http\Response
     */
    public function bookmarkCategoriesDestroy(CompanyBookmarkCategory $category)
    {
        try {
            $category->delete();

            flashSuccess(__('category_deleted_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Job Clone
     *
     * @return \Illuminate\Http\Response
     */
    public function jobClone(Job $job)
    {
        try {
            $user = authUser();
            $user_plan = $user->company->userPlan;

            if (! $user_plan->job_limit) {
                session()->flash('error', __('you_have_reached_your_plan_limit_please_upgrade_your_plan'));

                return redirect()->route('company.plan');
            }

            $newJob = $job->replicate();
            $newJob->created_at = now();

            if ($job->featured && $user_plan->featured_job_limit) {
                $newJob->featured = 1;
                $user_plan->featured_job_limit = $user_plan->featured_job_limit - 1;
            } else {
                $newJob->featured = 0;
            }

            if ($job->highlight && $user_plan->highlight_job_limit) {
                $newJob->highlight = 1;
                $user_plan->highlight_job_limit = $user_plan->highlight_job_limit - 1;
            } else {
                $newJob->highlight = 0;
            }

            $newJob->save();
            $user_plan->job_limit = $user_plan->job_limit - 1;
            $user_plan->save();

            storePlanInformation();

            flashSuccess(__('job_cloned_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Company Username Update
     *
     * @return \Illuminate\Http\Response
     */
    public function usernameUpdate(Request $request)
    {
        try {
            $request->session()->put('type', 'account');

            if ($request->type == 'company_username') {
                $request->validate([
                    'username' => 'required|unique:users,username,'.auth()->user()->id,
                ]);

                authUser()->update([
                    'username' => $request->username,
                ]);

                flashSuccess(__('username_updated_successfully'));

                return back();
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function manageQuestion()
    {
        try {
            $questions = currentCompany()
                ->questions()
                ->latest()
                ->paginate(8);
            $dataCount = currentCompany()
                ->questions()
                ->count();

            return view('frontend.pages.company.manage-questions', [
                'questions' => $questions,
                'dataCount' => $dataCount,
            ]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function storeQuestion(Request $request)
    {
        try {
            if ($request->get('isEditing') == 'true' && $request->get('editingId')) {
                $toEdit = CompanyQuestion::query()->findOrFail($request->get('editingId'));

                $toEdit->update([
                    'title' => $request->get('newQuestion'),
                    'required' => $request->has('isRequired'),
                ]);

                flashSuccess(__('question_updated_success'));

                return back();
            }

            if ($request->wantsJson()) {
                $request->validate(['newQuestion' => 'required']);
                $question = currentCompany()
                    ->questions()
                    ->create([
                        'reuse' => $request->get('newQuestionSave'),
                        'title' => $request->get('newQuestion'),
                        'required' => $request->get('isRequired'),
                    ]);

                return response()->json($question->only('id', 'reuse', 'title', 'required'), 201);
            }
            $request->validate(['newQuestion' => 'required']);
            currentCompany()
                ->questions()
                ->create([
                    'reuse' => $request->has('newQuestionSave'),
                    'title' => $request->get('newQuestion'),
                    'required' => $request->has('isRequired'),
                    'reuse' => 1,
                ]);

            flashSuccess(__('question_created_success'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function deleteQuestion(CompanyQuestion $question)
    {
        $question->delete();
        flashSuccess(__('question_deleted_success'));

        return back();
    }

    public function featureToggle(Request $request)
    {
        try {
            if ($request->has('enableQuestion')) {
                currentCompany()->update([
                    'question_feature_enable' => true,
                ]);
                flashSuccess(__('question_feature_enable'));
            } else {
                currentCompany()->update([
                    'question_feature_enable' => false,
                ]);
                flashSuccess(__('question_feature_disabled'));
            }

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
