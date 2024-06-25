<?php

use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use Database\Seeders\ProfessionSeeder;
use Modules\Testimonial\Entities\Testimonial;

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
    $this->seed([ProfessionSeeder::class]);
});

it('testimonials are displayed on the index page', function () {
    $this->get(route('module.testimonial.index'))
        ->assertStatus(200)
        ->assertViewIs('testimonial::index')
        ->assertViewHas(['testimonials', 'group_testimonials']);
});

it('can store a testimonial', function () {
    // Mock the request data
    $requestData = [
        'name' => 'John Doe',
        'position' => 'Developer',
        'description' => 'A great testimonial.',
        'stars' => 5,
        'code' => 'ABC123',
    ];

    $this->post(route('module.testimonial.store'), $requestData)->assertStatus(302);

    // You can also assert that the testimonial was created in the database
    $this->assertDatabaseHas('testimonials', [
        'name' => $requestData['name'],
        'position' => $requestData['position'],
        'description' => $requestData['description'],
        'stars' => $requestData['stars'],
        'code' => $requestData['code'],
    ]);
});

it('can view the edit testimonial page', function () {
    // Assuming you have a testimonial in the database
    $testimonial = Testimonial::factory()->create();

    // Act
    $this->get(route('module.testimonial.edit', $testimonial))
        ->assertStatus(200)
        ->assertViewIs('testimonial::edit')
        ->assertViewHas('testimonial', $testimonial);
});

it('can update a testimonial', function () {
    // Assuming you have a testimonial in the database
    $testimonial = Testimonial::factory()->create();

    // Mock the updated data
    $updatedData = [
        'name' => 'Updated Name',
        'position' => 'Updated Position',
        'description' => 'Updated description.',
        'stars' => 4,
        'code' => 'XYZ789',
    ];

    // Act
    $response = $this->put(route('module.testimonial.update', $testimonial), $updatedData)->assertStatus(302); // Or any appropriate status code

    // You can also assert that the testimonial was updated in the database
    $this->assertDatabaseHas('testimonials', [
        'id' => $testimonial->id,
        'name' => $updatedData['name'],
        'position' => $updatedData['position'],
        'description' => $updatedData['description'],
        'stars' => $updatedData['stars'],
        'code' => $updatedData['code'],
    ]);
});

it('can delete a testimonial', function () {
    // Assuming you have a testimonial in the database
    $testimonial = Testimonial::factory()->create();

    // Act
    $this->delete(route('module.testimonial.destroy', $testimonial))->assertStatus(302); // Or any appropriate status code

    // You can also assert that the testimonial was deleted from the database
    $this->assertDatabaseMissing('testimonials', ['id' => $testimonial->id]);
});
