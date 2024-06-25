<?php

use App\Models\Admin;
use App\Models\Company;
use App\Models\Education;
use App\Models\Experience;
use App\Models\IndustryType;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobRole;
use App\Models\JobType;
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
    $plan = Plan::first();

    $this->companyUser = $company->user;
    $company->userPlan()->create([
        'plan_id' => $plan->id,
        'job_limit' => $plan->job_limit,
        'featured_job_limit' => $plan->featured_job_limit,
        'highlight_job_limit' => $plan->highlight_job_limit,
        'candidate_cv_view_limit' => $plan->candidate_cv_view_limit,
        'candidate_cv_view_limitation' => $plan->candidate_cv_view_limitation,
    ]);
});

test('admin can create job with just company name', function () {
    $this->withoutExceptionHandling();

    $admin = Admin::factory()->create();
    $admin->assignRole('superadmin');
    $params = [
        'title' => 'Test Title',
        'company_id' => null,
        'company_name' => 'Test Company Name',
        'category_id' => '2',
        'vacancies' => '2',
        'deadline' => '2023-09-07',
        'salary_mode' => 'range',
        'min_salary' => '20',
        'max_salary' => '10000',
        'custom_salary' => 'Competitive',
        'salary_type' => '1',
        'apply_on' => 'app',
        'apply_email' => null,
        'apply_url' => null,
        'badge' => 'featured',
        'experience' => '4',
        'role_id' => '2',
        'tags' => [],
        'benefits' => [],
        'skills' => [],
        'education' => '2',
        'job_type' => '1',
        'description' => '<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Maiores iusto error veniam deserunt esse debitis ipsa, iure labore! Autem laudantium cupiditate odio placeat quisquam reprehenderit, velit quidem repudiandae vero perspiciatis quas accusantium dolor praesentium eligendi quo ratione deleniti repellendus pariatur, consequuntur ab sequi tempore nostrum! Eaque quasi culpa repellendus, ullam officia voluptatum dicta consectetur, sed, nobis maiores magni nihil dignissimos dolore quam deleniti quaerat enim inventore. Quas dolore assumenda ipsa eveniet eum iste deleniti minima ipsam commodi, autem quam aut delectus, aliquid explicabo earum magnam nesciunt temporibus est ducimus, consequuntur totam natus. Nulla veritatis adipisci suscipit eum at ipsam blanditiis.</p>',
    ];

    session()->put('location', [
        'lat' => '23.757853442383',
        'lng' => '90.411270491741',
        'country' => 'Bangladesh',
        'region' => 'Dhaka Division',
        'district' => 'Dhaka District',
        'place' => 'Dhaka',
        'exact_location' => 'Dhaka District,Dhaka Division,Bangladesh',
    ]);

    expect(Job::count())->toBe(0);
    actingAs($admin, 'admin')->post(route('job.store'), $params);
    expect(Job::count())->toBe(1);

    $createdJob = Job::first();
    expect($createdJob->title)
        ->toBe('Test Title')
        ->and($createdJob->company_id)
        ->toBeNull();
});
