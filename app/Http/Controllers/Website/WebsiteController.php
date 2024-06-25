<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Traits\CandidateAble;
use App\Http\Traits\HasCountryBasedJobs;
use App\Http\Traits\JobAble;
use App\Http\Traits\ResetCvViewsHistoryTrait;
use App\Models\Candidate;
use App\Models\CandidateCvView;
use App\Models\CandidateResume;
use App\Models\Company;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Job;
use App\Models\Profession;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\Website\Candidate\ApplyJobNotification;
use App\Notifications\Website\Candidate\BookmarkJobNotification;
use App\Services\Website\Candidate\CandidateProfileDetailsService;
use App\Services\Website\Company\CompanyDetailsService;
use App\Services\Website\Company\CompanyListService;
use App\Services\Website\IndexPageService;
use App\Services\Website\Job\JobListService;
use App\Services\Website\PricePlanService;
use App\Services\Website\PrivacyPolicyService;
use App\Services\Website\RefundPolicyService;
use App\Services\Website\TermsConditionService;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostCategory;
use Modules\Blog\Entities\PostComment;
use Modules\Currency\Entities\Currency as CurrencyModel;
use Modules\Faq\Entities\Faq;
use Modules\Faq\Entities\FaqCategory;
use Modules\Language\Entities\Language;
use Modules\Location\Entities\Country;
use Modules\Plan\Entities\Plan;
use Modules\Testimonial\Entities\Testimonial;
use Srmklive\PayPal\Services\PayPal;
use Stevebauman\Location\Facades\Location;

class WebsiteController extends Controller
{
    use CandidateAble, HasCountryBasedJobs, JobAble, ResetCvViewsHistoryTrait;

    public $setting;

