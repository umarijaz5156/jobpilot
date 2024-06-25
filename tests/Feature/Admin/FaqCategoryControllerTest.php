<?php

use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use Database\Seeders\ProfessionSeeder;
use Modules\Faq\Entities\FaqCategory;

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
    $this->seed([ProfessionSeeder::class]);
});

it('displays FAQ categories in the index', function () {
    // Given some FAQ categories in the database
    $faqCategories = FaqCategory::factory()->count(3)->create();

    // When a user visits the FAQ category index page
    $response = $this->get(route('module.faq.category.index'));

    // They should see the FAQ categories listed
    $response->assertStatus(200);
    foreach ($faqCategories as $faqCategory) {
        $response->assertSee($faqCategory->name);
    }
});

it('can create a FaqCategory', function () {
    $categoryData = [
        'name' => 'Test Category',
        'slug' => 'test-category',
        'icon' => 'fas fa-test',
    ];

    $response = $this->post(route('module.faq.category.store'), $categoryData)
        ->assertStatus(302); // Assuming 201 is the correct status code for successful creation
    $this->assertDatabaseHas('faq_categories', $categoryData);

});

it('can edit a faq category', function () {
    // Create a FaqCategory instance in the database
    $category = FaqCategory::factory()->create();

    // New data for the category
    $newData = [
        'name' => 'Updated Category',
        'slug' => 'updated-category',
        'icon' => 'fas fa-updated',
    ];

    // Send a PUT request to update the category
    $this->put(route('module.faq.category.update', $category), $newData)
        ->assertStatus(302); // Assuming 200 is the correct status code for successful update

    // Verify that the changes were saved to the database
    $this->assertDatabaseHas('faq_categories', $newData);
});

test('it can delete a faq category', function () {
    // Create a FaqCategory instance in the database
    $category = FaqCategory::factory()->create();

    // Send a DELETE request to delete the category
    $this->delete(route('module.faq.category.destroy', $category))
        ->assertStatus(302); // Assuming 204 is the correct status code for successful deletion

    // Verify that the instance is removed from the database
    $this->assertDatabaseMissing('faq_categories', ['id' => $category->id]);
});
