<?php

use App\Models\Benefit;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use Database\Seeders\BenefitSeeder;
use Modules\Language\Database\Seeders\LanguageDatabaseSeeder;
use Modules\Language\Entities\Language;

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

// Test case to check if benefits are displayed correctly on the index page
it('can display the benefit', function () {
    // Creating 3 benefit instances using the factory
    $benefit = Benefit::factory(3)->create();

    // Making a GET request to the benefit index route and performing assertions
    $this->get(route('benefit.index'))
        ->assertStatus(200)
        ->assertViewIs('backend.benefit.index')
        ->assertViewHas('benefits', $benefit);
});

// Test case to validate benefit creation
it('create validation', function () {
    // Fetching the latest app languages and preparing validation rules
    $app_languages = Language::latest()->get(['code']);
    $validate_array = [];
    foreach ($app_languages as $language) {
        $validate_array['name_'.$language->code] = 'required|string|max:255';
    }

    // Making a POST request to the benefit store route and asserting the status
    $this->post(route('benefit.store'), $validate_array)->assertStatus(302);
});

// Test case to create a benefit with translations
it('creates a benefit with translations', function () {
    // Fetching all languages and preparing data for benefit creation
    $languages = loadLanguage();
    $data = [];
    foreach ($languages as $language) {
        $data['name_'.$language->code] = 'Benefit Type Name in '.$language->code;
    }

    // Making a POST request to the benefit store route to create the benefit
    $this->post(route('benefit.store'), $data);

    // Asserting that the benefit and its translations were properly saved in the database
    $this->assertDatabaseCount('benefits', 1);
    $benefit = Benefit::first();
    foreach ($languages as $language) {
        $this->assertDatabaseHas('benefit_translations', [
            'benefit_id' => $benefit->id,
            'locale' => $language->code,
            'name' => 'Benefit Type Name in '.$language->code,
        ]);
    }
});

// Test case to display the edit page for a benefit
it('displays the edit benefit page', function () {
    // Creating a single benefit instance using the factory
    $benefit = Benefit::factory()->create();

    // Making a GET request to the edit route and performing assertions
    $this->get(route('benefit.edit', $benefit))
        ->assertStatus(200)
        ->assertViewHas('benefit', $benefit)
        ->assertViewHas('app_language', Language::latest()->get(['code']));
});

// Test case to update a benefit's data
it('can update a skill', function () {
    // Seeding benefit data using a seeder
    $this->seed([BenefitSeeder::class]);
    $benefit = Benefit::all();
    $languages = loadLanguage();

    // Preparing data for updating benefit translations
    $data = [];
    foreach ($languages as $language) {
        $translationKey = 'name_'.$language->code;
        $translationValue = 'Updated Name in '.$language->code;
        $data[$translationKey] = $translationValue;
    }

    // Making a PUT request to update the benefit and asserting the redirect
    $response = $this->put(route('benefit.update', ['benefit' => $benefit->first()->id]), $data);
    $response->assertRedirect();

    // Asserting that the benefit's name was properly updated in English
    $benefit = Benefit::all()->first();
    $this->assertEquals('Updated Name in en', $benefit['name']);
});

// Test case to delete a benefit without associated companies
it('deletes a benefit without associated companies', function () {
    // Creating a single benefit instance using the factory
    $benefit = Benefit::factory()->create();

    // Making a DELETE request to the destroy route to delete the benefit
    $this->delete(route('benefit.destroy', $benefit));

    // Asserting that the benefit was successfully deleted from the database
    $this->assertDatabaseCount('benefits', 0);
});
