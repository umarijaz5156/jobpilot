<?php

use App\Models\Candidate;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Job;
use App\Models\User;
use Database\Seeders\CandidateAppliedJobSeeder;
use Database\Seeders\CandidateBookmarks;
use Database\Seeders\CandidateResumeSeeder;
use Database\Seeders\CandidateSeeder;
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
use Database\Seeders\TagSeeder;
use Database\Seeders\TeamSizeSeeder;

// it('displays the candidate dashboard', function () {
//     // Seeding the database with necessary data for the test
//     $this->seed([
//         EducationSeeder::class,
//         ExperienceSeeder::class,
//         JobTypeSeeder::class,
//         JobRoleSeeder::class,
//         SalaryTypeSeeder::class,
//         TeamSizeSeeder::class,
//         OrganizationTypeSeeder::class,
//         ProfessionSeeder::class,
//         IndustryTypeSeeder::class,
//         JobCategorySeeder::class,
//         CompanySeeder::class,
//         CandidateSeeder::class,
//         CandidateResumeSeeder::class,
//         JobSeeder::class,
//         CandidateBookmarks::class,
//         CandidateAppliedJobSeeder::class,
//         TagSeeder::class,
//     ]);

//     // Getting a candidate user and acting as them
//     $user = User::where('role', 'candidate')->first();
//     $this->actingAs($user);

//     // Mocking the data that the DashboardService should return
//     $mockedData = [
//         'appliedJobs' => 100,
//         'favoriteJobs' => 5,
//         'notifications' => 0,
//         'jobs' => [],
//         'candidate' => [],
//     ];

//     // Creating a mock of the DashboardService and defining its behavior
//     $mockedService = Mockery::mock(DashboardService::class);
//     $mockedService->shouldReceive('execute')->andReturn($mockedData);
//     $this->app->instance(DashboardService::class, $mockedService);

//     // Making a request to the candidate dashboard route and asserting the response
//     $this
//         ->get(route('candidate.dashboard'))
//         ->assertStatus(200)
//         ->assertViewHas('appliedJobs', $mockedData['appliedJobs'])
//         ->assertViewHas('favoriteJobs', $mockedData['favoriteJobs'])
//         ->assertViewHas('notifications', $mockedData['notifications']);
// });

// it('displays applied job information in the view', function () {
//     // Seeding the database with necessary data for the test
//     $this->seed([
//         EducationSeeder::class,
//         ExperienceSeeder::class,
//         JobTypeSeeder::class,
//         JobRoleSeeder::class,
//         SalaryTypeSeeder::class,
//         TeamSizeSeeder::class,
//         OrganizationTypeSeeder::class,
//         ProfessionSeeder::class,
//         IndustryTypeSeeder::class,
//         JobCategorySeeder::class,
//         CompanySeeder::class,
//         CandidateSeeder::class,
//         CandidateResumeSeeder::class,
//         JobSeeder::class,
//         CandidateAppliedJobSeeder::class,
//     ]);

//     // Getting a candidate user and acting as them
//     $user = User::where('role', 'candidate')->first();
//     $this->actingAs($user);

//     // Making a request to the applied job page and asserting the response
//     $this->get(route('candidate.appliedjob'))
//         ->assertOk() // Asserting that the response status is OK (200)
//         ->assertViewIs('frontend.pages.candidate.applied-jobs') // Asserting the expected view
//         ->assertSee('lorem ipsum dolor sit amet'); // Asserting the presence of certain content
// });

it('shows bookmarked jobs for a candidate', function () {
    // Seeding the database with necessary data for the test
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
        CompanySeeder::class,
        TagSeeder::class,
    ]);

    // Creating a candidate and associating them with a user
    $candidate = Candidate::factory()->create();
    $user = User::factory()->create(['role' => 'candidate']);
    $user->candidate()->save($candidate);

    // Acting as the candidate user
    $this->actingAs($user);

    // Creating bookmarked jobs
    $bookmarkJobs = Job::factory()
        ->count(15)
        ->create();
    $candidate->bookmarkJobs()->attach($bookmarkJobs);

    // Making a request to the candidate bookmark page and asserting the response
    $this->get(route('candidate.bookmark'))
        ->assertStatus(200) // Asserting that the response status is OK (200)
        ->assertViewIs('frontend.pages.candidate.bookmark') // Asserting the expected view
        ->assertViewHas('jobs') // Asserting that the view has the 'jobs' variable
        ->assertViewHas('resumes'); // Asserting that the view has the 'resumes' variable
});

it('displays job alerts', function () {
    // Seeding the database with necessary data for the test
    $this->seed([
        EducationSeeder::class,
        ExperienceSeeder::class,
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
    ]);

    // Creating a candidate user and acting as them
    $user = User::factory()->create(['role' => 'candidate']);
    $this->actingAs($user);

    // Making a request to the job alerts page and asserting the response
    $this->get(route('candidate.job.alerts'))
        ->assertOk() // Asserting that the response status is OK (200)
        ->assertSeeText('Job Alert'); // Asserting that the response body contains 'Job Alert'
});

