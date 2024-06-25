<?php

namespace App;

use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Payment\PayPalController;
use App\Http\Controllers\Website\CandidateController;
use App\Http\Controllers\Website\CompanyController;
use App\Http\Controllers\Website\CompanyVerifyDocuments;
use App\Http\Controllers\Website\GlobalController;
use App\Http\Controllers\Website\MessengerController;
use App\Http\Controllers\Website\WebsiteController;
use App\Http\Requests\EmailVerificationUpdateRequest;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Seo\Entities\Seo;
use App\Http\Controllers\Api\CompanyController as ApiCompanyController;

// Route::get('/test', function () {
//     Seo::query()->delete();

//     $pages = [
//         [
//             'page_slug' => 'home',
//             'title' => 'Welcome To Jobpilot',
//         ],
//         [
//             'page_slug' => 'jobs',
//             'title' => 'Jobs',
//         ],
//         [
//             'page_slug' => 'job-details',
//             'title' => 'Job Details',
//         ],
//         [
//             'page_slug' => 'candidates',
//             'title' => 'Candidates',
//         ],
//         [
//             'page_slug' => 'candidate-details',
//             'title' => 'Candidate Details',
//         ],
//         [
//             'page_slug' => 'company',
//             'title' => 'Company',
//         ],
//         [
//             'page_slug' => 'company-details',
//             'title' => 'Company Details',
//         ],
//         [
//             'page_slug' => 'blog',
//             'title' => 'Blog',
//         ],
//         [
//             'page_slug' => 'post-details',
//             'title' => 'Post Details',
//         ],
//         [
//             'page_slug' => 'pricing',
//             'title' => 'Pricing',
//         ],
//         [
//             'page_slug' => 'login',
//             'title' => 'Login',
//         ],
//         [
//             'page_slug' => 'register',
//             'title' => 'Register',
//         ],
//         [
//             'page_slug' => 'about',
//             'title' => 'About',
//         ],
//         [
//             'page_slug' => 'contact',
//             'title' => 'Contact',
//         ],
//         [
//             'page_slug' => 'faq',
//             'title' => 'FAQ',
//         ],
//         [
//             'page_slug' => 'terms-condition',
//             'title' => 'Terms Condition',
//         ],

//     ];

//     $seo_chunks = array_chunk($pages, ceil(count($pages) / 3));

//     foreach ($seo_chunks as $key => $seo) {
//         // return $seo[0]['page_slug'];
//         $page = Seo::insert([
//             'page_slug' => $seo[$key]['page_slug'],
//             // 'title' => $seo['title'],
//         ]);

//         // $page->contents()->create([
//         //     'language_code' => 'en',
//         //     'title' => $seo['title'],
//         //     'description' => 'Jobpilot is job portal laravel script designed to create, manage and publish jobs posts. Companies can create their profile and publish jobs posts. Candidate can apply job posts.',
//         //     'image' => 'frontend/assets/images/jobpilot.png',
//         // ]);
//     }

//     return Seo::all();
// });


// =====================================================================
// =============================Authentication Routes===================
// ======================================================================
if (! app()->runningInConsole()) {
    Auth::routes(['verify' => setting('email_verification')]);
} else {
    Auth::routes(['verify' => false]);
}

// Email Verification
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    if (authUser()->role == 'company') {
        return redirect()->route('company.dashboard', ['verified' => true]);
    } else {
        return redirect()->route('candidate.dashboard', ['verified' => true]);
    }
})->middleware(['auth', 'signed'])->name('verification.verify');

// Email Verification Update
Route::get('/email/verify/update/{id}/{newEmail}', function (EmailVerificationUpdateRequest $request, $id, $newEmail) {
    if (! $request->hasValidSignature()) {
        abort(401);
    }
    $request->fulfill($newEmail);

    if (authUser()->role == 'company') {
        return redirect()->route('company.dashboard', ['verified' => true]);
    } else {
        return redirect()->route('candidate.dashboard', ['verified' => true]);
    }
})->middleware(['auth', 'signed'])->name('email.verification.update.verify');

