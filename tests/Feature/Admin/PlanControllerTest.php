<?php

use Modules\Plan\Entities\Plan;

/**
 * database refresh howar shomoi migration file theke 3 ta plan auto create hoye jai
 * Set up the testing environment before each test.
 */
beforeEach(function () {
    // Create an admin user using the 'createAdmin' function.
    $this->admin = createAdmin();

    // Seed the database with any necessary data (empty in this case).
    $this->seed([]);

    // Act as the authenticated admin user for the testing session.
    actingAs($this->admin, 'admin');
});

/**
 * Test to ensure that an admin can access the plan index page.
 */
it('admin can visit plan index page', function () {
    // Send a GET request to the plan index route.
    $response = $this->get(route('module.plan.index'));

    // Assert that the response status code is 200 (OK).
    $response->assertStatus(200);

    // Assert that the returned view is 'plan::index'.
    $response->assertViewIs('plan::index');

    // Assert that the view has the expected data variables 'plans' and 'current_language'.
    $response->assertViewHas(['plans', 'current_language']);
});

/**
 * Test to verify that an admin can create a new plan.
 */
it('admin can record new plan', function () {

    // Define the data to be used for creating a new plan.
    $data = [
        'label' => 'Plan unit test',
        'price' => '10',
        'job_limit' => '100',
        'featured_job_limit' => '50',
        'highlight_job_limit' => '25',
        'candidate_cv_view_limitation' => 'limited',
        'candidate_cv_view_limit' => '500',
        'frontend_show' => '1',
        'profile_verify' => '1',
        'description_en' => 'Description',
        'description_bn' => 'বর্ণনা',
        'description_ar' => 'wasaf',
        'description_hi' => 'wasaf',
        'description_fr' => 'wasaf',
        'description_es' => 'wasaf',
        'description_id' => 'wasaf',
        'description_de' => 'wasaf',
    ];

    // Send a POST request to store the new plan using the defined data.
    $response = $this->post(route('module.plan.store'), $data);

    // Assert that the response status code is 302 (a successful redirect).
    $response->assertStatus(302);

    // Expect that the most recently created plan's label matches the provided label.
    expect(Plan::count())->toBe(4);
});

/**
 * Test to ensure that an admin fails to create a new plan due to validation errors.
 */
it('admin fails to record new plan for validation', function () {
    // Define minimal data that should trigger validation errors.
    $data = [
        'frontend_show' => '1',
    ];

    // Send a POST request to store the new plan with incomplete data.
    $response = $this->post(route('module.plan.store'), $data);

    // Assert that the response status code is 302 (a redirect).
    // Also, assert that the session has errors for the specified fields.
    $response->assertStatus(302)
        ->assertSessionHasErrors([
            'label', 'price', 'job_limit', 'featured_job_limit', 'highlight_job_limit',
            'description_en', 'description_bn', 'description_ar',
        ]);
});

/**
 * Test to verify that an admin can access the plan edit page.
 */
it('admin can visit plan edit page', function () {
    // Send a GET request to the plan edit route with a plan ID of 1.
    $response = $this->get(route('module.plan.edit', 1));

    // Assert that the response status code is 200 (OK).
    $response->assertStatus(200);

    // Assert that the returned view is 'plan::edit'.
    $response->assertViewIs('plan::edit');

    // Assert that the view has the expected data variables 'plan' and 'app_languages'.
    $response->assertViewHas(['plan', 'app_languages']);
});

/**
 * Test to ensure that an admin can update any plan.
 */
it('admin can update any plan', function () {
    // Define the data to be used for updating the plan.
    $data = [
        'label' => 'Plan unit test',
        'price' => '10',
        'job_limit' => '100',
        'featured_job_limit' => '50',
        'highlight_job_limit' => '25',
        'candidate_cv_view_limitation' => 'limited',
        'candidate_cv_view_limit' => '500',
        'frontend_show' => '1',
        'profile_verify' => '1',
        'description_en' => 'Description',
        'description_bn' => 'বর্ণনা',
        'description_ar' => 'wasaf',
        'description_hi' => 'wasaf',
        'description_fr' => 'wasaf',
        'description_es' => 'wasaf',
        'description_id' => 'wasaf',
        'description_de' => 'wasaf',
    ];

    // Send a PUT request to update the plan with ID 1 using the defined data.
    $response = $this->put(route('module.plan.update', 1), $data);

    // Assert that the response status code is 302 (a redirect).
    $response->assertStatus(302);

    // Expect that the plan with ID 1 has the updated label.
    expect(Plan::first()->label)->toBe('Plan unit test');
});

/**
 * Test to ensure that an admin fails to update a plan due to validation errors.
 */
it('admin fails to update a plan for validation', function () {
    // Define minimal data that should trigger validation errors.
    $data = [
        'frontend_show' => '1',
    ];

    // Send a PUT request to update the plan with ID 1 with incomplete data.
    $response = $this->put(route('module.plan.update', 1), $data);

    // Assert that the response status code is 302 (a redirect).
    // Also, assert that the session has errors for the specified fields.
    $response->assertStatus(302)
        ->assertSessionHasErrors([
            'label', 'price', 'job_limit', 'featured_job_limit', 'highlight_job_limit',
            'description_en', 'description_bn', 'description_ar',
        ]);
});

/**
 * Test to verify that an admin can delete a plan.
 */
it('admin can delete a plan', function () {

    // Send a PUT request to delete the plan with ID 1 (or perform the delete operation as needed).
    $response = $this->delete(route('module.plan.delete', 1));

    // Assert that the response status code is 302 (a redirect or success status after deletion).
    $response->assertStatus(302);

    // check that plan has 2 data left
    expect(Plan::count())->toBe(2);
});

// it('admin can visit plan description will auto translate', function () {
//     $data = [
//         'textToTranslate'=> 'ok',
//     ];

//     $response = $this->post(route('module.plan.description.translate'),$data);

//     $response->assertStatus(200);
// });
