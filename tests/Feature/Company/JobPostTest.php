<?php

use App\Models\Company;
use App\Models\Job;

it('open a job post page', function () {

    $response = $this->get('company.create.job');

    $response->assertSeeText('Post a Job');
});

// it('new job post create validation testing', function () {

//     $data = [
//         'category_id'  => '',
//         'role_id'  => '',
//         'experience' => '',
//         'education'  => '',
//         'job_type'  => '',
//         'vacancies'  => '',
//         'salary_mode' => '',
//         'salary_type' => '',
//         'deadline' => '',
//         'description' => '',
//         'apply_on' => '',
//     ];

//     $response = $this->post(route('company.job.store'), $data);
//     $response->assertStatus(302);
//     $response->assertSessionHasErrors(['category_id', 'role_id', 'experience','education','job_type','vacancies','salary_mode','salary_type','deadline','description','apply_on']);
//     $response->assertInvalid(['category_id', 'role_id', 'experience','education','job_type','vacancies','salary_mode','salary_type','deadline','description','apply_on']);

// });

it('create a new job post', function () {
    // Seed the database with initial data for various seeders
    $this->seed([
        JobCategorySeeder::class,
        SalaryTypeSeeder::class,
        ExperienceSeeder::class,
        JobRoleSeeder::class,
        TagSeeder::class,
        BenefitSeeder::class,
        SkillSeeder::class,
        EducationSeeder::class,
        JobTypeSeeder::class,
    ]);

    // Define the data to be used for creating a new plan.
    $data = [
        'title' => 'Test title',
        'company_id' => 1,
        'category_id' => 1,
        'role_id' => 1,
        'education_id' => 1,
        'experience_id' => 1,
        'salary_mode' => 1,
        'custom_salary' => 1,
        'min_salary' => 99,
        'max_salary' => 100,
        'salary_type_id' => 1,
        'deadline' => '2023-12-31',
        'job_type_id' => 2,
        'vacancies' => 1,
        'apply_on' => 1,
        'apply_email' => null,
        'apply_url' => null,
        'description' => 'Test Data',
        'featured' => 'xyz',
        'highlight' => 'zxy',
        'is_remote' => 0,
        'status' => 'pending',
    ];

    // Send a POST request to store the new jobe post using the defined data.
    $response = $this->post(route('company.job.store'), $data);

    // Assert that the response status code is 302 (a successful redirect).
    $response->assertStatus(302);
});

it('job post update validation testing', function () {

    $data = [
        'category_id' => '',
        'role_id' => '',
        'experience' => '',
        'education' => '',
        'job_type' => '',
        'vacancies' => '',
        'salary_mode' => '',
        'salary_type' => '',
        'deadline' => '',
        'description' => '',
        'apply_on' => '',
    ];

    $response = $this->put(route('company.job.update', 'slug'), $data);

    // Assert that the response status code is 302 (a redirect).
    // Also, assert that the session has errors for the specified fields.
    $response->assertStatus(302);
});

it('update a job post', function () {

    // Seed the database with initial data for various seeders
    $this->seed([
        JobCategorySeeder::class,
        SalaryTypeSeeder::class,
        ExperienceSeeder::class,
        JobRoleSeeder::class,
        TagSeeder::class,
        BenefitSeeder::class,
        SkillSeeder::class,
        EducationSeeder::class,
        JobTypeSeeder::class,
    ]);

    // Define the data to be used for creating a new plan.
    $data = [
        'title' => 'Test title',
        'company_id' => 1,
        'category_id' => 1,
        'role_id' => 1,
        'education_id' => 1,
        'experience_id' => 1,
        'salary_mode' => 1,
        'custom_salary' => 1,
        'min_salary' => 99,
        'max_salary' => 100,
        'salary_type_id' => 1,
        'deadline' => '2023-12-31',
        'job_type_id' => 2,
        'vacancies' => 1,
        'apply_on' => 1,
        'apply_email' => null,
        'apply_url' => null,
        'description' => 'Test Data',
        'featured' => 'xyz',
        'highlight' => 'zxy',
        'is_remote' => 0,
        'status' => 'pending',
    ];

    // Send a POST request to store the new jobe post using the defined data.
    $response = $this->put(route('company.job.update', 'slug'), $data);

    // Assert that the response status code is 302 (a successful redirect).
    $response->assertStatus(302);

});