// Social Authentication
Route::controller(SocialLoginController::class)->group(function () {
    Route::post('/auth/social/register', 'register')->name('social.register');
    Route::get('/auth/{provider}/redirect', 'redirect')->name('social.login');
    Route::get('/auth/{provider}/callback', 'callback');
});

// =====================================================================
// =============================Guest Routes=============================
// ======================================================================
Route::controller(WebsiteController::class)->name('website.')->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/plans', 'pricing')->name('plan');
    Route::get('/plans/{label}', 'planDetails')->name('plan.details');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/terms-condition', 'termsCondition')->name('termsCondition');
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacyPolicy');
    Route::get('/refund-policy', 'refundPolicy')->name('refundPolicy');
    Route::get('/coming-soon', 'comingSoon')->name('comingsoon');
    Route::get('/careerjet/jobs', 'careerjetJobs')->name('careerjet.job');
    Route::get('/indeed/jobs', 'indeedJobs')->name('indeed.job');
    Route::get('/jobs', 'jobs')->name('job');
    Route::get('/loadmore', 'loadmore');
    Route::get('/jobs/category/{category}', 'jobsCategory')->name('job.category.slug');
    Route::get('/job/{job:slug}', 'jobDetails')->name('job.details');
    Route::get('/jobs/{job:slug}/bookmark', 'toggleBookmarkJob')->name('job.bookmark')->middleware('user_active');
    Route::post('/jobs/apply', 'toggleApplyJob')->name('job.apply')->middleware('user_active');
    Route::get('/candidates', 'candidates')->name('candidate');
    Route::get('/candidates/{candidate:username}', 'candidateDetails')->name('candidate.details');
    Route::get('/candidate/profile/details', 'candidateProfileDetails')->name('candidate.profile.details');
    Route::get('/candidate/application/profile/details', 'candidateApplicationProfileDetails')->name('candidate.application.profile.details');
    Route::get('/candidates/download/cv/{resume}', 'candidateDownloadCv')->name('candidate.download.cv');
    Route::get('/employers', 'employees')->name('company');
    Route::get('/employer/{user:username}', 'employersDetails')->name('employe.details');
    Route::get('/posts', 'posts')->name('posts');
    Route::get('/post/{post:slug}', 'post')->name('post');
    Route::post('/comment/{post:slug}/add', 'comment')->name('comment');
    Route::post('/markasread/single/notification', 'markReadSingleNotification')->name('markread.notification');
    Route::post('/set/session', 'setSession')->name('set.session');
    Route::get('/selected/country', 'setSelectedCountry')->name('set.country');
    Route::get('/selected/country/remove', 'removeSelectedCountry')->name('remove.country');
    Route::get('job/autocomplete', 'jobAutocomplete')->name('job.autocomplete');
    Route::post('/job/benefits/create', 'jobBenefitCreate')->name('job.benefit.create');
    Route::get('success-transaction', 'successTransaction')->name('paypal.successTransaction');

});

