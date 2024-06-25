<?php

use App\Models\CandidateLanguage;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use Database\Seeders\ProfessionSeeder;

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

it('index method returns candidate languages', function () {

    // Creating some candidate languages
    CandidateLanguage::factory()->create();

    // Making a GET request to the index method
    $response = $this->get(route('admin.candidate.language.index'))->assertStatus(200);

    // Asserting that the candidate languages are displayed on the page
    $response->assertViewHas('candidate_languages');

    // Asserting that the candidate languages are paginated and displayed correctly
    $response->assertSee(CandidateLanguage::first()->name);
});

it('edit method shows candidate language edit form', function () {

    // Create a candidate language
    $candidateLanguage = CandidateLanguage::factory()->create();

    // Making a GET request to the edit method
    $response = $this->get(route('admin.candidate.language.edit', $candidateLanguage))->assertStatus(200);

    // Asserting that the candidate language's name is displayed
    $response->assertSee($candidateLanguage->name);
});

test('update method updates candidate language', function () {

    // Create a candidate language
    $candidateLanguage = CandidateLanguage::factory()->create();

    // New data for updating the candidate language
    $newData = [
        'name' => 'Updated Language Name',
    ];

    // Making a PUT request to the update method
    $this->put(route('admin.candidate.language.update', $candidateLanguage), $newData)->assertStatus(302); // Assuming a successful update redirects

    // Refresh the model data from the database
    $candidateLanguage->refresh();

    // Asserting that the candidate language's name is updated
    $this->assertEquals($newData['name'], $candidateLanguage->name);
});

test('delete method deletes candidate language', function () {

    // Create a candidate language
    $candidateLanguage = CandidateLanguage::factory()->create();

    // Making a DELETE request to the delete method
    $this->delete(route('admin.candidate.language.destroy', $candidateLanguage))->assertStatus(302); // Assuming a successful delete redirects

    // Asserting that the candidate language is deleted from the database
    $this->assertDatabaseMissing('candidate_languages', ['id' => $candidateLanguage->id]);
});
