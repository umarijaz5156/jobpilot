<?php

use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use App\Models\Tag;
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

// Test case to check if tags are displayed correctly on the index page
it('can display the tag', function () {
    // Creating 3 tag instances using the factory
    $tags = Tag::factory(3)->create();

    // Making a GET request to the tags index route and performing assertions
    $this->get(route('tags.index'))
        ->assertStatus(200)
        ->assertViewIs('backend.tag.index')
        ->assertViewHas('tags', function ($viewTags) use ($tags) {
            return $tags->pluck('id')->diff($viewTags->pluck('id'))->isEmpty();
        });
});

// Test case to validate tag creation
it('create validation', function () {
    // Fetching the latest app languages and preparing validation rules
    $app_languages = Language::latest()->get(['code']);
    $validate_array = [];
    foreach ($app_languages as $language) {
        $validate_array['name_'.$language->code] = 'required|string|max:255';
    }

    // Making a POST request to the tags store route and asserting the status
    $this->post(route('tags.store'), $validate_array)->assertStatus(302);
});

// Test case to create a tag with translations
it('creates a tag with translations', function () {
    // Fetching all languages and preparing data for tag creation
    $languages = loadLanguage();
    $data = [];
    foreach ($languages as $language) {
        $data['name_'.$language->code] = 'Tag Type Name in '.$language->code;
    }

    // Making a POST request to the tags store route to create the tag
    $this->post(route('tags.store'), $data);

    // Asserting that the tag and its translations were properly saved in the database
    $this->assertDatabaseCount('tags', 1);
    $tag = Tag::first();
    foreach ($languages as $language) {
        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => $language->code,
            'name' => 'Tag Type Name in '.$language->code,
        ]);
    }
});

// Test case to display the edit tag page
it('displays the edit tag page', function () {
    // Creating a single tag instance using the factory
    $tag = Tag::factory()->create();

    // Making a GET request to the edit route and performing assertions
    $this->get(route('tags.edit', $tag))
        ->assertStatus(200)
        ->assertViewHas('tag', $tag)
        ->assertViewHas('app_language', Language::latest()->get(['code']));
});

// Test case to update a tag's data
it('can update a tag', function () {
    // Seeding tag data using a seeder
    $this->seed([TagSeeder::class]);
    $tag = Tag::all();
    $languages = loadLanguage();

    // Preparing data for updating tag translations
    $data = [];
    foreach ($languages as $language) {
        $translationKey = 'name_'.$language->code;
        $translationValue = 'Updated Name in '.$language->code;
        $data[$translationKey] = $translationValue;
    }

    // Making a PUT request to update the tag and asserting the redirect
    $response = $this->put(route('tags.update', ['tag' => $tag->first()->id]), $data);
    $response->assertRedirect();

    // Asserting that the tag's name was properly updated in English
    $tag = Tag::all()->first();
    $this->assertEquals('Updated Name in en', $tag['name']);
});

// Test case to delete a tag without associated companies
it('deletes a tag without associated companies', function () {
    // Creating a single tag instance using the factory
    $tag = Tag::factory()->create();

    // Making a DELETE request to the destroy route to delete the tag
    $this->delete(route('tags.destroy', $tag));

    // Asserting that the tag was successfully deleted from the database
    $this->assertDatabaseCount('tags', 0);
});

// Add a comment explaining the purpose or details of the tests