it('can update candidate info', function () {
    // Seeding the database with necessary data for the test
    $this->seed([
        EducationSeeder::class,
        ExperienceSeeder::class,
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
    ]);

    // Creating a candidate user
    $user = User::factory()->create(['role' => 'candidate']);

    // Acting as the candidate user
    actingAs($user);

    // Finding the candidate and updating their username
    $find_candidate = User::find($user->id);
    $find_candidate->update([
        'username' => 'update Name',
    ]);

    // Refreshing the user model
    $user->refresh();

    // Asserting that the username has been updated successfully
    $this->assertEquals('update Name', $user->username);
});

it('candidate basic info update with valid data', function () {
    // Seeding the database with necessary data for the test
    $this->seed([
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
    ]);

    // Creating instances of experience and education
    $experience = Experience::factory()->create();
    $education = Education::factory()->create();

    // Creating a candidate user and acting as them
    $user = User::factory()->create(['role' => 'candidate']);
    $this->actingAs($user);

    // Making a PUT request to update the candidate's basic info
    $this->put(route('candidate.settingUpdate'), [
        'name' => 'John Doe',
        'birth_date' => '1990-01-01',
        'education' => $education->id,
        'experience' => $experience->id,
        'type' => 'basic',
    ]);

    // Asserting that the user's name has been updated
    $this->assertEquals('John Doe', $user->fresh()->name);

    // Checking if the candidate information is updated in the database
    $this->assertDatabaseHas('candidates', [
        'experience_id' => $experience->id,
        'education_id' => $education->id,
    ]);
});

it('can store candidate experience', function () {
    // Seeding the database with necessary data for the test
    $this->seed([
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
    ]);

    // Creating instances of Experience and Education
    Experience::factory()->create();
    Education::factory()->create();

    // Creating a candidate user and acting as them
    $user = User::factory()->create(['role' => 'candidate']);
    $this->actingAs($user);

    // Data for the new experience
    $data = [
        'company' => 'Example Company',
        'department' => 'IT',
        'designation' => 'Developer',
        'start' => now()->subYear(),
        'end' => now(), // Assuming the candidate has ended the experience
        'responsibilities' => 'Developing awesome stuff',
        'currently_working' => 0,
        'type' => 'experience',
    ];

    // Making a POST request to store candidate experience
    $this->post(route('candidate.experiences.store'), $data);

    // Asserting that the new experience is stored in the database
    $this->assertDatabaseHas('candidate_experiences', [
        'candidate_id' => $user->id,
        'company' => 'Example Company',
        'department' => 'IT',
        'designation' => 'Developer',
        'responsibilities' => 'Developing awesome stuff',
        'currently_working' => 0,
    ]);
});

it('can update candidate experience', function () {
    // Seeding the database with necessary data for the test
    $this->seed([
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
    ]);

    // Creating an instance of Experience
    $experience = Experience::factory()->create();

    // Creating an instance of Candidate and Education
    $candidate = Education::factory()->create();

    // Creating a candidate user and acting as them
    $user = User::factory()->create(['role' => 'candidate']);

    // Creating a CandidateExperience and associating it with the user
    CandidateExperience::factory()->create(['candidate_id' => $user->id, 'company' => 1]);

    $this->actingAs($user);

    // Data for the updated experience
    $data = [
        'company' => 'Updated Company',
        'department' => 'Updated IT',
        'designation' => 'Updated Developer',
        'start' => now()->subYears(2),
        'end' => now(), // Assuming the candidate has ended the updated experience
        'responsibilities' => 'Updated responsibilities',
        'currently_working' => 0,
        'experience_id' => $experience->id,
        'type' => 'experience',
    ];

    // Making a PUT request to update candidate experience
    $this->put(route('candidate.experiences.update'), $data)->assertStatus(302);

    // Asserting that the experience is updated in the database
    $this->assertDatabaseHas('candidate_experiences', [
        'id' => $experience->id,
        'candidate_id' => $candidate->id,
        'company' => 'Updated Company',
        'department' => 'Updated IT',
        'designation' => 'Updated Developer',
        'responsibilities' => 'Updated responsibilities',
        'currently_working' => 0,
    ]);
});