// ======================================================================
// =============================Authenticated Routes=====================
// ======================================================================
Route::middleware('auth:user', 'verified')->group(function () {
    // Dashboard Route
    Route::get('/user/dashboard', [WebsiteController::class, 'dashboard'])->name('user.dashboard');

    Route::post('/user/notification/read', [WebsiteController::class, 'notificationRead'])->name('user.notification.read');

    // Candidate Routes
    Route::controller(CandidateController::class)->prefix('candidate')->middleware('candidate')->name('candidate.')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('applied-jobs', 'appliedjobs')->name('appliedjob');
        Route::get('bookmarks', 'bookmarks')->name('bookmark');
        Route::get('settings', 'setting')->name('setting');
        Route::put('settings/update', 'settingUpdate')->name('settingUpdate');
        Route::get('/all/notifications', 'allNotification')->name('allNotification');
        Route::get('/job/alerts', 'jobAlerts')->name('job.alerts');
        Route::post('/resume/store', 'resumeStore')->name('resume.store');
        Route::post('/resume/store/ajax', 'resumeStoreAjax')->name('resume.store.ajax');
        Route::post('/get/resume/ajax', 'getResumeAjax')->name('get.resume.ajax');
        Route::post('/resume/update', 'resumeUpdate')->name('resume.update');
        Route::delete('/resume/delete/{resume}', 'resumeDelete')->name('resume.delete');
        Route::post('/experiences/store', 'experienceStore')->name('experiences.store');
        Route::put('/experiences/update', 'experienceUpdate')->name('experiences.update');
        Route::delete('/experiences/{experience}', 'experienceDelete')->name('experiences.destroy');
        Route::post('/educations/store', 'educationStore')->name('educations.store');
        Route::put('/educations/update', 'educationUpdate')->name('educations.update');
        Route::delete('/educations/{education}', 'educationDelete')->name('educations.destroy');
        Route::post('/cv/show', 'cvShow')->name('cv.show');
    });

    // Company Routes
    Route::controller(CompanyController::class)->prefix('company')->middleware(['company', 'has_plan'])->name('company.')->group(function () {
        Route::middleware('company.profile')->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');
            Route::get('plans', 'plan')->name('plan')->middleware('user_active');
            Route::post('download/transaction/invoice/{transaction}', 'downloadTransactionInvoice')->name('transaction.invoice.download');
            Route::get('view/transaction/invoice/{transaction:order_id}', 'viewTransactionInvoice')->name('transaction.invoice.view');
            Route::get('my-jobs', 'myjobs')->name('myjob')->withoutMiddleware('has_plan');
            Route::get('pending-edited-jobs', 'pendingEditedJobs')->name('pending.edited.jobs');
            Route::get('create/pay-per-job', 'payPerJob')->name('job.payPerJobCreate')->withoutMiddleware('has_plan');
            Route::post('/store/payper/job', 'storePayPerJob')->name('payperjob.store')->withoutMiddleware('has_plan');
            Route::get('create/job', 'createJob')->name('job.create')->middleware('user_active');
            Route::post('/store/job', 'storeJob')->name('job.store');
            Route::get('/job/payment', 'payPerJobPayment')->name('payperjob.payment')->withoutMiddleware('has_plan');
            Route::get('/promote/job/{job:slug}', 'showPromoteJob')->name('job.promote.show');
            Route::get('/promote/{job:slug}', 'jobPromote')->name('promote');
            Route::get('/clone/{job:slug}', 'jobClone')->name('clone');
            Route::post('/promote/job/{jobCreated}', 'promoteJob')->name('job.promote');
            Route::get('edit/{job:slug}/job', 'editJob')->name('job.edit')->withoutMiddleware('has_plan');
            Route::post('make/job/expire/{job}', 'makeJobExpire')->name('job.make.expire');
            Route::post('make/job/active/{job}', 'makeJobActive')->name('job.make.active');
            Route::put('/update/{job:slug}/job', 'updateJob')->name('job.update')->withoutMiddleware('has_plan');
            Route::get('job/applications', 'jobApplications')->name('job.application');
            Route::put('applications/sync', 'applicationsSync')->name('application.sync');
            Route::post('applications/column/store', 'applicationColumnStore')->name('applications.column.store');
            Route::delete('applications/group/delete/{group}', 'applicationColumnDelete')->name('applications.column.delete');
            Route::put('applications/group/update', 'applicationColumnUpdate')->name('applications.column.update');
            Route::delete('delete/{job:id}/application', 'destroyApplication')->name('application.delete');
            Route::get('bookmarks', 'bookmarks')->name('bookmark');
            Route::get('settings', 'setting')->name('setting')->withoutMiddleware('has_plan');
            Route::put('settings/update', 'settingUpdateInformation')->name('settingUpdateInformation')->withoutMiddleware('has_plan');
            Route::get('/all/notifications', 'allNotification')->name('allNotification');
            Route::post('applications/group/store', 'applicationsGroupStore')->name('applications.group.store');
            Route::put('applications/group/update/{group}', 'applicationsGroupUpdate')->name('applications.group.update');
            Route::delete('applications/group/destroy/{group}', 'applicationsGroupDestroy')->name('applications.group.destroy');
            Route::post('/questions', 'storeQuestion')->name('questions.store');
            Route::get('/questions', 'manageQuestion')->name('questions.manage');
            Route::post('/questions/featureToggle', 'featureToggle')->name('questions.featureToggle');
            Route::delete('/questions/{question}', 'deleteQuestion')->name('questions.delete');
        });

        Route::post('/company/bookmark/{candidate}', 'companyBookmarkCandidate')->name('companybookmarkcandidate')->middleware('user_active');
        Route::get('account-progress', 'accountProgress')->name('account-progress')->withoutMiddleware('has_plan');
        Route::put('/profile/complete/{id}', 'profileCompleteProgress')->name('profile.complete')->withoutMiddleware('has_plan');
        Route::get('/bookmark/categories', 'bookmarkCategories')->name('bookmark.category.index');
        Route::post('/bookmark/categories/store', 'bookmarkCategoriesStore')->name('bookmark.category.store');
        Route::get('/bookmark/categories/edit/{category}', 'bookmarkCategoriesEdit')->name('bookmark.category.edit');
        Route::put('/bookmark/categories/update/{category}', 'bookmarkCategoriesUpdate')->name('bookmark.category.update');
        Route::delete('/bookmark/categories/destroy/{category}', 'bookmarkCategoriesDestroy')->name('bookmark.category.destroy');
        Route::post('username/change', 'usernameUpdate')->name('username.change');
    });

    Route::prefix('company')->middleware(['company', 'has_plan'])->group(function () {
        Route::get('verify-documents', [CompanyVerifyDocuments::class, 'index'])->name('company.verify.documents.index');
        Route::post('verify-documents', [CompanyVerifyDocuments::class, 'store'])->name('company.verify.documents.store');
    });
});

