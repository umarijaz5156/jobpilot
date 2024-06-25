<?php

use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use App\Models\Skill;
use Database\Seeders\ProfessionSeeder;
use Database\Seeders\SkillSeeder;
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

// Test case to check if skills are displayed correctly on the index page
it('can display the skill', function () {

    // Making a GET request to the skill index route and performing assertions
    $this->get(route('skill.index'))
        ->assertStatus(200)
        ->assertViewIs('backend.skill.index');
});

// Test case to validate skill creation
it('create validation', function () {
    // Fetching the latest app languages and preparing validation rules
    $app_languages = Language::latest()->get(['code']);
    $validate_array = [];
    foreach ($app_languages as $language) {
        $validate_array['name_'.$language->code] = 'required|string|max:255';
    }

    // Making a POST request to the skill store route and asserting the status
    $this->post(route('skill.store'), $validate_array)->assertStatus(302);
});

// Test case to create a skill with translations
it('creates a skill with translations', function () {

    // Fetching all languages and preparing data for skill creation
    $languages = loadLanguage();
    $data = [];
    foreach ($languages as $language) {
        $data['name_'.$language->code] = 'Skill Type Name in '.$language->code;
    }

    // Making a POST request to the skill store route to create the skill
    $this->post(route('skill.store'), $data);
    // Asserting that the skill and its translations were properly saved in the database

    $this->assertDatabaseCount('skills', Skill::count());

    $skill_id = Skill::find(Skill::count());
    foreach ($languages as $language) {
        if ($language->locale = 'en') {
            $this->assertDatabaseHas('skill_translations', [

                'skill_id' => $skill_id->id,
                'locale' => $language->code,
                'name' => 'Skill Type Name in '.$language->code,
            ]);
        }
    }
});

// Test case to display the edit skill page
// it('displays the edit skill page', function () {
//     // Creating a single skill instance using the factory
//     $skill = Skill::find(1);

//     // Making a GET request to the edit route and performing assertions
//     $this->get(route('skill.edit', $skill->id) )
//         ->assertStatus(200)
//         ->assertViewHas('skilll', $skill)
//         ->assertViewHas('app_language', Language::latest()->get(['code']));
// });

// Test case to update a skill's data
it('can update a skill', function () {
    // Seeding skill data using a seeder
    $this->seed([SkillSeeder::class]);
    $skill = Skill::all();
    $languages = loadLanguage();

    // Preparing data for updating skill translations
    $data = [];
    foreach ($languages as $language) {
        $translationKey = 'name_'.$language->code;
        $translationValue = 'Updated Name in '.$language->code;
        $data[$translationKey] = $translationValue;
    }

    // Making a PUT request to update the skill and asserting the redirect
    $response = $this->put(route('skill.update', ['skill' => $skill->first()->id]), $data);
    $response->assertRedirect();

    // Asserting that the skill's name was properly updated in English
    $skill = Skill::all()->first();
    $this->assertEquals('Updated Name in en', $skill['name']);
});

// Test case to delete a skill without associated companies
it('deletes a skill without associated companies', function () {
    // Creating a single skill instance using the factory
    $skill = Skill::factory()->create();
    // Making a DELETE request to the destroy route to delete the skill
    $this->delete(route('skill.destroy', $skill));

    // Assert that the role is deleted from the database
    $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
});