    public function __construct()
    {
        $this->setting = loadSetting(); // see helpers.php
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function dashboard()
    {
        try {
            if (auth('user')->check() && authUser()->role == 'candidate') {
                return redirect()->route('candidate.dashboard');
            } elseif (auth('user')->check() && authUser()->role == 'company') {
                storePlanInformation();

                return redirect()->route('company.dashboard');
            }

            return redirect('login');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Notification mark as read
     *
     * @param  Request  $request
     * @return void
     */
    public function notificationRead()
    {
        try {
            foreach (auth()->user()->unreadNotifications as $notification) {
                $notification->markAsRead();
            }

            return response()->json(true);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Home page view
     *
     * @param  Request  $request
     * @return void
     */
    public function index()
    {
        try {
            $data = (new IndexPageService())->execute();

            if ($this?->setting?->landing_page == 2) {
                return view('frontend.pages.index-2', $data);
            } elseif ($this->setting->landing_page == 3) {
                return view('frontend.pages.index-3', $data);
            } else {
                return view('frontend.pages.index', $data);
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Terms and condition page view
     *
     * @param  Request  $request
     * @return void
     */
    public function termsCondition()
    {
        try {
            $data = (new TermsConditionService())->execute();

            return view('frontend.pages.terms-condition', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Privacy policy page view
     *
     * @param  Request  $request
     * @return void
     */
    public function privacyPolicy()
    {
        try {
            $data = (new PrivacyPolicyService())->execute();

            return view('frontend.pages.privacy-policy', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Refund policy page view
     *
     * @param  Request  $request
     * @return void
     */
    public function refundPolicy()
    {

        try {
            $data = (new RefundPolicyService())->execute();

            return view('frontend.pages.refund-policy', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Job page view
     *
     * @return void
     */
    public function jobs(Request $request)
    {
        try {
            $data = (new JobListService())->jobs($request);

            // For adding currency code
            $current_currency = currentCurrency();

            return view('frontend.pages.jobs', $data, compact('current_currency'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function loadmore(Request $request)
    {
        try {
            $data = (new JobListService())->loadMore($request);

            return view('components.website.job.load-more-jobs', compact('data'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Job category page view
     *
     * @param  string  $slug
     * @return void
     */
    public function jobsCategory(Request $request, $slug)
    {
        try {
            $data = (new JobListService())->categoryJobs($request, $slug);

            return view('frontend.pages.jobsCategory', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Job details page view
     *
     * @param  Request  $request
     * @param  string  $slug
     * @return void
     */
    public function jobDetails(Job $job)
    {
        try {
            if ($job->status == 'pending') {
                if (! auth('admin')->check()) {
                    abort_if(! auth('user')->check(), 404);
                    abort_if(authUser()->role != 'company', 404);
                    abort_if(currentCompany()->id != $job->company_id, 404);
                }
            }

            $data = $this->getJobDetails($job);
            $data['questions'] = $job->questions;

            return view('frontend.pages.job-details', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate page view
     *
     * @return void
     */
    public function candidates(Request $request)
    {
        abort_if(auth('user')->check() && authUser()->role == 'candidate', 404);

        try {
            $data['professions'] = Profession::all()->sortBy('name');
            $data['candidates'] = $this->getCandidates($request);
            $data['experiences'] = Experience::all();
            $data['educations'] = Education::all();
            $data['skills'] = Skill::all()->sortBy('name');
            $data['popularTags'] = Tag::popular()
                ->withCount('tags')
                ->latest('tags_count')
                ->get()
                ->take(10);

            // reset candidate cv views history
            $this->reset();

            return view('frontend.pages.candidates', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate details page view
     *
     * @param  string  $username
     * @return void
     */
    public function candidateDetails(Request $request, $username)
    {
        try {
            $candidate = User::where('username', $username)
                ->with('candidate', 'contactInfo', 'socialInfo')
                ->firstOrFail();

            abort_if(auth('user')->check() && $candidate->id != auth('user')->id(), 404);

            if ($request->ajax) {
                return response()->json($candidate);
            }

            return view('frontend.pages.candidate-details', compact('candidate'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate profile details
     *
     * @return Response
     */
    public function candidateProfileDetails(Request $request)
    {
        try {
            if (! auth('user')->check()) {
                return response()->json([
                    'message' => __('if_you_perform_this_action_you_need_to_login_your_account_first_do_you_want_to_login_now'),
                    'success' => false,
                ]);
            }

            $user = authUser();

            if ($user->role != 'company') {
                return response()->json([
                    'message' => __('you_are_not_authorized_to_perform_this_action'),
                    'success' => false,
                ]);
            } else {
                $user_plan = $user->company->userPlan;
            }
            if (! $user_plan) {
                return response()->json([
                    'message' => __('you_dont_have_a_chosen_plan_please_choose_a_plan_to_continue'),
                    'success' => false,
                ]);
            }

            $already_view = CandidateCvView::join('candidates', 'candidate_cv_views.candidate_id', '=', 'candidates.id')
                ->join('users', 'candidates.user_id', '=', 'users.id')
                ->where('users.username', $request->username)
                ->where('candidate_cv_views.company_id', currentCompany()->id)
                ->first();

            if (empty($already_view)) {
                if (isset($user_plan) && $user_plan->candidate_cv_view_limitation == 'limited' && $user_plan->candidate_cv_view_limit <= 0) {
                    return response()->json([
                        'message' => __('you_have_reached_your_limit_for_viewing_candidate_cv_please_upgrade_your_plan'),
                        'success' => false,
                        'redirect_url' => route('website.plan'),
                    ]);
                }
            }

            $data = (new CandidateProfileDetailsService())->execute($request);

            return response()->json($data);
        } catch (\Exception $e) {

            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }

    /**
     * Candidate application profile details
     *
     * @return Response
     */
    public function candidateApplicationProfileDetails(Request $request)
    {
        try {
            $candidate = User::where('username', $request->username)
                ->with([
                    'contactInfo',
                    'socialInfo',
                    'candidate' => function ($query) {
                        $query->with('experiences', 'educations', 'experience', 'coverLetter', 'education', 'profession', 'languages:id,name', 'skills', 'socialInfo');
                    },
                ])
                ->firstOrFail();

            $candidate->candidate->birth_date = Carbon::parse($candidate->candidate->birth_date)->format('d F, Y');

            $languages = $candidate->candidate
                ->languages()
                ->pluck('name')
                ->toArray();
            $candidate_languages = $languages ? implode(', ', $languages) : '';

            $skills = $candidate->candidate->skills->pluck('name');
            $candidate_skills = $skills ? implode(', ', json_decode(json_encode($skills))) : '';

            return response()->json([
                'success' => true,
                'data' => $candidate,
                'skills' => $candidate_skills,
                'languages' => $candidate_languages,
            ]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate download cv
     *
     * @return void
     */
    public function candidateDownloadCv(CandidateResume $resume)
    {
        try {

            $company_id = auth()->user()->companyId();
            $hasCandidate = DB::table('applied_jobs')
                ->whereIn('job_id', function ($query) use ($company_id) {
                    $query->select('id')
                        ->from('jobs')
                        ->where('company_id', $company_id);
                })
                ->where('candidate_id', $resume->candidate_id)
                ->exists();
            // $user = auth()->user();
            // $hasCandidate = $user->company->applicationGroups()->whereHas('applications.candidate', function ($query) use ($user, $resume) {
            //     $query->where('user_id', $user->id)
            //         ->where('id', $resume->candidate_id);
            // })->exists();

            if (! $hasCandidate) {
                return redirect()->back();
            }

            $filePath = $resume->file;
            $filename = time().'.pdf';
            $headers = ['Content-Type: application/pdf', 'filename' => $filename];
            $fileName = rand().'-resume'.'.pdf';

            return response()->download($filePath, $fileName, $headers);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Employer page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function employees(Request $request)
    {
        try {
            abort_if(auth('user')->check() && authUser()->role == 'company', 404);

            $data = (new CompanyListService())->execute($request);

            return view('frontend.pages.employees', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Employers details page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function employersDetails($username)
    {
        try {

            $user = User::where('role', 'company')->where('username', $username)->first();

            $data = (new CompanyDetailsService())->execute($user);

            return view('frontend.pages.employe-details', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * About page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function about()
    {
        try {
            $testimonials = Testimonial::all();
            $companies = Company::count();
            $candidates = Candidate::count();

            return view('frontend.pages.about', compact('testimonials', 'companies', 'candidates'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Plan page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function pricing()
    {
        try {
            abort_if(auth('user')->check() && authUser()->role == 'candidate', 404);
            $plans = Plan::active()->get();
            $plan_descriptions = $plans->pluck('descriptions')->flatten();

            $current_language = currentLanguage();
            $current_currency = currentCurrency();
            $current_language_code = $current_language ? $current_language->code : config('templatecookie.default_language');
            $faqs = Faq::where('code', currentLangCode())
                ->with('faq_category')
                ->whereHas('faq_category', function ($query) {
                    $query->where('name', 'Plan');
                })
                ->get();

            if ($current_language_code) {
                $plans->load([
                    'descriptions' => function ($q) use ($current_language_code) {
                        $q->where('locale', $current_language_code);
                    },
                ]);
            }

            return view('frontend.pages.pricing', compact('plans', 'faqs', 'current_language', 'plan_descriptions', 'current_currency', 'current_language_code'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Plan details page
     *
     * @param  string  $label
     * @return void
     */
    public function planDetails($label)
    {
        try {
            abort_if(! auth('user')->check(), 404);
            abort_if(auth('user')->check() && auth('user')->user()->role == 'candidate', 404);

            $data = (new PricePlanService())->details($label);

            return view('frontend.pages.plan-details', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Contact page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact()
    {
        return view('frontend.pages.contact');
    }

    /**
     * Faq page
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function faq()
    {
        try {
            $faq_categories = FaqCategory::with([
                'faqs' => function ($q) {
                    $q->where('code', currentLangCode());
                },
            ])->get();

            return view('frontend.pages.faq', compact('faq_categories'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function toggleBookmarkJob(Job $job)
    {
        try {
            $check = $job->bookmarkJobs()->toggle(auth('user')->user()->candidate);

            if ($check['attached'] == [1]) {
                $user = auth('user')->user();
                // make notification to company candidate bookmark job
                Notification::send($job->company->user, new BookmarkJobNotification($user, $job));
                // make notification to candidate for notify
                if (auth()->user()->recent_activities_alert) {
                    Notification::send(auth('user')->user(), new BookmarkJobNotification($user, $job));
                }
            }

            $check['attached'] == [1] ? ($message = __('job_added_to_favorite_list')) : ($message = __('job_removed_from_favorite_list'));

            flashSuccess($message);

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function toggleApplyJob(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'resume_id' => 'required',
                    'cover_letter' => 'required',
                ],
                [
                    'resume_id.required' => 'Please select resume',
                    'cover_letter.required' => 'Please enter cover letter',
                ],
            );

            if ($validator->fails()) {
                flashError($validator->errors()->first());

                return back();
            }

            if (auth('user')->user()->candidate->profile_complete != 0) {
                flashError(__('complete_your_profile_before_applying_to_jobs_add_your_information_resume_and_profile_picture_for_a_better_chance_of_getting_hired'));

                return redirect()->route('candidate.dashboard');
            }

            $candidate = auth('user')->user()->candidate;
            $job = Job::find($request->id);

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
            $job->company->user->notify(new ApplyJobNotification(auth('user')->user(), $job->company->user, $job));

            if (auth('user')->user()->recent_activities_alert) {
                auth('user')
                    ->user()
                    ->notify(new ApplyJobNotification(auth('user')->user(), $job->company->user, $job));
            }

            flashSuccess(__('job_applied_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function register($role)
    {
        try {
            return view('frontend.auth.register', compact('role'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Get all posts
     *
     * @return void
     */
    public function posts(Request $request)
    {
        try {
            $code = currentLangCode();
            $key = request()->search;
            $posts = Post::query()
                ->where('locale', $code)
                ->published()
                ->withCount('comments');

            if ($key) {
                $posts->whereLike('title', $key);
            }

            if ($request->category) {
                $category_ids = PostCategory::whereIn('slug', $request->category)
                    ->get()
                    ->pluck('id');
                $posts = $posts
                    ->whereIn('category_id', $category_ids)
                    ->latest()
                    ->paginate(10)
                    ->withQueryString();
            } else {
                $posts = $posts
                    ->latest()
                    ->paginate(10)
                    ->withQueryString();
            }

            $recent_posts = Post::where('locale', $code)
                ->published()
                ->withCount('comments')
                ->latest()
                ->take(5)
                ->get();
            $categories = PostCategory::latest()->get();

            return view('frontend.pages.posts', compact('posts', 'categories', 'recent_posts'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Post details
     *
     * @param  string  $slug
     * @return void
     */
    public function post($slug)
    {
        try {
            $code = currentLangCode();
            $data['post'] = Post::published()
                ->whereSlug($slug)
                ->where('locale', $code)
                ->with(['author:id,name,name', 'comments.replies.user:id,name,image'])
                ->first();

            if (! $data['post']) {
                $current_language = getLanguageByCode($code);
                $post_language = getLanguageByCode(Post::whereSlug($slug)->value('locale'));
                $data['error_message'] = "This post is not available in {$current_language}, change the language to {$post_language} to see this post";

                flashError($data['error_message']);
                abort(404);
            }

            return view('frontend.pages.post', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Post comment
     *
     * @return void
     */
    public function comment(Post $post, Request $request)
    {
        try {
            if (! auth()->check()) {
                flashError(__('if_you_perform_this_action_you_need_to_login_your_account_first_do_you_want_to_login_now'));

                return redirect()->route('login');
            }

            $request->validate([
                'body' => 'required|max:2500|min:2',
            ]);

            $comment = new PostComment();
            $comment->author_id = auth()->user()->id;
            $comment->post_id = $post->id;
            if ($request->has('parent_id')) {
                $comment->parent_id = $request->parent_id;
                $redirect = '#replies-'.$request->parent_id;
            } else {
                $redirect = '#comments';
            }
            $comment->body = $request->body;
            $comment->save();

            return redirect(url()->previous().$redirect);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Mark all notification as read
     *
     * @return void
     */
    public function markReadSingleNotification(Request $request)
    {
        try {
            $has_unread_notification = auth()
                ->user()
                ->unreadNotifications->count();

            if ($has_unread_notification && $request->id) {
                auth()
                    ->user()
                    ->unreadNotifications->where('id', $request->id)
                    ->markAsRead();
            }

            return true;
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Set session
     *
     * @return void
     */
    public function setSession(Request $request)
    {
        try {
            info($request->all());
            $request->session()->put('location', $request->input());

            return response()->json(true);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Set current location
     *
     * @param  Request  $request
     * @return void
     */
    public function setCurrentLocation($request)
    {
        return false;
        try {
            // Current Visitor Location Track && Set Country IF App Is Multi Country Base
            $app_country = setting('app_country_type');

            if ($app_country == 'multiple_base') {
                $ip = request()->ip();
                // $ip = '103.102.27.0'; // Bangladesh
                // $ip = '105.179.161.212'; // Mauritius
                // $ip = '110.33.122.75'; // AUD
                // $ip = '5.132.255.255'; // SA
                // $ip = '107.29.65.61'; // United States"
                // $ip = '46.39.160.0'; // Czech Republic
                // $ip = "94.112.58.11"; // Czechia
                // if ($ip) {
                //     $current_user_data = Location::get($ip);
                //     if ($current_user_data) {
                //         $user_country = $current_user_data->countryName;
                //         if ($user_country) {
                //             $this->setLangAndCurrency($user_country);
                //             $database_country = Country::where('name', $user_country)
                //                 ->where('status', 1)
                //                 ->first();
                //             if ($database_country) {
                //                 $selected_country = session()->get('selected_country');
                //                 if (! $selected_country) {
                //                     session()->put('selected_country', $database_country->id);

                //                     return true;
                //                 }
                //             }
                //         }
                //     }
                // } else {
                //     return false;
                // }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Process for set currency & language
     *
     * @param  string  $name
     * @return bool
     */
    public function setLangAndCurrency($name)
    {
        try {
            // this process for get language code/sort name  and currency sortname
            $get_lang_wise_sort_name = json_decode(file_get_contents(base_path('resources/backend/dummy-data/country_currency_language.json')), true);

            $country_name = Str::slug($name);
            if ($get_lang_wise_sort_name) {
                // loop json file data

                for ($i = 0; $i < count($get_lang_wise_sort_name); $i++) {
                    $json_country_name = Str::slug($get_lang_wise_sort_name[$i]['name']);

                    if ($country_name == $json_country_name) {
                        // check country are same

                        $cn_code = $get_lang_wise_sort_name[$i]['currency']['code'];
                        $ln_code = $get_lang_wise_sort_name[$i]['language']['code'];

                        // Currency setup
                        $set_currency = CurrencyModel::where('code', Str::upper($cn_code))->first();
                        if ($set_currency) {
                            session(['current_currency' => $set_currency]);
                            currencyRateStore();
                        }
                        // // Currency setup
                        $set_language = Language::where('code', Str::lower($ln_code))->first();
                        if ($set_language) {
                            session(['current_lang' => $set_language]);
                            // session()->put('set_lang', $lang);
                            app()->setLocale($ln_code);
                        }

                        // menu list cache clear
                        Cache::forget('menu_lists');

                        return true;
                    }
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Set selected country
     *
     * @return void
     */
    public function setSelectedCountry(Request $request)
    {
        try {
            session()->put('selected_country', $request->country);

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove selected country
     *
     * @return void
     */
    public function removeSelectedCountry()
    {
        try {
            session()->forget('selected_country');

            return redirect()->back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Job autocomplete
     *
     * @return array
     */
    public function jobAutocomplete(Request $request)
    {
        try {
            $jobs = Job::select('title as value', 'id')
                ->where('title', 'LIKE', '%'.$request->get('search').'%')
                ->active()
                ->withoutEdited()
                ->latest()
                ->get()
                ->take(15);

            if ($jobs && count($jobs)) {
                $data = '<ul class="dropdown-menu show">';
                foreach ($jobs as $job) {
                    $data .= '<li class="dropdown-item"><a href="'.route('website.job', ['keyword' => $job->value]).'">'.$job->value.'</a></li>';
                }
                $data .= '</ul>';
            } else {
                $data = '<ul class="dropdown-menu show"><li class="dropdown-item">No data found</li></ul>';
            }

            return response()->json($data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Careerjet jobs list
     *
     * @return Renderable
     */
    public function careerjetJobs(Request $request)
    {
        try {
            if (! config('templatecookie.careerjet_id')) {
                abort(404);
            }

            $careerjet_jobs = $this->getCareerjetJobs($request, 25);

            return view('frontend.pages.jobs.careerjet-jobs', compact('careerjet_jobs'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Indeed jobs list
     *
     * @return Renderable
     */
    public function indeedJobs(Request $request)
    {
        try {
            if (! config('templatecookie.indeed_id')) {
                abort(404);
            }

            $indeed_jobs = $this->getIndeedJobs($request, 25);

            return view('frontend.pages.jobs.indeed-jobs', compact('indeed_jobs'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function successTransaction(Request $request)
    {
        $provider = new PayPal;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            session(['transaction_id' => $response['id'] ?? null]);

            $this->orderPlacing();
        } else {
            session()->flash('error', __('payment_was_failed'));

            return back();
        }
    }
}