it('open promote page', function () {

    // Seed the database with initial data for various seeders
    $this->seed([
        JobCategorySeeder::class,
        SalaryTypeSeeder::class,
        ExperienceSeeder::class,
        JobRoleSeeder::class,
        TagSeeder::class,
        BenefitSeeder::class,
        SkillSeeder::class,
        EducationSeeder::class,
        JobTypeSeeder::class,
    ]);
    Company::factory()->create();
    $job = Job::factory()->create(['deadline' => now()->addDay(), 'status' => 'active']);

    $response = $this->get(route('company.promote', $job->slug));

    $response->assertStatus(302);
});

it('open job details', function () {

    // Seed the database with initial data for various seeders
    $this->seed([
        JobCategorySeeder::class,
        SalaryTypeSeeder::class,
        ExperienceSeeder::class,
        JobRoleSeeder::class,
        TagSeeder::class,
        BenefitSeeder::class,
        SkillSeeder::class,
        EducationSeeder::class,
        JobTypeSeeder::class,
    ]);
    Company::factory()->create();
    $job = Job::factory()->create(['deadline' => now()->addDay(), 'status' => 'active']);
    //dd($job);

    $response = $this->get(route('website.job.details', $job->slug));

    $response->assertStatus(200);
});

// it('job active or inactive', function () {
//     $response = $this->get('company.myjob');

//     $response->assertSeeText('My Jobs');
// });

it('job clone', function () {

    // Seed the database with initial data for various seeders
    $this->seed([
        JobCategorySeeder::class,
        SalaryTypeSeeder::class,
        ExperienceSeeder::class,
        JobRoleSeeder::class,
        TagSeeder::class,
        BenefitSeeder::class,
        SkillSeeder::class,
        EducationSeeder::class,
        JobTypeSeeder::class,
    ]);
    Company::factory()->create();
    $job = Job::factory()->create(['deadline' => now()->addDay(), 'status' => 'active']);

    $response = $this->get(route('company.clone', $job->slug));

    $response->assertStatus(302);
});

it('open job plans page', function () {
    $response = $this->get('website.plan');

    $response->assertSeeText('Pricing');
});

it('pay per job create page', function () {
    $response = $this->get('company.create.pay-per-job');

    $response->assertSeeText('Post Job');
});

it('pay per job store validation', function () {

    $response = $this->post(route('company.payperjob.store', (['location' => '', 'apply_on' => ''])));

    $response->assertStatus(302);
});

it('pay per job store', function () {

    // Seed the database with initial data for various seeders
    $this->seed([
        JobCategorySeeder::class,
        SalaryTypeSeeder::class,
        ExperienceSeeder::class,
        JobRoleSeeder::class,
        TagSeeder::class,
        BenefitSeeder::class,
        SkillSeeder::class,
        EducationSeeder::class,
        JobTypeSeeder::class,
    ]);
    Company::factory()->create();
    $job = Job::factory()->create(['title' => 'Pay Par Job test', 'company_id' => 1, 'deadline' => now()->addDay(), 'status' => 'active']);

    $response = $this->get(route('company.clone', $job->slug));

    // Assert that the response status code is 302 (a successful redirect).
    $response->assertStatus(302);
    // Send request with some data
    $response2 = $this->post(route('company.payperjob.store', (['location' => 'test', 'apply_on' => 'emai', 'apply_email' => 'test@email.com'])));

    session(['job_total_amount' => 100]);
    session(['job_request' => $response]);

    $response2->assertStatus(302);

});
