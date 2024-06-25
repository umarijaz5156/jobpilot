<?php

// Import necessary model classes and seeders

use App\Models\Candidate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use App\Models\User;
use Database\Seeders\ProfessionSeeder;
use Illuminate\Http\UploadedFile;
use Modules\Language\Database\Seeders\LanguageDatabaseSeeder;

// Set up before each test
beforeEach(function () {
    // Create an admin user
    $this->admin = createAdmin();

    // Log in as the admin user
    $this->actingAs($this->admin, 'admin');

    // Seed the database with required data
    JobRole::factory()->create();
    Experience::factory()->create();
    Education::factory()->create();

    // Seed the database with additional data using the ProfessionSeeder
    $this->seed([
        ProfessionSeeder::class, LanguageDatabaseSeeder::class,
    ]);
});

// Test: Admin can visit the candidate index page
it('admin can visit candidate index page', function () {
    // Send a GET request to the candidate index route
    $response = $this->get(route('candidate.index'));

    // Assert that the response status code is 200 (OK)
    $response->assertStatus(200);

    // Assert that the view being returned is 'admin.candidate.index'
    $response->assertViewIs('backend.candidate.index');

    // Assert that the view has a specific data variable: 'candidates'
    $response->assertViewHas('candidates');
});

// Test: Admin can visit the candidate create page
it('admin can visit candidate create page', function () {
    // Create a candidate user
    User::factory()->create(['role' => 'candidate']);

    // Send a GET request to the candidate index route
    $response = $this->get(route('candidate.create'));

    // Assert that the response status code is 200 (OK)
    $response->assertStatus(200);

    // Assert that the view being returned is 'admin.candidate.create'
    $response->assertViewIs('backend.candidate.create');

    // // Assert that the view has a specific data variable: 'candidates'
    // $response->assertViewHas('data');
});

// Test: Admin can store new candidate
it('admin can store new candidate', function () {

    // Store location to session as a reuired data
    $data = [
        'lat' => '23.757853442383',
        'lng' => '90.411270491741',
        'country' => 'Bangladesh',
        'region' => 'Dhaka Division',
        'district' => 'Dhaka District',
        'place' => 'Dhaka',
        'exact_location' => 'Dhaka District, Dhaka Division, Bangladesh',
    ];

    // Store the data in the session
    session()->put('location', $data);

    // Send a POST request to the candidate index route
    $response = $this->post(route('candidate.store'), [
        'name' => 'OPU Saha',
        'email' => 'sahaapo@gmail.com',
        'password' => 'password',
        'profession_id' => '1',
        'experience' => '1',
        'role_id' => '1',
        'education' => '1',
        'gender' => 'male',
        'website' => 'https://www.facebook.com',
        'birth_date' => '21-08-2023',
        'marital_status' => 'married',
        'skills' => [
            0 => '3',
        ],
        'languages' => [
            0 => '19',
            1 => '40',
        ],
        'bio' => 'this is boidata',
        'image' => UploadedFile::fake()->create('image.jpg', 1024),
    ]);

    // Assert that the response status code is 302 (Redirect)
    $response->assertStatus(302);

    $candidate = User::first();

    // check database that our new candidate name is 'OPU Saha'
    expect($candidate->name)->toBe('OPU Saha');
});

// Test: Admin fails to store new candidate for validation
it('admin fails to store new candidate for validation', function () {

    // Store location to session as a reuired data
    $data = [
        'lat' => '23.757853442383',
        'lng' => '90.411270491741',
        'country' => 'Bangladesh',
        'region' => 'Dhaka Division',
        'district' => 'Dhaka District',
        'place' => 'Dhaka',
        'exact_location' => 'Dhaka District, Dhaka Division, Bangladesh',
    ];

    // Store the data in the session
    session()->put('location', $data);

    // Send a POST request to the candidate index route
    $response = $this->post(route('candidate.store'), [
        'password' => 'password',
        'profession_id' => '1',
        'experience' => '1',
        'role_id' => '1',
        'education' => '1',
        'gender' => 'male',
        'website' => 'https://www.facebook.com',
        'birth_date' => '21-08-2023',
        'marital_status' => 'married',
        'skills' => [
            0 => '3',
        ],
        'languages' => [
            0 => '19',
            1 => '40',
        ],
        'bio' => 'this is boidata',
        'image' => UploadedFile::fake()->create('image.jpg', 1024),
    ])->assertSessionHasErrors(['name', 'email']);

    // Assert that the response status code is 302 (Redirect)
    $response->assertStatus(302);

    $candidate = User::get();

    // check database that, there is no candidate registerd in our database
    expect(count($candidate))->toBe(0);
});

