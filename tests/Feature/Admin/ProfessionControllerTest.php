<?php

use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use App\Models\Profession;
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
    $this->seed([ProfessionSeeder::class, LanguageDatabaseSeeder::class]);
});

// Test case to check if professions are displayed correctly on the index page
it('can display the industry types', function () {
    // Fetching all professions
    $profession = Profession::all();

    // Making a GET request to the profession index route and performing assertions
    $this->get(route('profession.index'))
        ->assertStatus(200)
        ->assertViewIs('backend.profession.index')
        ->assertViewHas('professions', $profession);
});

// Test case to validate profession creation
it('create validation', function () {
    // Fetching the latest app languages and preparing validation rules
    $app_languages = Language::latest()->get(['code']);
    $validate_array = [];
    foreach ($app_languages as $language) {
        $validate_array['name_'.$language->code] = 'required|string|max:255';
    }

    // Making a POST request to the profession store route and asserting the status
    $this->post(route('profession.store'), $validate_array)->assertStatus(302);
});

// Test case to create a profession with translations
it('creates a profession with translations', function () {
    // Fetching the first profession (assuming there is at least one) and all languages
    $profession = Profession::first();
    $languages = loadLanguage();
    $data = [];

    foreach ($languages as $language) {
        $data['name_'.$language->code] = $profession->name.$language->code;
    }

    // Making a POST request to the profession store route to create the profession
    $this->post(route('profession.store'), $data);
    $this->assertDatabaseCount('professions', Profession::count());

    // Asserting that the profession and its translations were properly saved in the database
    foreach ($languages as $language) {
        $this->assertDatabaseHas('profession_translations', [
            'profession_id' => $profession->id,
            'locale' => $language->code,
            'name' => $profession->name,
        ]);
    }
});

// Test case to display the edit profession page
it('displays the edit profession page', function () {

    // Making a GET request to the edit route and performing assertions
    $this->get(route('profession.edit', 1))->assertStatus(200);
});

// Test case to update a profession's data
it('can update a profession', function () {
    // Fetching all professions and all languages
    $profession = Profession::all();
    $languages = loadLanguage();
    foreach ($languages as $language) {
        $translationKey = 'name_'.$language->code;
        $translationValue = 'Updated Name in '.$language->code;
        $data[$translationKey] = $translationValue;
    }

    // Making a PUT request to update the profession and asserting the redirect
    $response = $this->put(route('profession.update', ['profession' => $profession->first()->id]), $data);
    $response->assertRedirect();

    // Asserting that the profession's name was properly updated in English
    $profession = Profession::all()->first();
    $this->assertEquals('Updated Name in en', $profession['name']);
});

// Test case to delete a profession without associated companies
it('deletes a profession without associated companies', function () {
    // Making a DELETE request to the destroy route to delete a specific profession (assuming id 2)
    $this->delete(route('profession.destroy', 2))->assertStatus(302);
});
