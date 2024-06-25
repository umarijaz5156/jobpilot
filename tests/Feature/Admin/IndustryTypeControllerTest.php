<?php

use App\Models\Education;
use App\Models\Experience;
use App\Models\IndustryType;
use App\Models\JobRole;
use Database\Seeders\IndustryTypeSeeder;
use Database\Seeders\ProfessionSeeder;
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
    $this->seed([LanguageDatabaseSeeder::class, ProfessionSeeder::class]);
});

// Test case to check if industry types are displayed correctly on the index page
it('can display the industry types', function () {
    // Creating 3 industry type instances using the factory
    $industryTypes = IndustryType::factory(3)->create();

    // Making a GET request to the industry type index route and performing assertions
    $this->get(route('industryType.index'))
        ->assertStatus(200)
        ->assertViewIs('backend.industryType.index')
        ->assertViewHas('industrytypes', $industryTypes);
});

// Test case to validate industry type creation
it('create validation', function () {
    // Fetching the latest app languages and preparing validation rules
    $app_languages = Language::latest()->get(['code']);
    $validate_array = [];
    foreach ($app_languages as $language) {
        $validate_array['name_'.$language->code] = 'required|string|max:255';
    }

    // Making a POST request to the industry type store route and asserting the status
    $this->post(route('industryType.store'), $validate_array)->assertStatus(302);
});

// Test case to create an industry type with translations
it('creates an industry type with translations', function () {
    // Fetching all languages and preparing data for industry type creation
    $languages = loadLanguage();
    $data = [];
    foreach ($languages as $language) {
        $data['name_'.$language->code] = 'Industry Type Name in '.$language->code;
    }

    // Making a POST request to the industry type store route to create the industry type
    $this->post(route('industryType.store'), $data);

    // Asserting that the industry type and its translations were properly saved in the database
    $this->assertDatabaseCount('industry_types', 1);
    $industryType = IndustryType::first();
    foreach ($languages as $language) {
        $this->assertDatabaseHas('industry_type_translations', [
            'industry_type_id' => $industryType->id,
            'locale' => $language->code,
            'name' => 'Industry Type Name in '.$language->code,
        ]);
    }
});

// Test case to display the edit page for an industry type
it('displays the edit industry type page', function () {
    // Creating a single industry type instance using the factory
    $industryType = IndustryType::factory()->create();

    // Making a GET request to the edit route and performing assertions
    $this->get(route('industryType.edit', $industryType))->assertStatus(200)
        ->assertViewHas('industryType', $industryType)
        ->assertViewHas('app_language', Language::latest()->get(['code']));
});

// Test case to update an industry type's data
it('can update an industry type', function () {
    // Seeding industry type data using a seeder
    $this->seed([IndustryTypeSeeder::class]);
    $industryType = IndustryType::all();
    $languages = loadLanguage();

    // Preparing data for updating industry type translations
    $data = [];
    foreach ($languages as $language) {
        $translationKey = 'name_'.$language->code;
        $translationValue = 'Updated Name in '.$language->code;
        $data[$translationKey] = $translationValue;
    }

    // Making a PUT request to update the industry type and asserting the redirect
    $response = $this->put(route('industryType.update', ['industryType' => $industryType->first()->id]), $data);
    $response->assertRedirect();

    // Asserting that the industry type's name was properly updated in English
    $industryType = IndustryType::all()->first();
    $this->assertEquals('Updated Name in en', $industryType['name']);
});

// Test case to delete an industry type without associated companies
it('deletes an industry type without associated companies', function () {
    // Creating a single industry type instance using the factory
    $industryType = IndustryType::factory()->create();

    // Making a DELETE request to the destroy route to delete the industry type
    $this->delete(route('industryType.destroy', $industryType));

    // Asserting that the industry type was successfully deleted from the database
    $this->assertDatabaseCount('organization_types', 0);
});