it('can delete a candidate experience', function () {
    // Seeding the database with necessary data for the test
    $this->seed([
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
    ]);

    // Creating instances of Experience and Education
    Experience::factory()->create();
    Education::factory()->create();

    // Creating a candidate user and acting as them
    $user = User::factory()->create(['role' => 'candidate']);

    // Creating a CandidateExperience and associating it with the user
    CandidateExperience::factory()->create(['candidate_id' => $user->id, 'company' => 1]);

    $this->actingAs($user);

    // Making a DELETE request to delete the candidate's experience
    $this->delete(route('candidate.experiences.destroy', $user->id))
        ->assertStatus(302) // Asserting the response status is a redirect (302)
        ->assertRedirect(); // Asserting a redirect

    // Asserting that the candidate experiences count in the database is now 0
    $this->assertDatabaseCount('candidate_experiences', 0);
});

it('can store candidate education', function () {
    // Seeding the database with necessary data for the test
    $this->seed([
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
    ]);

    // Creating instances of Experience and Education
    Experience::factory()->create();
    Education::factory()->create();

    // Creating a candidate user and acting as them
    $user = User::factory()->create(['role' => 'candidate']);
    $this->actingAs($user);

    // Data for the new education
    $data = [
        'level' => 'Bachelor\'s',
        'degree' => 'Computer Science',
        'year' => 2020,
        'notes' => 'Graduated with honors',
        'type' => 'experience',
    ];

    // Making a POST request to store candidate education
    $this->post(route('candidate.educations.store'), $data);

    // Asserting that the new education is stored in the database
    $this->assertDatabaseHas('candidate_education', [
        'candidate_id' => $user->id,
        'level' => 'Bachelor\'s',
        'degree' => 'Computer Science',
        'year' => 2020,
        'notes' => 'Graduated with honors',
    ]);
});

it('can update candidate education', function () {
    // Seeding the database with necessary data for the test
    $this->seed([
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
    ]);

    // Creating instances of Experience and Education
    $experience = Experience::factory()->create();
    $education = Education::factory()->create();

    // Creating a candidate user and acting as them
    $user = User::factory()->create(['role' => 'candidate']);

    // Creating a CandidateEducation and associating it with the user
    CandidateEducation::factory()->create(['candidate_id' => $user->id]);

    $this->actingAs($user);

    // Data for the updated education
    $data = [
        'level' => 'Updated Level',
        'degree' => 'Updated Degree',
        'year' => 2022,
        'notes' => 'Updated notes',
        'education_id' => $education->id,
        'type' => 'experience',
    ];

    // Making a PUT request to update candidate education
    $this->put(route('candidate.educations.update'), $data)->assertStatus(302);

    // Asserting that the education is updated in the database
    $this->assertDatabaseHas('candidate_education', [
        'id' => $experience->id,
        'candidate_id' => $education->id,
        'level' => 'Updated Level',
        'degree' => 'Updated Degree',
        'year' => 2022,
        'notes' => 'Updated notes',
    ]);
});

it('can delete a candidate education', function () {
    // Seeding the database with necessary data for the test
    $this->seed([
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
    ]);

    // Creating instances of Experience and Education
    Experience::factory()->create();
    Education::factory()->create();

    // Creating a candidate user and acting as them
    $user = User::factory()->create(['role' => 'candidate']);

    // Creating a CandidateEducation and associating it with the user
    CandidateEducation::factory()->create(['candidate_id' => $user->id]);

    $this->actingAs($user);

    // Making a DELETE request to delete the candidate's education
    $this->delete(route('candidate.educations.destroy', $user->id))
        ->assertStatus(302) // Asserting the response status is a redirect (302)
        ->assertRedirect(); // Asserting a redirect

    // Asserting that the candidate education count in the database is now 0
    $this->assertDatabaseCount('candidate_education', 0);
});

it('deleted user', function () {
    // Seeding the database with necessary data for the test
    $this->seed([
        JobTypeSeeder::class,
        JobRoleSeeder::class,
        ProfessionSeeder::class,
    ]);

    // Creating instances of Experience and Education
    Experience::factory()->create();
    Education::factory()->create();

    // Creating a candidate user
    $user = User::factory()->create(['role' => 'candidate']);

    // Data for the account deletion action
    $data = [
        'type' => 'account-delete',
        'user' => $user->id,
    ];

    // Act: Making a PUT request to perform the account deletion action
    $this->put(route('candidate.settingUpdate'), $data);

    // Assert: Attempting to log in with the deleted user's credentials should result in a redirect
    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password', // Assuming a default password for testing
    ])->assertStatus(302);
});

it('successfully applies', function () {
    // Seeding the database with necessary data for the test
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
        CompanySeeder::class,
    ]);

    // Creating a company user
    $user = User::factory()->create(['role' => 'company']);

    // Creating a job for testing
    Job::factory()->create();

    $this->actingAs($user);

    // Making a POST request to apply for a job
    $this->post(route('website.job.apply'), [
        'resume_id' => 1, // Assuming the resume with ID 1
        'cover_letter' => 'Sample cover letter',
    ])->assertRedirect(); // Asserting a redirect

});
