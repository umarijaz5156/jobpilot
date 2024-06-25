<?php

use App\Models\Admin;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\IndustryType;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\OrganizationType;
use App\Models\TeamSize;
use App\Models\User;
use Database\Seeders\EducationSeeder;
use Database\Seeders\ExperienceSeeder;
use Database\Seeders\IndustryTypeSeeder;
use Database\Seeders\JobCategorySeeder;
use Database\Seeders\JobCategorySlugSeeder;
use Database\Seeders\JobRoleSeeder;
use Database\Seeders\JobSeeder;
use Database\Seeders\JobTypeSeeder;
use Database\Seeders\OrganizationTypeSeeder;
use Database\Seeders\ProfessionSeeder;
use Database\Seeders\SalaryTypeSeeder;
use Database\Seeders\TeamSizeSeeder;
use Illuminate\Support\Facades\DB;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostCategory;
use Modules\Plan\Entities\Plan;

it('gives back a successful response for the signup page', function () {
    $this->get(route('register'))
        ->assertSeeText('Create Account');
});

test('account create validation and redirect back to form', function () {
    $this->post(route('register'), [
        'name' => '',
        'email' => '',
        'password' => '',
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['name', 'email', 'password'])
        ->assertInvalid(['name', 'email', 'password']);

    $this->get(route('register'))->assertStatus(200);
});

test('account create unique validation redirect back to form', function () {
    $this->seed([
        JobRoleSeeder::class,
        ProfessionSeeder::class,
        ExperienceSeeder::class,
        EducationSeeder::class,
    ]);

    User::factory()->create(['email' => 'user@mail.com']);

    $this->post(route('register'), [
        'email' => 'user@mail.com',
    ])
        ->assertStatus(302);
});

test('user can create a account', function () {
    config(['captcha.active' => false]);

    $this->seed([
        JobRoleSeeder::class,
        ProfessionSeeder::class,
        ExperienceSeeder::class,
        EducationSeeder::class,
    ]);

    $user = [
        'name' => 'Ariful Islam Arif',
        'email' => 'devboyarif@gmail.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'candidate',
    ];

    $this->post(route('register'), $user)
        ->assertStatus(302);

    $lastUser = User::latest()->first();
    $this->assertDatabaseHas('users', ['name' => 'Ariful Islam Arif', 'email' => 'devboyarif@gmail.com']);

    $lastUser = User::latest()->first();
    expect($lastUser->name)->toBe($user['name']);
    expect($lastUser->email)->toBe($user['email']);
});

it('gives back a successful response for the login page', function () {
    $this->get(route('login'))->assertStatus(200);
});

test('user can login their own account', function () {
    $this->seed([
        IndustryTypeSeeder::class,
        OrganizationTypeSeeder::class,
        TeamSizeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
        ExperienceSeeder::class,
        EducationSeeder::class,
    ]);

    // Company login
    $company = Company::factory()->create();
    $this->post(route('login', [
        'email' => $company->user->email,
        'password' => 'password',
    ]))->assertStatus(302);

    // Candidate login
    $candidate = Candidate::factory()->create();
    $this->post(route('login', [
        'email' => $candidate->user->email,
        'password' => 'password',
    ]))->assertStatus(302);
});

it('gives back a successful response for the forget password page', function () {
    $this->get(route('password.request'))->assertStatus(200);
});

it('gives back a successful response for the reset password page', function () {
    $this->seed([
        JobRoleSeeder::class,
        ProfessionSeeder::class,
        ExperienceSeeder::class,
        EducationSeeder::class,
    ]);

    // Forget password request sent
    $token = uniqid();
    $email = 'devboyarif@gmail.com';

    $user = User::factory()->create(['email' => $email, 'role' => 'candidate']);

    $this->post(route('password.email', [
        'token' => $token,
        'email' => $email,
    ]))->assertStatus(302);

    // Reset password
    $this->post(route('password.update', [
        'token' => $token,
        'email' => $email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]))->assertStatus(302);
});

it('gives back a successful response for the home page', function () {
    $this->get(route('website.home'))->assertStatus(200);
});

it('gives back a successful response for the about page', function () {
    $this->get(route('website.about'))->assertStatus(200);
});

it('gives back a successful response for the contact page', function () {
    $this->get(route('website.contact'))->assertStatus(200);
});

it('gives back a successful response for the plan page', function () {
    $this->get(route('website.plan'))->assertStatus(200);
});

it('gives back a successful response for the plan details page', function () {
    $this->seed([
        TeamSizeSeeder::class,
        IndustryTypeSeeder::class,
        OrganizationTypeSeeder::class,
    ]);

    $plan = Plan::inRandomOrder()->first();
    $company = Company::factory()->create([
        'industry_type_id' => IndustryType::inRandomOrder()->first()->id,
        'team_size_id' => TeamSize::inRandomOrder()->first()->id,
        'organization_type_id' => OrganizationType::inRandomOrder()->first()->id,
    ]);
    $this->actingAs($company->user, 'user')->get(route('website.plan.details', $plan->label))->assertStatus(200);
});

it('gives back a successful response for the faq page', function () {
    $this->get(route('website.faq'))->assertStatus(200);
});

it('gives back a successful response for the termsCondition page', function () {
    $this->get(route('website.termsCondition'))->assertStatus(200);
});

it('gives back a successful response for the refundPolicy page', function () {
    $this->get(route('website.refundPolicy'))->assertStatus(200);
});

it('gives back a successful response for the job page', function () {
    $this->get(route('website.job'))
        ->assertOk()
        ->assertSeeText('Latest Jobs');
});

it('gives back a successful response for the active job details page', function () {
    $this->seed([
        JobTypeSeeder::class,
        JobCategorySeeder::class,
        JobRoleSeeder::class,
        ExperienceSeeder::class,
        EducationSeeder::class,
        SalaryTypeSeeder::class,
        IndustryTypeSeeder::class,
        OrganizationTypeSeeder::class,
        TeamSizeSeeder::class,
    ]);
    Company::factory()->create([
        'industry_type_id' => IndustryType::inRandomOrder()->first()->id, 'team_size_id' => TeamSize::inRandomOrder()->first()->id, 'organization_type_id' => OrganizationType::inRandomOrder()->first()->id,
    ]);
    $job = Job::factory()->create(['status' => 'active']);

    $this->get(route('website.job.details', $job->slug))->assertStatus(200);
});

it('gives back a successful response for the category jobs list page', function () {
    $category = JobCategory::factory()->create();

    $this->get(route('website.job.category.slug', $category->slug))
        ->assertOk()
        ->assertSeeText('Latest Jobs');
});

it('gives back a successful response for the candidate page', function () {
    $this->get(route('website.candidate'))->assertStatus(200);
});

it('gives back a successful response for the candidate details page', function () {
    $this->seed([
        JobRoleSeeder::class,
        ProfessionSeeder::class,
        ExperienceSeeder::class,
        EducationSeeder::class,
    ]);

    $candidate = Candidate::factory()->create();

    $this->get(route('website.candidate.application.profile.details', ['username' => $candidate->user->username]))->assertStatus(200);
    $this->get(route('website.candidate.details', $candidate->user->username))->assertStatus(200);
    $this->get(route('website.candidate.profile.details', $candidate->user->username))->assertStatus(200);
});

it('gives back a successful response for the company page', function () {
    $this->get(route('website.company'))->assertStatus(200);
});

it('gives back a successful response for the company details page', function () {
    $this->seed([
        IndustryTypeSeeder::class,
        OrganizationTypeSeeder::class,
        TeamSizeSeeder::class,
    ]);

    $company = Company::factory()->create([
        'industry_type_id' => IndustryType::inRandomOrder()->first()->id,
        'team_size_id' => TeamSize::inRandomOrder()->first()->id,
        'organization_type_id' => OrganizationType::inRandomOrder()->first()->id,
    ]);

    $this->get(route('website.employe.details', $company->user->username))->assertStatus(200);
});

it('gives back a successful response for the posts page', function () {
    $this->get(route('website.posts'))->assertStatus(200);
});

it('gives back a successful response for the post details page', function () {
    Admin::factory()->create();
    PostCategory::factory()->create();
    $post = Post::factory()->create();

    $this->get(route('website.posts', $post->slug))->assertStatus(200);
});

it('gives back a successful response for the post comment creation', function () {
    Admin::factory()->create();
    PostCategory::factory()->create();
    $post = Post::factory()->create();
    $candidate = Candidate::factory()->create();

    $this->actingAs($candidate->user, 'user')->get(route('website.comment', $post->slug))->assertStatus(200);
});

it('gives back a successful response for the company benefit creation', function () {
    $this->seed([
        IndustryTypeSeeder::class,
        OrganizationTypeSeeder::class,
        TeamSizeSeeder::class,
    ]);

    $company = Company::factory()->create([
        'industry_type_id' => IndustryType::inRandomOrder()->first()->id,
        'team_size_id' => TeamSize::inRandomOrder()->first()->id,
        'organization_type_id' => OrganizationType::inRandomOrder()->first()->id,
    ]);

    $this->actingAs($company->user, 'user')->get(route('website.job.benefit.create'))->assertStatus(200);
});

it('displays jobs with a category slug', function () {
    $this->seed([
        EducationSeeder::class,
        ExperienceSeeder::class,
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        SalaryTypeSeeder::class,
        TeamSizeSeeder::class,
        OrganizationTypeSeeder::class,
        ProfessionSeeder::class,
        IndustryTypeSeeder::class,
        JobCategorySeeder::class,
        JobCategorySlugSeeder::class,
    ]);

    Company::factory()->create([
        'industry_type_id' => IndustryType::inRandomOrder()->first()->id,
        'team_size_id' => TeamSize::inRandomOrder()->first()->id,
        'organization_type_id' => OrganizationType::inRandomOrder()->first()->id,
    ]);

    $this->seed([
        JobSeeder::class,
    ]);

    $category = DB::table('job_categories')->first();
    $this->get(route('website.job.category.slug', ['category' => $category->slug]))
        ->assertOk()
        ->assertSeeText('Latest Jobs');
});

it('displays jobs without a category slug', function () {
    $this->seed([
        EducationSeeder::class,
        ExperienceSeeder::class,
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        SalaryTypeSeeder::class,
        TeamSizeSeeder::class,
        OrganizationTypeSeeder::class,
        ProfessionSeeder::class,
        IndustryTypeSeeder::class,
        JobCategorySeeder::class,
        JobCategorySlugSeeder::class,
    ]);

    Company::factory()->create([
        'industry_type_id' => IndustryType::inRandomOrder()->first()->id,
        'team_size_id' => TeamSize::inRandomOrder()->first()->id,
        'organization_type_id' => OrganizationType::inRandomOrder()->first()->id,
    ]);

    $this->seed([
        JobSeeder::class,
    ]);

    $response = $this->get(route('website.job'))
        ->assertStatus(200)
        ->assertViewIs('frontend.pages.jobs');

    $jobCount = $response->viewData('jobs')->count();
    $this->assertTrue($jobCount > 0);
});
