<?php

use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\EducationSeeder;
use Database\Seeders\ExperienceSeeder;
use Database\Seeders\IndustryTypeSeeder;
use Database\Seeders\JobCategorySeeder;
use Database\Seeders\JobRoleSeeder;
use Database\Seeders\JobSeeder;
use Database\Seeders\JobTypeSeeder;
use Database\Seeders\OrganizationTypeSeeder;
use Database\Seeders\ProfessionSeeder;
use Database\Seeders\SalaryTypeSeeder;
use Database\Seeders\TeamSizeSeeder;

it('displays the popup modal for unauthorized users', function () {
    $this->get(route('website.candidate'))
        ->assertStatus(200)
        ->assertSee('Performing this action requires logging into your account. Would you like to log in now');
});

it('displays error message for unauthorized users trying to access a protected page', function () {
    $this->get(route('user.dashboard'))->assertStatus(302);
});

it('redirects to the login page after clicking the login button', function () {
    $this->get(route('website.candidate'))
        ->assertStatus(200)
        ->assertSee('Yes, I want to login');
    $this->seed([JobRoleSeeder::class, ProfessionSeeder::class, ExperienceSeeder::class, EducationSeeder::class]);

    config(['captcha.active' => false]);

    $user = User::factory()->create(['role' => 'candidate']);
    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('user.dashboard'));

    config(['captcha.active' => true]);
});

it('displays job view page job filter modal', function () {
    $this->get(route('website.job'))
        ->assertOK()
        ->assertSeeText('Category')
        ->assertSeeText('Job Type')
        ->assertSeeText('Salary');
});

it('displays candidate resume modal ', function () {
    $this->seed([
        IndustryTypeSeeder::class,
        OrganizationTypeSeeder::class,
        TeamSizeSeeder::class,
    ]);
    $user = User::factory()->create(['role' => 'company']);
    $this->actingAs($user);
    $this->get(route('website.candidate'))->assertOK()->assertSeeText('About Me');

});

it('displays candidate resume kanbanboard modal', function () {
    $this->seed([IndustryTypeSeeder::class, OrganizationTypeSeeder::class, TeamSizeSeeder::class, EducationSeeder::class, ExperienceSeeder::class, CompanySeeder::class, JobCategorySeeder::class, JobTypeSeeder::class, SalaryTypeSeeder::class, JobRoleSeeder::class, JobSeeder::class]);
    $user = User::where('role', 'company')->first();
    $jobs = Job::where('company_id', $user->id)->get();
    foreach ($jobs as $job) {
        $this->actingAs($user)
            ->get(route('company.job.application', ['job' => $job->id]))
            ->assertOk()
            ->assertSeeText('About Me');
    }
});

it('displays apply job modal', function () {
    Company::factory()->create();
    $this->seed([
        IndustryTypeSeeder::class,
        OrganizationTypeSeeder::class,
        TeamSizeSeeder::class,
        ProfessionSeeder::class,
        EducationSeeder::class,
        ExperienceSeeder::class,
        CompanySeeder::class,
        JobCategorySeeder::class,
        JobTypeSeeder::class,
        SalaryTypeSeeder::class,
        JobRoleSeeder::class,
        JobSeeder::class,
    ]);
    $user = User::factory()->create(['role' => 'candidate']);
    $job = Job::find(1);

    $this->actingAs($user)
        ->get(route('website.job.details', $job->slug))
        ->assertOk();

});

it('displays add candidate resume modal', function () {
    $this->seed([
        ProfessionSeeder::class, EducationSeeder::class, ExperienceSeeder::class, JobRoleSeeder::class,
    ]);
    $user = User::factory()->create(['role' => 'candidate']);
    $this->actingAs($user)
        ->get(route('candidate.setting'))
        ->assertOk()->assertSeeText('Add a CV/Resume');

});

it('displays add candidate experience modal', function () {
    $this->seed([
        ProfessionSeeder::class, EducationSeeder::class, ExperienceSeeder::class, JobRoleSeeder::class,
    ]);
    $user = User::factory()->create(['role' => 'candidate']);
    $this->actingAs($user)
        ->get(route('candidate.setting'))
        ->assertOk()->assertSeeText('Add Experience');

});

it('displays add candidate education modal', function () {
    $this->seed([
        ProfessionSeeder::class, EducationSeeder::class, ExperienceSeeder::class, JobRoleSeeder::class,
    ]);
    $user = User::factory()->create(['role' => 'candidate']);
    $this->actingAs($user)
        ->get(route('candidate.setting'))
        ->assertOk()->assertSeeText('Add Education');

});
