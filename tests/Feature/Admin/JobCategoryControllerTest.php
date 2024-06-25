<?php

use App\Models\JobCategory;
use App\Models\JobCategoryTranslation;

// Setup before each test
beforeEach(function () {

    // Create an admin user
    $this->admin = createAdmin();

    // Log in as the admin user
    $this->actingAs($this->admin, 'admin');
});

// Test: Admin can visit the job category index page
it('admin can visit job category index page', function () {

    // Send a GET request to the 'jobCategory.index' route
    $response = $this->get(route('jobCategory.index'));

    // Ensure the response status is 200 (OK)
    $response->assertStatus(200);

    // Assert that the response view is 'admin.JobCategory.index'
    $response->assertViewIs('backend.JobCategory.index');

    // Assert that the response view has the specified data variables: 'jobCategories' and 'app_language'
    $response->assertViewHas(['jobCategories', 'app_language']);
});

// Test: Admin can visit the job category edit page
it('admin can visit job category edit page', function () {

    // Create a new job category using factory
    $jc = JobCategory::factory()->create();

    // Send a GET request to the 'jobCategory.edit' route, passing the created job category as a parameter
    $response = $this->get(route('jobCategory.edit', $jc));

    // Ensure the response status is 200 (OK)
    $response->assertStatus(200);

    // Assert that the response view is 'admin.JobCategory.index'
    $response->assertViewIs('backend.JobCategory.index');

    // Assert that the response view has the specified data variables: 'jobCategories' and 'app_language'
    $response->assertViewHas(['jobCategories', 'app_language']);
});

// Test: Admin can record a new job category
it('admin can record new job category', function () {

    // Define the data for a new job category
    $data = [
        'name_en' => 'Motion design',
        'name_bn' => 'গতি নকশা',
        'name_ar' => 'تصميم الحركة',
        'name_hi' => 'تصميم الحركة',
        'name_fr' => 'تصميم الحركة',
        'name_es' => 'تصميم الحركة',
        'name_id' => 'تصميم الحركة',
        'name_de' => 'تصميم الحركة',
        'icon' => 'fas fa-bone',
    ];

    // Send a POST request to the 'jobCategory.store' route with the defined data
    $response = $this->post(route('jobCategory.store'), $data);

    // Ensure the response status is 302 (a redirect)
    $response->assertStatus(302);
    // Expect that there is now one record in the JobCategory model's database table
    expect(JobCategory::count())->toBe(1);
});

// // Test: Admin fails to record a new job category due to validation errors
it('admin fails to record new job category for validation', function () {

    // Define data with missing or incorrect values to trigger validation errors.
    $data = [
        // Include data that is intentionally incorrect or missing to trigger validation errors.
    ];

    // Send a POST request to the 'jobCategory.store' route with the defined data and assert that session has errors.
    // Specifically, we expect errors for 'name_en', 'name_bn', 'name_ar', and 'icon'.
    $response = $this->post(route('jobCategory.store'), $data);

    // Ensure the response status is 302 (a redirect)
    $response->assertStatus(302);

    // Expect that there are no records in the JobCategory model's database table since the data was not valid.
    expect(JobCategory::count())->toBe(0);
});

// // Test: Admin can update any job category
it('admin can update any job category', function () {

    // Create a new job category using factory
    $jc = JobCategory::factory()->create();

    // Define the data for updating the job category
    $data = [
        'name_en' => 'Motion design',
        'name_bn' => 'গতি নকশা',
        'name_ar' => 'تصميم الحركة',
        'name_hi' => 'تصميم الحركة',
        'name_fr' => 'تصميم الحركة',
        'name_es' => 'تصميم الحركة',
        'name_id' => 'تصميم الحركة',
        'name_de' => 'تصميم الحركة',
        'icon' => 'fas fa-bone',
    ];

    // Send a PUT request to the 'jobCategory.update' route, updating the job category with the defined data
    $response = $this->put(route('jobCategory.update', $jc), $data);

    // Ensure the response status is 302 (a redirect)
    $response->assertStatus(302);

    // Expect that the job category translations have been updated correctly
    expect(JobCategoryTranslation::find(1)->name)->toBe('Motion design');
    expect(JobCategoryTranslation::find(2)->name)->toBe('গতি নকশা');
    expect(JobCategoryTranslation::find(3)->name)->toBe('تصميم الحركة');
});

// Test: Admin fails to update any job category due to validation errors
it('admin can fails to update any job category for validation check', function () {

    // Create a new job category using factory
    $jc = JobCategory::factory()->create();

    // Define data with missing or incorrect values to trigger validation errors.
    $data = [
        // Include data that is intentionally incorrect or missing to trigger validation errors.
    ];

    // Send a PUT request to the 'jobCategory.update' route with the defined data and assert that session has errors.
    // Specifically, we expect errors for 'name_en', 'name_bn', 'name_ar', and 'icon'.
    $response = $this->put(route('jobCategory.update', $jc), $data);

    // Ensure the response status is 302 (a redirect)
    $response->assertStatus(302);
});

// Test: Admin can delete any job category
it('admin can delete any job category ', function () {

    // Create a new job category using factory
    $jc = JobCategory::factory()->create();

    // Send a DELETE request to the 'jobCategory.destroy' route to delete the job category
    $response = $this->delete(route('jobCategory.destroy', $jc));

    // Ensure the response status is 302 (a redirect)
    $response->assertStatus(302);

    // Expect that there are no records in the JobCategory model's database table after deletion
    expect(JobCategory::count())->toBe(0);
});
