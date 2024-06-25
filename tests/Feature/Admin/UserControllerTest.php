<?php

use App\Models\Admin;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use Database\Seeders\ProfessionSeeder;
use Modules\Language\Database\Seeders\LanguageDatabaseSeeder;

// Set up before each test
beforeEach(function () {
    // Create an admin user
    $admin = $this->admin = createAdmin();

    // Log in as the admin user
    $this->actingAs($this->admin, 'admin');

    // Seed the database with required data
    JobRole::factory()->create();
    Experience::factory()->create();
    Education::factory()->create();

    // Seed the database with additional data using the ProfessionSeeder
    $this->seed([ProfessionSeeder::class, LanguageDatabaseSeeder::class]);
});

it('displays paginated users for admins', function () {

    // Create some additional users for testing
    Admin::factory()->count(12)->create();

    // Make a GET request to the index route
    $response = $this->get(route('user.index'));

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the view displays the users
    $response->assertViewIs('backend.users.index');

    // Assert that the "users" variable is passed to the view
    $response->assertViewHas('users');

    // Assert that there are 13 users on the current page
    $this->assertDatabaseCount('admins', 13);

});

it('displays the edit form for an admin user', function () {
    // Create a sample admin user to be edited
    $adminToEdit = Admin::factory()->create();

    // Make a GET request to the edit route
    $response = $this->get(route('user.edit', ['user' => $adminToEdit->id]));

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the view is the edit view for admin users
    $response->assertViewIs('backend.users.edit');

    // Assert that the "admin" variable is passed to the view
    $response->assertViewHas('user', $adminToEdit);
});

it('updates an admin user', function () {

    $adminToUpdate = Admin::first();
    // Define the data to be sent in the PUT request
    $updatedData = [
        'name' => 'Updated Admin Name',
        'email' => 'updatedadmin@example.com',
        'roles' => 1,
        // Add other necessary fields here
    ];

    // Make a PUT request to the update route with the form data
    $this->put(route('user.update', $adminToUpdate), $updatedData);

    // Assert that the admin user data is updated in the database
    $this->assertDatabaseHas('admins', [
        'id' => $adminToUpdate->id,
        'name' => 'Updated Admin Name',
        'email' => 'updatedadmin@example.com',
        // Add other necessary fields here
    ]);

});

it('deletes an admin user', function () {

    // Create an admin user to be deleted
    $adminToDelete = Admin::factory()->create();

    // Make a DELETE request to the destroy route
    $this->delete(route('user.destroy', $adminToDelete));
    // Assert that the admin user is deleted from the database
    $this->assertDatabaseMissing('admins', ['id' => $adminToDelete->id]);

});