Route::controller(MessengerController::class)->middleware('auth:user', 'verified')->group(function () {
    Route::get('/company/messages', 'companyMessages')->name('company.messages')->middleware('company');
    Route::get('/candidate/messages', 'candidateMessages')->name('candidate.messages')->middleware('candidate');
    Route::post('/company/message/candidate', 'messageSendCandidate')->name('company.message.candidate');
    Route::get('/get/messages/{username}', 'fetchMessages');
    Route::post('/send/message', 'sendMessage');
    Route::post('message/markas/read/{username}', 'messageMarkasRead')->name('message.markas.read');
    Route::get('/get/users', 'filterUsers');
    Route::get('/sync/user-list', 'syncUserList');
    Route::get('/load-unread-count', 'loadUnreadMessageCount')->name('load.unread.count');
});

// ======================================================================
// ===================Global & Artisan Command Routes====================
// ======================================================================
Route::controller(GlobalController::class)->group(function () {
    Route::get('/check/username/{name}', 'checkUsername');
    Route::get('/translated/texts', 'fetchCurrentTranslatedText');
    Route::get('/lang/{lang}', 'changeLanguage');
    Route::get('/migrate/data', 'migrateData');
    Route::get('/optimize-clear', 'optimizeClear')->name('app.optimize-clear');
    Route::post('/ckeditor/upload', 'ckeditorImageUpload')->name('ckeditor.upload');
});

Route::get('/{slug}', [PageController::class, 'showCustomPage'])->name('showCustomPage');
Route::controller(PayPalController::class)->group(function () {
    Route::post('paypal/payment', 'processTransaction')->name('paypal.post');
    Route::get('success-transaction', 'successTransaction')->name('paypal.successTransaction');
    Route::get('cancel-transaction', 'cancelTransaction')->name('paypal.cancelTransaction');
});
Route::get('/payment-from-app/{label}',[ApiCompanyController::class, 'payment']);
