<?php

use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use Database\Seeders\ProfessionSeeder;
use Modules\Faq\Entities\Faq;
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

it('can list all FAQs', function () {
    // Assuming you have some Faq and FaqCategory records in your database
    $faqCategory = FaqCategory::factory()->create();
    $faq = Faq::factory()->create(['faq_category_id' => $faqCategory->id]);

    $response = $this->get(route('module.faq.index'));

    $response->assertStatus(200);
    $response->assertViewHas('faq_category');
    $response->assertViewHas('faqs_group');
    $response->assertViewHas('all_faqs_count');
});

it('can list FAQs by category slug', function () {
    $faqCategory = FaqCategory::factory()->create();
    $faq = Faq::factory()->create(['faq_category_id' => $faqCategory->id]);

    $response = $this->get(route('module.faq.index', ['slug' => $faqCategory->slug]));

    $response->assertStatus(200);
    $response->assertViewHas('faq_category');
    $response->assertViewHas('faqs_group');
    $response->assertViewHas('all_faqs_count');
});

it('can show the edit form for a FAQ', function () {
    $faqCategory = FaqCategory::factory()->create();
    $faq = Faq::factory()->create(['faq_category_id' => $faqCategory->id]);

    $response = $this->get(route('module.faq.edit', ['faq' => $faq->id]));

    $response->assertStatus(200);
    $response->assertViewIs('faq::edit'); // Make sure to adjust the view name as needed
    $response->assertViewHas('faq', $faq);
});

it('can update a FAQ', function () {
    $faqCategory = FaqCategory::factory()->create();
    $faq = Faq::factory()->create(['faq_category_id' => $faqCategory->id]);

    $updatedData = [
        'code' => 'updated-code', // Provide a valid code
        'faq_category_id' => $faqCategory->id, // Provide a valid faq category id
        'question' => 'Updated Question',
        'answer' => 'Updated Answer',
        // Add other fields you want to update
    ];

    $this->put(route('module.faq.update', ['faq' => $faq->id]), $updatedData);

    // Check if the FAQ in the database has been updated with the new data
    $this->assertDatabaseHas('faqs', $updatedData + ['id' => $faq->id]);
});

it('can delete a FAQ', function () {
    $faqCategory = FaqCategory::factory()->create();
    $faq = Faq::factory()->create(['faq_category_id' => $faqCategory->id]);

    $response = $this->delete(route('module.faq.destroy', ['faq' => $faq->id]));

    $response->assertRedirect(route('module.faq.index'));

    // Check if the FAQ has been deleted from the database
    $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
});