// // Test: Admin can visit the candidate edit page
it('admin can visit candidate edit page', function () {
    // Create a candidate user
    User::factory()->create(['role' => 'candidate']);

    // Send a GET request to the candidate index route
    $response = $this->get(route('candidate.edit', 1));

    // Assert that the response status code is 200 (OK)
    $response->assertStatus(200);

    // Assert that the view being returned is 'admin.candidate.create'
    $response->assertViewIs('backend.candidate.edit');

    // // Assert that the view has a specific data variable: 'candidates'
});

// Test: Admin can update an existing candidate
it('admin can update a candidate', function () {

    // Create a candidate user
    User::factory()->create([
        'name' => 'Donald Trump',
        'role' => 'candidate',
    ]);

    // Store location to session as a reuired data
    $data = [
        'lat' => '23.757853442383',
        'lng' => '90.411270491741',
        'country' => 'Bangladesh',
        'region' => 'Dhaka Division',
        'district' => 'Dhaka District',
        'place' => 'Dhaka',
        'exact_location' => 'Dhaka District, Dhaka Division, Bangladesh',
    ];

    // Store the data in the session
    session()->put('location', $data);

    // Send a POST request to the candidate index route
    $response = $this->put(route('candidate.update', 1), [
        'name' => 'Opu Saha',
        'email' => 'sahaapo@gmail.com',
        'password' => 'password',
        'profession_id' => '1',
        'experience' => '1',
        'role_id' => '1',
        'education' => '1',
        'gender' => 'male',
        'website' => 'https://www.facebook.com',
        'birth_date' => '21-08-2023',
        'marital_status' => 'married',
        'skills' => [
            0 => '3',
        ],
        'languages' => [
            0 => '19',
            1 => '40',
        ],
        'bio' => 'this is boidata',
        'image' => UploadedFile::fake()->create('image.jpg', 1024),
    ]);

    // Assert that the response status code is 302 (Redirect)
    $response->assertStatus(302);

    $candidate = User::first();

    // check database that, candidate name is changed to 'Opu Saha' from 'Donald trump'
    expect($candidate->name)->toBe('Opu Saha');
});

// Test: Admin fails to update an existing candidate for validation
it('admin fails to update an existing candidate for validation', function () {

    // Create a candidate user
    User::factory()->create([
        'name' => 'Donald Trump',
        'role' => 'candidate',
    ]);
    Candidate::factory()->create(['user_id' => 1]);

    // Store location to session as a reuired data
    $data = [
        'lat' => '23.757853442383',
        'lng' => '90.411270491741',
        'country' => 'Bangladesh',
        'region' => 'Dhaka Division',
        'district' => 'Dhaka District',
        'place' => 'Dhaka',
        'exact_location' => 'Dhaka District, Dhaka Division, Bangladesh',
    ];

    // Store the data in the session
    session()->put('location', $data);

    // Send a POST request to the candidate index route
    $response = $this->put(route('candidate.update', 1), [
        'password' => 'password',
        'profession_id' => '1',
        'experience' => '1',
        'role_id' => '1',
        'education' => '1',
        'gender' => 'male',
        'website' => 'https://www.facebook.com',
        'birth_date' => '21-08-2023',
        'marital_status' => 'married',
        'skills' => [
            0 => '3',
        ],
        'languages' => [
            0 => '19',
            1 => '40',
        ],
        'bio' => 'this is boidata',
        'image' => UploadedFile::fake()->create('image.jpg', 1024),
    ]);

    // Assert that the response status code is 302 (Redirect)
    $response->assertStatus(302);

    $candidate = User::first();

    // check database that, candidate name is 'Donald trump'
    expect($candidate->name)->toBe('Donald Trump');

});

// Test: Admin can delete an existing candidate
it('admin can delete an existing candidate', function () {

    // Create a candidate user
    User::factory()->create([
        'name' => 'Donald Trump',
        'role' => 'candidate',
    ]);

    $response = $this->delete(route('candidate.destroy', 1));

    // Assert that the response status code is 302 (Redirect)
    $response->assertStatus(302);

    expect(Candidate::count())->toBe(0);
    expect(User::count())->toBe(0);
});

// Test: Admin can Change Candidate Status
it('admin can change candidate status', function () {
    // Create a candidate user with the name 'Donald Trump', role 'candidate', and status '0'.
    User::factory()->create([
        'name' => 'Donald Trump',
        'role' => 'candidate',
        'status' => 0,
    ]);

    // Make a GET request to change the candidate status using a specific route.
    $this->get(route('candidate.status.change', ['status' => 1, 'id' => 1]));

    // Expect the candidate's status to be updated to '1'.
    expect(User::first()->status)->toBe(1);
});
