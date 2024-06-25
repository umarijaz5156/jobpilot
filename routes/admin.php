<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AffiliateSettingsController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\BenefitController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\CandidateLanguageController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\IndustryTypeController;
use App\Http\Controllers\Admin\JobCategoryController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\JobRoleController;
use App\Http\Controllers\Admin\JobTypeController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrganizationTypeController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProfessionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SalaryTypeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SocialiteController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TeamSizeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\SearchCountryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\Website\WebsiteSettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    /**
     * Auth routes
     */
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.admin');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login');
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

    Route::middleware(['guest:admin'])->group(function () {
        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');
        Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');
        Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('admin.password.update');
        Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('admin.password.reset');
    });

    Route::middleware(['auth:admin'])->group(function () {
        //Dashboard Route
        Route::get('/', [AdminController::class, 'dashboard']);
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Notification Route
        Route::post('/notifications/read', [AdminController::class, 'notificationRead'])->name('admin.notification.read');
        Route::get('/notifications', [AdminController::class, 'allNotifications'])->name('admin.all.notification');

        // Roles Route
        Route::resource('role', RolesController::class);

        //Users Route
        Route::resource('user', UserController::class)->only(['dashboard', 'index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::get('/company/{company}/documents', [CompanyController::class, 'documents'])->name('admin.company.documents');
        Route::get('/company/{company}/documents/change', [CompanyController::class, 'toggle'])->name('admin.document.verify.change');

        Route::post('/company/{company}/documents', [CompanyController::class, 'downloadDocument'])->name('company.verify.documents.download');
        //Company Route resource

        Route::resource('company', CompanyController::class);
        Route::get('/company/change/status', [CompanyController::class, 'statusChange'])->name('company.status.change');
        Route::get('/company/verify/status', [CompanyController::class, 'verificationChange'])->name('company.verify.change');
        Route::get('/company/profile/verify/status', [CompanyController::class, 'profileVerificationChange'])->name('company.profile.verify.change');

        // Candidate Route
        Route::resource('candidate', CandidateController::class);
        Route::get('/candidate/change/status', [CandidateController::class, 'statusChange'])->name('candidate.status.change');
        Route::get('/candidate/export/{type}', [CandidateController::class, 'candidateExport'])->name('candidate.export');

        //JobCategory Route resource
        Route::resource('jobCategory', JobCategoryController::class)->except('show');
        Route::post('/job/category/bulk/import', [JobCategoryController::class, 'bulkImport'])->name('admin.job.category.bulk.import');

        //job Route resource
        Route::resource('job', JobController::class);
        Route::get('/jobs/delete-selected', [JobController::class, 'deleteSelected'])->name('jobs.deleteSelected');
        Route::get('applied/jobs', [JobController::class, 'appliedJobs'])->name('applied.jobs');
        Route::get('applied/jobs/{applied_job}', [JobController::class, 'appliedJobsShow'])->name('applied.job.show');
        Route::post('/job/bulk/import', [JobController::class, 'bulkImport'])->name('admin.job.bulk.import');
        Route::put('job/change/status/{job}', [JobController::class, 'jobStatusChange'])->name('admin.job.status.change');
        Route::get('job/clone/{job:slug}', [JobController::class, 'clone'])->name('admin.job.clone');
        Route::get('edited/job/list', [JobController::class, 'editedJobList'])->name('admin.job.edited.index');
        Route::get('edited/job/show/{job:slug}', [JobController::class, 'editedShow'])->name('admin.job.edited.show');
        Route::put('edited/job/approved/{job:slug}', [JobController::class, 'editedApproved'])->name('admin.job.edited.approved');

        // job role route resource
        Route::resource('jobRole', JobRoleController::class)->except('show', 'create');
        Route::post('/job/role/bulk/import', [JobRoleController::class, 'bulkImport'])->name('admin.job.role.bulk.import');

        // industry type route resource
        Route::resource('industryType', IndustryTypeController::class)->except('show', 'create');
        Route::post('/industry/type/bulk/import', [IndustryTypeController::class, 'bulkImport'])->name('admin.industry.type.bulk.import');

        // Organization Type route resource
        Route::resource('organizationType', OrganizationTypeController::class)->except('show', 'create');
        Route::post('/organization/type/bulk/import', [OrganizationTypeController::class, 'bulkImport'])->name('admin.organization.type.bulk.import');

        // Salary Type  route resource
        Route::resource('salaryType', SalaryTypeController::class)->except('show', 'create');
        Route::post('/salary/type/bulk/import', [SalaryTypeController::class, 'bulkImport'])->name('admin.salary.type.bulk.import');

        // profession route resource
        Route::resource('profession', ProfessionController::class)->except('show', 'create');
        Route::post('/profession/bulk/import', [ProfessionController::class, 'bulkImport'])->name('admin.profession.bulk.import');

        // skills route resource
        Route::resource('skill', SkillController::class)->except('show', 'create');
        Route::post('/skill/bulk/import', [SkillController::class, 'bulkImport'])->name('admin.skill.bulk.import');

        // benefit route resource
        Route::resource('benefit', BenefitController::class)->except('show', 'create');

        //  education route resource
        Route::resource('education', EducationController::class)->except('show', 'create');

        //  experience route resource
        Route::resource('experience', ExperienceController::class)->except('show', 'create');

        //  team size route resource
        Route::resource('teamSize', TeamSizeController::class)->except('show', 'create');

        //  job type route resource
        Route::resource('jobType', JobTypeController::class)->except('show', 'create');

        // tags route resource
        Route::resource('tags', TagController::class);
        Route::post('tags/status/change/{tag}', [TagController::class, 'statusChange'])->name('tags.status.change');
        Route::post('/tags/bulk/import', [TagController::class, 'bulkImport'])->name('admin.tags.bulk.import');

        // menu settings
        Route::post('menu-settings/status-update/{menuSetting}', [MenuController::class, 'statusChange'])->name('menu-setting.status.change');
        Route::resource('settings/menu-settings', MenuController::class);
        Route::post('settings/menu-settings/sort', [MenuController::class, 'sortAble'])->name('menu-setting.sort-able');

        // About Page
        Route::controller(CmsController::class)->group(function () {
            Route::get('settings/delete/about/logo/{name}', 'aboutLogoDelete')->name('settings.aboutLogo.delete');
            Route::get('settings/delete/payment/logo/{name}', 'paymentLogoDelete')->name('settings.paymentLogo.delete');
            Route::put('settings/about', 'aboutupdate')->name('settings.aboutupdate');
            Route::put('settings/payments', 'paymentupdate')->name('settings.paymentupdate');
            Route::put('settings/others', 'othersupdate')->name('settings.others.update');
            Route::put('settings/home', 'home')->name('settings.home.update');
            Route::put('settings/auth', 'auth')->name('settings.auth.update');
            Route::put('settings/faq', 'faq')->name('settings.faq.update');
            Route::put('settings/errorpages', 'updateErrorPages')->name('settings.errorpage.update');
            Route::put('settings/comingsoon', 'comingsoon')->name('settings.comingsoon.update');
            Route::put('settings/account/complete/update', 'accountCompleteUpdate')->name('settings.account.complate.update');
            Route::put('settings/maintenance/mode/update', 'maintenanceModeUpdate')->name('settings.maintenance.mode.update');
        });

        //Dashboard Route
        Route::controller(AdminController::class)->group(function () {
            Route::get('/', 'dashboard');
            Route::get('/dashboard', 'dashboard')->name('admin.dashboard');
            Route::post('/admin/search', 'search')->name('admin.search');
            Route::post('/admin/download/transaction/invoice/{transaction}', 'downloadTransactionInvoice')->name('admin.transaction.invoice.download');
            Route::post('/view/transaction/invoice/{transaction}', 'viewTransactionInvoice')->name('admin.transaction.invoice.view');
        });

        //Profile Route
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile/settings', 'setting')->name('profile.setting');
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/profile', 'profile_update')->name('profile.update');
        });

        // Order Route
        Route::controller(OrderController::class)->group(function () {
            Route::get('/orders', 'index')->name('order.index');
            Route::get('/order/create', 'create')->name('order.create');
            Route::post('/order/store', 'store')->name('order.store');
            Route::get('/orders/{id}', 'show')->name('order.show');

            Route::get('/order/user/plan/{earning}', 'updateUserPlan')->name('order.user.plan.update');
            Route::put('/user/plan/update/{user}', 'UserPlanUpdate')->name('user.plan.update');
        });

        // ========================================================
        // ====================Setting=============================
        // ========================================================

        // Website Setting Route
        Route::put('settings/terms/conditions/update', [CmsController::class, 'termsConditionsUpdate'])->name('admin.privacy.terms.update');
        Route::controller(WebsiteSettingController::class)
            ->prefix('settings')
            ->name('settings.')
            ->group(function () {
                Route::get('/websitesetting', 'website_setting')->name('websitesetting');
                Route::post('/session/terms-privacy', 'sessionUpdateTermsPrivacy')->name('session.update.tems-privacy');
                Route::delete('/cms/content', 'cmsContentDestroy')->name('cms.content.destroy');
            });

        // Admin Setting Route
        Route::controller(SettingsController::class)
            ->prefix('settings')
            ->name('settings.')
            ->group(function () {
                Route::get('general', 'general')->name('general');
                Route::put('general', 'generalUpdate')->name('general.update');
                Route::put('preference', 'preferenceUpdate')->name('preference.update');
                Route::get('layout', 'layout')->name('layout');
                Route::put('layout', 'layoutUpdate')->name('layout.update');
                Route::put('mode', 'modeUpdate')->name('mode.update');
                Route::get('theme', 'theme')->name('theme');
                Route::put('theme', 'colorUpdate')->name('theme.update');
                Route::get('custom', 'custom')->name('custom');
                Route::put('custom', 'custumCSSJSUpdate')->name('custom.update');
                Route::get('email', 'email')->name('email');
                Route::put('email', 'emailUpdate')->name('email.update');
                Route::post('test-email', 'testEmailSent')->name('email.test');

                // system update
                Route::get('system', 'system')->name('system');
                Route::put('system/update', 'systemUpdate')->name('system.update');
                Route::put('system/mode/update', 'systemModeUpdate')->name('system.mode.update');
                Route::put('system/jobdeadline/update', 'systemJobdeadlineUpdate')->name('system.jobdeadline.update');

                // system update end
                Route::put('search/indexing', 'searchIndexing')->name('search.indexing');
                Route::put('google-analytics', 'googleAnalytics')->name('google.analytics');
                Route::put('allowLangChanging', 'allowLaguageChanage')->name('allow.langChange');
                Route::put('change/timezone', 'timezone')->name('change.timezone');

                // cookies routes
                Route::get('cookies', 'cookies')->name('cookies');
                Route::put('cookies/update', 'cookiesUpdate')->name('cookies.update');

                // seo
                Route::get('seo/index', 'seoIndex')->name('seo.index');
                Route::get('seo/edit/{page}', 'seoEdit')->name('seo.edit');
                Route::put('seo/update/{content}', 'seoUpdate')->name('seo.update');
                Route::get('generate/sitemap', 'generateSitemap')->name('generateSitemap');

                // database backup end
                Route::put('working-process/update', 'workingProcessUpdate')->name('working.process.update');

                // pwa option Update
                Route::put('pwa/update', 'pwaUpdate')->name('pwa.update');

                // recaptcha Update
                Route::put('recaptcha/update', 'recaptchaUpdate')->name('recaptcha.update');

                // pusher Update
                Route::put('pusher/update', 'pusherUpdate')->name('pusher.update');

                // analytics Update
                Route::put('analytics/update', 'analyticsUpdate')->name('analytics.update');

                // payperjob Update
                Route::put('payperjob/update', 'payperjobUpdate')->name('payperjob.update');

                // upgrade application
                Route::get('upgrade', 'upgrade')->name('upgrade');
                Route::post('upgrade/apply', 'upgradeApply')->name('upgrade.apply');

                // systemInfo
                Route::get('/system/info', 'systemInfo')->name('systemInfo');

                // landing page
                Route::put('landing-page', 'landingPageUpdate')->name('landingPage.update');

                Route::get('/system/ad_setting', 'ad_setting')->name('ad_setting');
                Route::put('/update_ad_info', 'update_ad_info')->name('adinfo.update');
                Route::put('/update_ad_status', 'update_ad_status')->name('adstatus.update');
            });

        // Affiliate Settings Route
        Route::controller(AffiliateSettingsController::class)
            ->prefix('settings/affiliate')
            ->name('settings.')
            ->group(function () {
                Route::get('/', 'index')->name('affiliate.index');
                Route::put('careerjet/update', 'careerjetUpdate')->name('careerjet.update');
                Route::put('indeed/update', 'indeedUpdate')->name('indeed.update');
                Route::post('set/default/affiliate', 'setDefaultJob')->name('affiliate.default');
            });

        // Email Template Route
        Route::group(['prefix' => 'settings/email-templates', 'as' => 'settings.email-templates.'], function () {
            Route::get('/', [EmailTemplateController::class, 'index'])->name('list');
            Route::post('/save', [EmailTemplateController::class, 'save'])->name('save');
        });

        Route::controller(PageController::class)->prefix('settings/pages')->name('settings.')->group(function () {
            Route::get('/', 'index')->name('pages.index');
            Route::get('/create', 'create')->name('pages.create');
            Route::post('/create', 'store')->name('pages.store');
            Route::get('/edit/{page}', 'edit')->name('pages.edit');
            Route::put('/update/{page}', 'update')->name('pages.update');
            Route::delete('/delete/{page}', 'delete')->name('pages.delete');
            Route::get('/status/showinheader', 'changeShowInheader')->name('pages.header.status');
            Route::get('/status/showinfooter', 'changeShowInFooter')->name('pages.footer.status');
        });
        // Socialite Route
        Route::controller(SocialiteController::class)->group(function () {
            Route::get('settings/social-login', 'index')->name('settings.social.login');
            Route::put('settings/social-login', 'update')->name('settings.social.login.update');
            Route::post('settings/social-login/status', 'updateStatus')->name('settings.social.login.status.update');
        });

        // Payment Route
        Route::controller(PaymentController::class)
            ->prefix('settings/payment')
            ->name('settings.')
            ->group(function () {
                // Automatic Payment
                Route::get('/auto', 'autoPayment')->name('payment');
                Route::put('/', 'update')->name('payment.update');

                // Manual Payment
                Route::get('/manual', 'manualPayment')->name('payment.manual');
                Route::post('/manual/store', 'manualPaymentStore')->name('payment.manual.store');
                Route::get('/manual/{manual_payment}/edit', 'manualPaymentEdit')->name('payment.manual.edit');
                Route::put('/manual/{manual_payment}/update', 'manualPaymentUpdate')->name('payment.manual.update');
                Route::delete('/manual/{manual_payment}/delete', 'manualPaymentDelete')->name('payment.manual.delete');
                Route::get('/manual/status/change', 'manualPaymentStatus')->name('payment.manual.status');
            });

        // candidate language
        Route::resource('candidate/language/index', CandidateLanguageController::class, ['names' => 'admin.candidate.language']);
        Route::controller(SearchCountryController::class)->prefix('settings/location/country')->name('location.country.')->group(function () {
            Route::get('/', 'index')->name('country');
            Route::get('/add', 'create')->name('create');
            Route::post('/add', 'store')->name('store');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/edit/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'destroy')->name('destroy');
        });
        Route::controller(StateController::class)->prefix('settings/location/state')->name('location.state.')->group(function () {
            Route::get('/', 'index')->name('state');
            Route::get('/add', 'create')->name('create');
            Route::post('/add', 'store')->name('store');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/edit/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'destroy')->name('destroy');
        });
        Route::controller(CityController::class)->prefix('settings/location/city')->name('location.city.')->group(function () {
            Route::get('/', 'index')->name('city');
            Route::get('/add', 'create')->name('create');
            Route::post('/add', 'store')->name('store');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/edit/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'destroy')->name('destroy');
        });
    });
});
