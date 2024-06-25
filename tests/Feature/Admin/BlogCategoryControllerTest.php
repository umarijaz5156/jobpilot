<?php

/**
 * Set up the testing environment before each test.
 */

use Illuminate\Http\UploadedFile;
use Modules\Blog\Entities\PostCategory;

beforeEach(function () {
    // Create an admin user using the 'createAdmin' function.
    $this->admin = createAdmin();

    // Seed the database with any necessary data (empty in this case).
    $this->seed([]);

    // Act as the authenticated admin user for the testing session.
    actingAs($this->admin, 'admin');
});

// /**
//  * Test to ensure that an admin can visit the blog category index page.
//  */
// it('test admin can visit blog category index page', function () {
//     // Send a GET request to the blog category index route.
//     $response = $this->get(route('module.category.index'));

//     // Assert that the response status code is 200 (OK).
//     $response->assertStatus(200);

//     // Assert that the returned view is 'blog::postcategory.index'.
//     $response->assertViewIs('blog::postcategory.index');

//     // Assert that the view has the expected data variable 'categories'.
//     $response->assertViewHas(['categories']);
// });

// // /**
// //  * Test to ensure that an admin can visit the blog category create page.
// //  */
// it('test admin can visit blog category create page', function () {
//     // Send a GET request to the blog category create route.
//     $response = $this->get(route('module.category.create'));

//     // Assert that the response status code is 200 (OK).
//     $response->assertStatus(200);

//     // Assert that the returned view is 'blog::postcategory.create'.
//     $response->assertViewIs('blog::postcategory.create');
// });

// /**
//  * Test to ensure that an admin can store a new blog category.
//  */
// it('test admin can store new blog category', function () {
//     // Define the data for creating a new blog category.
//     $data = [
//         'author_id' => '1',
//         'name' => 'Airtel',
//     ];

//     // Send a POST request to the blog category store route with the defined data.
//     $response = $this->post(route('module.category.store'), $data);

//     // Assert that the response status code is 302 (a redirect).
//     $response->assertStatus(302);

//     // Expect that there is one blog category record in the database.
//     expect(PostCategory::count())->toBe(1);
// });

/**
 * Test to ensure that an admin can update .
 */
it('test admin fails to store new blog category for validation', function () {
    // Define the data for creating a new blog category.
    $data = [
        'author_id' => '1',
        'image' => UploadedFile::fake()->create('category.jpg', 1024),
    ];

    // Send a POST request to the blog category store route with the defined data.
    $response = $this->post(route('module.category.store'), $data);

    // Assert that the response status code is 302 (a redirect).
    $response->assertStatus(302);

    // Expect that there is one blog category record in the database.
    expect(PostCategory::count())->toBe(0);
});

/**
 * Test to ensure that an admin can visit the blog category edit page.
 */
it('test admin can visit blog category edit page', function () {
    // Create a test blog category using the factory.
    PostCategory::factory()->create();

    // Send a GET request to the blog category edit route for the created category (ID 1 in this case).
    $response = $this->get(route('module.category.edit', 1));

    // Assert that the response status code is 200 (OK).
    $response->assertStatus(200);

    // Assert that the returned view is 'blog::postcategory.edit'.
    $response->assertViewIs('blog::postcategory.edit');

    // Assert that the view has the expected data variable 'category'.
    $response->assertViewHas('category');
});

/**
 * Test to ensure that an admin can update a new blog category.
 */
it('test admin can update blog category', function () {
    PostCategory::factory()->create();
    // Define the data for creating a new blog category.
    $data = [
        'title' => 'Airtel',
        'slug' => 'airtel',
    ];

    // dd(PostCategory::first());

    // Send a PUT request to the blog category update route with the defined data.
    $response = $this->put(route('module.category.update', 1), $data);

    // Assert that the response status code is 302 (a redirect).
    $response->assertStatus(302);

    // Expect that there is one blog category record in the database.
    expect(PostCategory::first()->name)->toBe('Airtel');
});

/**
 * Test to ensure that an admin can delete any blog category.
 */
it('test admin can delete a new post category', function () {
    // Create a new PostCategory using a factory.
    PostCategory::factory()->create();

    // Send a delete request to the blog category delete route with the defined data.
    $response = $this->delete(route('module.category.delete', 1));

    // Assert that the response status code is 302 (a redirect).
    $response->assertStatus(302);

    // Confirming that the category has been deleted.
    expect(PostCategory::count())->toBe(0);
});
