<?php

use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\CandidateJobsController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyJobsController;
use App\Http\Controllers\Api\LocalizationController;
use App\Http\Controllers\Api\SocialAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebsiteController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(WebsiteController::class)->group(function () {
    Route::get('/home', 'home');
    Route::get('/about', 'about');
    Route::get('/jobs', 'jobs');
    Route::get('/jobs/{job:slug}', 'jobDetails');
    Route::get('/candidates', 'candidates');
    Route::get('/candidates/{username}', 'candidateDetails');
    Route::get('/companies', 'companies');
    Route::get('/companies/{username}', 'companyDetails');
    Route::get('/posts', 'posts')->name('posts');
    Route::get('/post/{post:slug}', 'postDetails');
    Route::post('/comment/{post:slug}/store', 'comment')->middleware('auth:sanctum');
    Route::get('/pricing', 'pricing');
    Route::get('/faq', 'faq');
    Route::get('/terms-condition', 'termsCondition');
    Route::get('/privacy-policy', 'privacyPolicy');
    Route::get('/refund-policy', 'refundPolicy');
    Route::post('/contact', 'contact');
    Route::get('/page-list', 'pageList');
});

Route::post('/social-login', [SocialAuthController::class, 'socialLogin']);
Route::get('/localizations/{locale}/strings', [LocalizationController::class, 'getStrings']);
Route::controller(AttributeController::class)->group(function () {
    Route::get('/languages', 'languageList');
    Route::get('/change-language/{code}', 'changeLanguage');
    Route::get('/language/translations', 'fetchTranslations');
    // Route::get('/default/attributes', 'defaultAttributes');
    Route::get('/countries', 'countries');
    Route::get('/change-country/{countryId}', 'changeCountry');
    Route::get('/currencies', 'currencies');
    Route::get('/change-currency/{code}', 'changeCurrency');
    Route::get('/categories', 'categories');
    Route::get('/job-roles', 'job_roles');
    Route::get('/experiences', 'experiences');
    Route::get('/educations', 'educations');
    Route::get('/job-types', 'job_types');
    Route::get('/popular-tags', 'popular_tags');
    Route::get('/professions', 'professions');
    Route::get('/current-session', 'currentSession');
});

// Authentication Apis
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::get('/profile', 'profile')->middleware('auth:sanctum');
    // Password Reset Emails
    Route::post('/password/email', 'sendResetCodeEmail');
    Route::post('/password/reset', 'reset');
    Route::get('/user-info', 'getUserInfo');
});

// Cadidate and Company common apis
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(WebsiteController::class)->group(function () {
        Route::get('/notifications', 'getAllNotification');
    });

});

//  Candidate Apis
Route::middleware('auth:sanctum')->prefix('candidate')->group(function () {
    Route::controller(WebsiteController::class)->group(function () {
        Route::get('/jobs', 'ifLoggedinCadidateJobs');
        Route::get('/jobs/{job:slug}', 'jobDetails');
    });
    //  Candidate Dashboard Apis
    Route::controller(CandidateController::class)->group(function () {
        Route::get('/', 'candidate');
        Route::get('/dashboard', 'dashboard');
        Route::get('/settings', 'fetchSettings');
        Route::post('/settings', 'updateSettings');
        // Route::delete('/settings', 'deleteSettings');
        Route::get('/resumes', 'getResumes');
        Route::get('/resumes/{id}', 'getResumeById');
        Route::post('/upload-resume', 'uploadResume');
        Route::post('/update-resume/{id}', 'updateResume');
        Route::delete('/delete-resume/{id}', 'deleteResume');
    });
    //  Candidate Jobs Apis
    Route::controller(CandidateJobsController::class)->group(function () {
        Route::get('/applied-jobs', 'appliedjobs');
        Route::get('/favorite-jobs', 'favoritejobs');
        Route::post('/jobs/{job}/bookmark', 'bookmarkedJob');
        Route::post('/jobs/apply', 'jobApply');
        Route::get('/job-alert', 'jobAlerts');
    });
});

//  company Apis
Route::middleware(['auth:sanctum', 'api_company', 'api_has_plan'])->prefix('company')->group(function () {
    //  company Dashboard Apis
    Route::controller(CompanyController::class)->group(function () {
        Route::get('/', 'company')->withoutMiddleware('api_has_plan');
        Route::get('/dashboard', 'dashboard')->withoutMiddleware('api_has_plan');
        Route::get('/plan', 'plan')->withoutMiddleware('api_has_plan');
        Route::get('/plan-limit', 'planLimit')->withoutMiddleware('api_has_plan');
        Route::post('/bookmark/candidate', 'bookmarkCandidate');
        Route::get('/bookmark/candidates', 'fetchBookmarkCandidates');
        Route::get('/bookmark/categories', 'fetchBookmarkCategories');
        Route::post('/bookmark/categories', 'storeBookmarkCategories');
        Route::get('/bookmark/categories/{category}/edit', 'editBookmarkCategories');
        Route::put('/bookmark/categories/{category}', 'updateBookmarkCategories');
        Route::delete('/bookmark/categories/{category}', 'deleteBookmarkCategories');
        Route::get('/create/job', 'createJob');
        Route::post('/store/job', 'storeJob');
        Route::get('/edit/{job:slug}/job', 'editJob');
        Route::put('/update/{job:slug}/job', 'updateJob');
        Route::post('/promote/job', 'promoteJob');
        Route::post('/clone/job', 'cloneJob');
        Route::post('/change-status/job', 'changeJobStatus');
        Route::get('/application/group', 'fetchApplicationGroup');
        Route::post('/application/group', 'storeApplicationGroup');
        Route::put('/application/group/{group}', 'updateApplicationGroup');
        Route::delete('/application/group/{group}', 'deleteApplicationGroup');
        Route::get('/account-progress', 'fetchAccountProgress')->withoutMiddleware('api_has_plan');
        Route::post('/account-progress', 'submitAccountProgress')->withoutMiddleware('api_has_plan');
        Route::get('/download-invoice/{id}', 'downloadInvoice');
        Route::get('/social-links', 'getSocialLinks')->withoutMiddleware('api_has_plan');
    });

    Route::controller(CompanyJobsController::class)->prefix('job')->group(function () {
        Route::get('/', 'getJobs');
        Route::get('/{id}/applications', 'applications');
        Route::post('/applications/{id}/group-update', 'applicationGroupUpdate');
        Route::get('/download-cv/{id}', 'downloadCv');
    });

});
