<?php

use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use Modules\Language\Database\Seeders\LanguageDatabaseSeeder;
use Spatie\Permission\Models\Role;

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
    $this->seed([ProfessionSeeder::class, LanguageDatabaseSeeder::class]);
});

it('index page displays roles', function () {

    $this->get(route('role.index'))->assertStatus(200);

});

it('creates a role with permissions', function () {
    // Prepare data for the request
    $requestData = [
        'name' => 'Test Role',
        'permissions' => [1, 2, 3], // IDs of permissions
    ];

    // Create a new role using the static function
    Role::create($requestData);

    // Assert that the role was created in the database
    $this->assertDatabaseHas('roles', ['name' => 'Test Role']);
});

it('edit page displays role', function () {

    $role = Role::first();
    $this->get(route('role.edit', $role))->assertStatus(200)->assertSee($role->name);
});

it('update role', function () {

    // Prepare data for the request
    $requestData = [
        'name' => 'Test Role',
        'permissions' => [1, 2, 3], // IDs of permissions
    ];

    // Create a new role using the static function
    $role = Role::create($requestData);

    $this->put(route('role.update', $role), [
        'name' => 'Updated Role Name',
        'permissions' => [1, 2, 3],
    ])->assertStatus(302);
    // Assert that the role's name is updated in the database
    $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Updated Role Name']);

});

it('deletes a role', function () {

    // Prepare data for the request
    $requestData = [
        'name' => 'Test Role',
        'permissions' => [1, 2, 3], // IDs of permissions
    ];

    // Create a new role using the static function
    $role = Role::create($requestData);

    // Send a DELETE request to delete the role
    $this->delete(route('role.destroy', $role));

    // Assert that the role is deleted from the database
    $this->assertDatabaseMissing('roles', ['id' => $role->id]);

});
