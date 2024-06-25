<?php

// Import necessary model classes and seeders

use App\Models\Education;
use App\Models\Experience;
use App\Models\IndustryType;
use App\Models\Job;
use App\Models\OrganizationType;
use App\Models\TeamSize;
use App\Models\User;
use Database\Seeders\BenefitSeeder;
use Database\Seeders\EducationSeeder;
use Database\Seeders\ExperienceSeeder;
use Database\Seeders\JobCategorySeeder;
use Database\Seeders\JobRoleSeeder;
use Database\Seeders\JobTypeSeeder;
use Database\Seeders\SalaryTypeSeeder;
use Database\Seeders\SkillSeeder;
use Database\Seeders\TagSeeder;
use Modules\Currency\Database\Seeders\CurrencyDatabaseSeeder;

// Set up before each test
beforeEach(function () {
    // Create an admin user
    $this->admin = createAdmin();

    // Log in as the admin user
    $this->actingAs($this->admin, 'admin');

    // Create sample data using factory methods for various models
    IndustryType::factory()->create();
    OrganizationType::factory()->create();
    TeamSize::factory()->create();

    // Create a sample user with the role 'company'
    User::factory()->create(['role' => 'company']);

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
});

it('admin can visit job index page', function () {

    // Send a GET request to the 'job.index' route
    $response = $this->get(route('job.index'));

    // Assert that the response status code is 200 (OK)
    $response->assertStatus(200);

    // Assert that the view being rendered is 'admin.Job.index'
    $response->assertViewIs('backend.Job.index');

    // Assert that the view has these specific variables: 'jobs', 'job_categories', 'experiences', 'job_types', 'companies', 'edited_jobs'
    $response->assertViewHas(['jobs', 'job_categories', 'experiences', 'job_types', 'companies', 'edited_jobs']);
});

// it('admin can visit job create page', function(){
//     // Send a GET request to the 'job.create' route
//     $response = $this->get(route('job.create'));

//     // Assert that the response status code is 200 (OK)
//     $response->assertStatus(200);

//     // Assert that the view being rendered is 'admin.Job.index'
//     $response->assertViewIs('backend.Job.create');

// });

// test that an admin can post a new job
it('admin can post a job', function () {

    // Define location data that needs to be stored in the session
    $data = [
        'lat' => '23.757853442383',
        'lng' => '90.411270491741',
        'country' => 'Bangladesh',
        'region' => 'Dhaka Division',
        'district' => 'Dhaka District',
        'place' => 'Dhaka',
        'exact_location' => 'Dhaka District, Dhaka Division, Bangladesh',
    ];

    // Store the location data in the session
    session()->put('location', $data);

    // Send a POST request to the 'job.store' route to create a new job record
    $response = $this->post(route('job.store'), [
        // Fill in job-related data
        'title' => 'Web Developer',
        'company_id' => '1',
        'company_name' => null,
        'category_id' => '5',
        'vacancies' => '1',
        'deadline' => '2023-08-23',
        'salary_mode' => 'range',
        'min_salary' => '80000',
        'max_salary' => '1000000',
        'custom_salary' => 'Competitive',
        'salary_type' => '1',
        'apply_on' => 'app',
        'apply_email' => null,
        'apply_url' => null,
        'badge' => 'featured',
        'experience' => '3',
        'role_id' => '5',
        'tags' => [
            0 => '2',
        ],
        'benefits' => [
            0 => '3',
        ],
        'skills' => [
            0 => '5',
        ],
        'education' => '4',
        'job_type' => '3',
        'description' => 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Reprehenderit hic, sint delectus quis officia tempore ex suscipit quod quisquam corrupti. Dolore quibusdam qui, placeat optio provident officiis aut blanditiis illo asperiores, odio tempore ab expedita nulla adipisci saepe sunt iusto molestiae corporis fugit perferendis a id neque dignissimos! Molestiae, tempora maiores laborum cupiditate fugiat deleniti iste asperiores error cumque sed dolorum architecto rem quasi voluptatem laboriosam aliquam quos expedita. Autem, perferendis omnis dolorum, voluptas esse, eum fugiat natus error aliquam labore iste placeat tenetur modi architecto ipsam quidem dolor totam quisquam quis obcaecati! Quia aliquam recusandae voluptas facere. Laborum, illo.',
    ]);

    // Assert that the response status code is 302 (a redirect)
    $response->assertStatus(302);

    // Assert that there is now one job record in the database
    expect(Job::count())->toBe(1);

    // Assert that the title of the first job record matches what was created
    expect(Job::first()->title)->toBe('Web Developer');
});

// test that an admin fails to post new job for validation
it('admin fails to post new job for validation check', function () {

    // Define location data that needs to be stored in the session
    $data = [
        'lat' => '23.757853442383',
        'lng' => '90.411270491741',
        'country' => 'Bangladesh',
        'region' => 'Dhaka Division',
        'district' => 'Dhaka District',
        'place' => 'Dhaka',
        'exact_location' => 'Dhaka District, Dhaka Division, Bangladesh',
    ];

    // Store the location data in the session
    session()->put('location', $data);

    // Send a POST request to the 'job.store' route to create a new job record
    $response = $this->post(route('job.store'), [
        // Fill in job-related data
        'salary_mode' => 'range',
        'min_salary' => '80000',
        'max_salary' => '1000000',
        'custom_salary' => 'Competitive',
        'salary_type' => '1',
        'apply_on' => 'app',
        'apply_email' => null,
        'apply_url' => null,
        'badge' => 'featured',
        'tags' => [
            0 => '2',
        ],
        'benefits' => [
            0 => '3',
        ],
        'skills' => [
            0 => '5',
        ],
        'job_type' => '3',
        'description' => 'This is a description',
    ])->assertSessionHasErrors([
        'title',
        'category_id',
        'vacancies',
        'deadline',
        'experience',
        'role_id',
        'education',
    ]);

    // Assert that the response status code is 302 (a redirect)
    $response->assertStatus(302);

    // Assert that there is now one job record in the database
    expect(Job::count())->toBe(0);

});

