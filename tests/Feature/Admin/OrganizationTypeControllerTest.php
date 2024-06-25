<?php

use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use App\Models\OrganizationType;
use Database\Seeders\OrganizationTypeSeeder;
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
    $this->seed([ProfessionSeeder::class]);
});

// Test case to check if organization types are displayed correctly on the index page
it('can display the organization types', function () {
    // Seeding language data and creating 3 organization type instances using the factory
    $this->seed([LanguageDatabaseSeeder::class]);
    $organizationTypes = OrganizationType::factory(3)->create();

    // Making a GET request to the organization type index route and performing assertions
    $this->get(route('organizationType.index'))
        ->assertStatus(200)
        ->assertViewIs('backend.organizationType.index')
        ->assertViewHas('organizationTypes', $organizationTypes);
});

// Test case to validate organization type creation
it('account create validation and redirect back to form', function () {
    // Seeding language data
    $this->seed([LanguageDatabaseSeeder::class]);

    // Fetching the latest app languages and preparing validation rules
    $app_languages = Language::latest()->get(['code']);
    $validate_array = [];
    foreach ($app_languages as $language) {
        $validate_array['name_'.$language->code] = 'required|string|max:255';
    }

    // Making a POST request to the organization type store route and asserting the status
    $this->post(route('organizationType.store'), $validate_array)->assertStatus(302);
});

// Test case to create an organization type with translations
it('creates an organization type with translations', function () {
    // Seeding language data
    $this->seed([LanguageDatabaseSeeder::class]);

    // Fetching all languages and preparing data for organization type creation
    $languages = loadLanguage();
    $data = [];
    foreach ($languages as $language) {
        $data['name_'.$language->code] = 'Organization Type Name in '.$language->code;
    }

    // Making a POST request to the organization type store route to create the organization type
    $this->post(route('organizationType.store'), $data);

    // Asserting that the organization type and its translations were properly saved in the database
    $this->assertDatabaseCount('organization_types', 1);
    $organizationType = OrganizationType::first();
    foreach ($languages as $language) {
        $this->assertDatabaseHas('organization_type_translations', [
            'organization_type_id' => $organizationType->id,
            'locale' => $language->code,
            'name' => 'Organization Type Name in '.$language->code,
        ]);
    }
});

// Test case to display the edit page for an organization type
it('displays the edit organization type page', function () {
    // Seeding language data and creating a single organization type instance using the factory
    $this->seed([LanguageDatabaseSeeder::class]);
    $organizationType = OrganizationType::factory()->create();

    // Making a GET request to the edit route and performing assertions
    $response = $this->get(route('organizationType.edit', $organizationType));
    $response
        ->assertStatus(200)
        ->assertViewHas('organizationType', $organizationType)
        ->assertViewHas('organizationTypes', OrganizationType::all())
        ->assertViewHas('app_language', Language::latest()->get(['code']));
});

// Test case to update an organization type's data
it('can update an organization type', function () {
    // Seeding language and organization type data
    $this->seed([LanguageDatabaseSeeder::class, OrganizationTypeSeeder::class]);
    $organizationType = OrganizationType::all();
    $languages = loadLanguage();

    // Preparing data for updating organization type translations
    $data = [];
    foreach ($languages as $language) {
        $translationKey = 'name_'.$language->code;
        $translationValue = 'Updated Name in '.$language->code;
        $data[$translationKey] = $translationValue;
    }

    // Making a PUT request to update the organization type and asserting the redirect
    $response = $this->put(route('organizationType.update', ['organizationType' => $organizationType->first()->id]), $data);
    $response->assertRedirect();

    // Asserting that the organization type's name was properly updated in English
    $organizationType = OrganizationType::all()->first();
    $this->assertEquals('Updated Name in en', $organizationType['name']);
});

// Test case to delete an organization type without associated companies
it('deletes an organization type without associated companies', function () {
    // Seeding language data and creating a single organization type instance using the factory
    $this->seed([LanguageDatabaseSeeder::class]);
    $organizationType = OrganizationType::factory()->create();

    // Making a DELETE request to the destroy route to delete the organization type
    $this->delete(route('organizationType.destroy', $organizationType));

    // Asserting that the organization type was successfully deleted from the database
    $this->assertDatabaseCount('organization_types', 0);
});
