<?php

use App\Models\Company;
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
use App\Models\Profession;
use App\Models\SalaryType;
use App\Models\TeamSize;
use Modules\Plan\Entities\Plan;

beforeEach(function () {
    IndustryType::factory()->create();
    OrganizationType::factory()->create();
    JobCategory::factory()->create();
    JobRole::factory()->create();
    Experience::factory()->create();
    Education::factory()->create();
    JobType::factory()->create();
    SalaryType::factory()->create();
    Profession::query()->create();
    TeamSize::factory()->create();
    Plan::factory()->create();
    $company = Company::factory()->create();
    $this->companyUser = $company->user;
    $plan = Plan::first();
    $company->userPlan()->create([
        'plan_id' => $plan->id,
        'job_limit' => $plan->job_limit,
        'featured_job_limit' => $plan->featured_job_limit,
        'highlight_job_limit' => $plan->highlight_job_limit,
        'candidate_cv_view_limit' => $plan->candidate_cv_view_limit,
        'candidate_cv_view_limitation' => $plan->candidate_cv_view_limitation,
    ]);

    config(['captcha.active' => false]);
});

test('can visit dashboard page', function () {
    actingAs($this->companyUser)
        ->get(route('company.dashboard'))
        ->assertOk()
        ->assertSee('Hello, '.$this->companyUser->name);
});

it('shows number of jobs', function () {

    actingAs($this->companyUser)
        ->get(route('company.dashboard'))
        ->assertSee('Open Job');
});

it('can show company profile page', function () {

    actingAs($this->companyUser)
        ->get(route('website.employe.details', $this->companyUser->username))
        ->assertOk()
        ->assertSee('Company Description');
});

it('just created profile in unverified', function () {

    $this->actingAs($this->companyUser);
    expect($this->companyUser->company->is_profile_verified)->toBeFalse();
});

it('lists jobs created by the company', function () {

    $myJob = Job::factory()->create([
        'company_id' => $this->companyUser->company->id,
    ]);

    actingAs($this->companyUser)->get(route('company.myjob'))
        ->assertOk()
        ->assertSee($myJob->title);
});

it('can update accounts settings', function () {
    actingAs($this->companyUser)->put(route('company.settingUpdateInformation'), [
        'name' => 'new name',
        'type' => 'personal',
    ]);

    expect($this->companyUser->fresh()->name)
        ->toBe('new name');
});

it('can update profile info', function () {

    actingAs($this->companyUser)->put(route('company.settingUpdateInformation'), [
        'organization_type' => 1,
        'industry_type' => 1,
        'team_size' => 1,
        'type' => 'profile',
    ]);

    expect($this->companyUser->fresh()
        ->company->organization->id)
        ->toBe(1);
});

// it('can change username', function () {
//     actingAs($this->companyUser)->post(route('company.username.change'),[
//         'username' => 'newusername',
//         'type' => 'company_username',
//     ]);
//     expect($this->companyUser->fresh()->username)
//         ->toBe('newusername');
// });

test('company can have any active plan', function () {
    actingAs($this->companyUser);
    expect($this->companyUser->company->userPlan)
        ->not()
        ->toBeNull();
});

test('after register a company profile progress is zero', function () {
    $attr = [
        'role' => 'company',
        'name' => 'Jon Doe',
        'email' => 'foo@bar.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $this->assertGuest();
    $this->post(route('register'), $attr);
    $this->assertAuthenticated();

    $authenticatedUser = auth()->user();
    expect($authenticatedUser->company->profile_completion)->toBe(false);

    $this->get(route('company.account-progress'))->assertSee('0%');

});

test('shows profile 25 % complete in profile tab', function () {
    $attr = [
        'role' => 'company',
        'name' => 'Jon Doe',
        'email' => 'foo@bar.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $this->assertGuest();
    $this->post(route('register'), $attr);
    $this->assertAuthenticated();

    $authenticatedUser = auth()->user();
    expect($authenticatedUser->company->profile_completion)->toBe(false);

    $this->get(route('company.account-progress').'?profile')
        ->assertSee('25%');

});

test('shows profile 50 % complete in social tab', function () {
    $attr = [
        'role' => 'company',
        'name' => 'Jon Doe',
        'email' => 'foo@bar.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $this->assertGuest();
    $this->post(route('register'), $attr);
    $this->assertAuthenticated();

    $authenticatedUser = auth()->user();
    expect($authenticatedUser->company->profile_completion)->toBe(false);

    $this->get(route('company.account-progress').'?social')
        ->assertSee('50%');
});

test('shows profile 100 % complete after contact submit', function () {
    $attr = [
        'role' => 'company',
        'name' => 'Jon Doe',
        'email' => 'foo@bar.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $this->assertGuest();
    $this->post(route('register'), $attr);
    $this->assertAuthenticated();

    $authenticatedUser = auth()->user();
    expect($authenticatedUser->company->profile_completion)->toBe(false);

    session()->put('location', [
        'lat' => '23.757853442383',
        'lng' => '90.411270491741',
        'country' => 'Bangladesh',
        'region' => 'Dhaka Division',
        'district' => 'Dhaka District',
        'place' => 'Dhaka',
        'exact_location' => 'Dhaka District,Dhaka Division,Bangladesh',
    ]);

    $this->put(route('company.profile.complete', $authenticatedUser->company->id), [
        'field' => 'contact',
        'email' => 'demo@mail.com',
        'phone' => '01539542041',
    ]);

    expect($authenticatedUser->company->fresh()->profile_completion)
        ->toBe(true);
});

it('shows purchase plan page', function () {
    $plan = Plan::first();
    actingAs($this->companyUser)
        ->get(route('website.plan', $plan->label))
        ->assertSee($plan->label)
        ->assertOk();
});

it('purchase plan manual', function () {
    $manualPayment = ManualPayment::query()->create([
        'type' => 'bank_payment',
        'name' => 'Bank',
        'description' => 'Payment made by bank',
    ]);

    $plan = Plan::query()->latest()->first();
    expect(Earning::count())->toBe(0);

    actingAs($this->companyUser)
        ->post(route('manual.payment', [
            'plan_id' => $plan->id,
            'payment_id' => $manualPayment->id,
        ]));

    expect(Earning::count())->toBe(1)
        ->and(Earning::first()->payment_status)
        ->toBe('unpaid');
});

test('show candidate list', function () {
    $this->get(route('website.candidate'))
        ->assertStatus(200)
        ->assertViewIs('frontend.pages.candidates');
});

test('candidate details', function () {
    $response = $this->get(route('website.candidate.profile.details'));

    $response->assertStatus(200);
});

// test('candidate bookmark category', function () {
//     $response = $this->get('company.bookmark.category.index');

//     $response->assertSeeText('Bookmark Category');
// });

// test('candidate bookmark', function () {

//     Candidate::factory()->create();

//     $candidate = Candidate::first();

//     $response = $this->post('company.companybookmarkcandidate', ([$candidate]));

//     $response->assertStatus(302);
// });