// test admin can visit job details page
it('admin can visit job details page', function () {

    $job = Job::factory()->create();
    // Send a GET request to the 'job.details' route
    $response = $this->get(route('job.show', $job));

    // Assert that the response status code is 200 (OK)
    $response->assertStatus(200);

    // Assert that the view being rendered is 'admin.Job.index'
    $response->assertViewIs('backend.Job.show');

});

//  // test admin can visit job edit page
// it('admin can visit job edit page', function () {

//     $this->seed(CurrencyDatabaseSeeder::class);
//     $job = Job::factory()->create();
//     // Send a GET request to the 'job.edit' route
//     $response = $this->get(route('job.edit',$job));

//     // Assert that the response status code is 200 (OK)
//     $response->assertStatus(200);

//     // Assert that the view being rendered is 'admin.Job.index'
//     $response->assertViewIs('backend.Job.edit');

// });

// // // test that an admin can update any existing job
// it('admin can post update any job', function () {

//     $job = Job::factory()->create();
//     // Define location data that needs to be stored in the session
//     $data = [
//         "lat" => "23.757853442383",
//         "lng" => "90.411270491741",
//         "country" => "Bangladesh",
//         "region" => "Dhaka Division",
//         "district" => "Dhaka District",
//         "place" => "Dhaka",
//         "exact_location" => "Dhaka District, Dhaka Division, Bangladesh"
//     ];

//     // Store the location data in the session
//     session()->put('location', $data);

//     // Send a PUT request to the 'job.update' route to create a new job record
//     $response = $this->put(route('job.update',1), [
//         // Fill in job-related data
//         "title" => "Web Developer",
//         "company_id" => "1",
//         "company_name" => null,
//         "category_id" => "5",
//         "vacancies" => "1",
//         "deadline" => "2023-08-23",
//         "salary_mode" => "range",
//         "min_salary" => "80000",
//         "max_salary" => "1000000",
//         "custom_salary" => "Competitive",
//         "salary_type" => "1",
//         "apply_on" => "app",
//         "apply_email" => null,
//         "apply_url" => null,
//         "badge" => "featured",
//         "experience" => "3",
//         "role_id" => "5",
//         "tags" => [
//             0 => "2",
//         ],
//         "benefits" => [
//             0 => "3",
//         ],
//         "skills" => [
//             0 => "5",
//         ],
//         "education" => "4",
//         "job_type" => "3",
//         "description" => "This is a description",
//     ]);

//     // Assert that the response status code is 302 (a redirect)
//     $response->assertStatus(302);

//     // Assert that there is now one job record in the database
//     expect(Job::count())->toBe(1);

//     // Assert that the title of the first job record matches what was created
//     expect(Job::first()->title)->toBe("Web Developer");
// });

// // test that an admin fails to post new job for validation
// it('admin fails to update a job for validation check', function () {

//     $job = Job::factory()->create();

//     // Define location data that needs to be stored in the session
//     $data = [
//         "lat" => "23.757853442383",
//         "lng" => "90.411270491741",
//         "country" => "Bangladesh",
//         "region" => "Dhaka Division",
//         "district" => "Dhaka District",
//         "place" => "Dhaka",
//         "exact_location" => "Dhaka District, Dhaka Division, Bangladesh"
//     ];

//     // Store the location data in the session
//     session()->put('location', $data);

//     // Send a POST request to the 'job.store' route to create a new job record
//     $response = $this->put(route('job.update',$job), [
//         // Fill in job-related data
//         "salary_mode" => "range",
//         "min_salary" => "80000",
//         "max_salary" => "1000000",
//         "custom_salary" => "Competitive",
//         "salary_type" => "1",
//         "apply_on" => "app",
//         "apply_email" => null,
//         "apply_url" => null,
//         "badge" => "featured",
//         "tags" => [
//             0 => "2",
//         ],
//         "benefits" => [
//             0 => "3",
//         ],
//         "skills" => [
//             0 => "5",
//         ],
//         "job_type" => "3",
//         "description" => "This is a description",
//     ])->assertSessionHasErrors([
//         "title",
//         "category_id",
//         "vacancies",
//         "deadline",
//         "experience",
//         "role_id",
//         "education",
//     ]);

//     // Assert that the response status code is 302 (a redirect)
//     $response->assertStatus(302);

//     // Assert that there is now one job record in the database
//     expect(Job::first()->title)->toBe($job->title);
// });

// test admin can delete a job a job
it('admin fails to update a job for validation check', function () {

    $job = Job::factory()->create();

    $response = $this->delete(route('job.destroy', $job));
    $response->assertStatus(302);
    expect(Job::count())->toBe(0);
});

// This is a test case for verifying that an admin can change the status of a job.
it('admin can change a job status', function () {
    // Create a new job with an initial status of 'pending' using a factory.
    $job = Job::factory()->create(['status' => 'pending']);

    // Send a PUT request to the specified route ('admin.job.status.change') to change the job's status to 'active'.
    $this->put(route('admin.job.status.change', $job), ['status' => 'active']);

    // Expectation: Check if the status of the first job in the database is now 'active'.
    expect(Job::first()->status)->toBe('active');
});
