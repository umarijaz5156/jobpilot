<?php

use Illuminate\Http\UploadedFile;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostCategory;
use Modules\Language\Database\Seeders\LanguageDatabaseSeeder;

beforeEach(function () {
    // Create an admin user using the 'createAdmin' function.
    $this->admin = createAdmin();

    // Seed the database with any necessary data (empty in this case).
    $this->seed([]);

    // Act as the authenticated admin user for the testing session.
    actingAs($this->admin, 'admin');

    $this->seed(LanguageDatabaseSeeder::class);
    PostCategory::factory()->create();
    Post::factory()->create();
});

it('admin can visit blog index page', function () {
    // Send a GET request to the blog index route.
    $response = $this->get(route('module.blog.index'));

    // Assert that the response status code is 200 (OK).
    $response->assertStatus(200);

    // Assert that the returned view is 'blog::index'.
    $response->assertViewIs('blog::index');

    // Assert that the view has the expected data variables.
    $response->assertViewHas(['blogs', 'categories', 'authors', 'totalComments', 'totalAuthor',  'totalDraft', 'totalPublished', 'languages']);
});

it('admin can visit blog create page', function () {
    // Send a GET request to the blog create route.
    $response = $this->get(route('module.blog.create'));

    // Assert that the response status code is 200 (OK).
    $response->assertStatus(200);

    // Assert that the returned view is 'blog::index'.
    $response->assertViewIs('blog::create');

    // Assert that the view has the expected data variables.
    $response->assertViewHas(['categories', 'languages']);
});

it('admin can post a new blog', function () {
    // Define the data for the new blog.
    $data = [
        'author_id' => '2',
        'title' => 'Job pilot testing',
        'locale' => 'en',
        'category_id' => '1',
        'short_description' => 'ok',
        'description' => 'this is a description for testing',
        'image' => UploadedFile::fake()->create('thumbnail.jpg', 1024),
    ];

    // Send a POST request to the blog store route with the defined data.
    $response = $this->post(route('module.blog.store'), $data);

    // Assert that the response status code is 302 (a redirect).
    $response->assertStatus(302);

    // Expect that a Post record with ID 2 has been created and has the title 'Job pilot testing'.
    expect(Post::find(2)->title)->toBe('Job pilot testing');
});

it('admin fails to post a new blog for validation', function () {
    // Define the data for the new blog with some missing input value.
    $data = [
        'locale' => 'en',
    ];

    $response = $this->post(route('module.blog.store'), $data)
        ->assertSessionHasErrors(['title', 'category_id', 'short_description', 'description', 'image']);

    // Assert that the response status code is 302 (a redirect).
    $response->assertStatus(302);

    expect(Post::count())->toBe(1);
});

it('admin can visit blog edit page', function () {
    // Send a GET request to the 'module.blog.edit' route with parameter 1.
    $response = $this->get(route('module.blog.edit', 1));

    // Assert that the response status code is 200 (OK).
    $response->assertStatus(200);

    // Assert that the returned view is 'blog::edit'.
    $response->assertViewIs('blog::edit');

    // Assert that the view returned contains specific data variables.
    $response->assertViewHas(['categories', 'post', 'languages']);
});

it('admin can update any blog', function () {
    //  Define the data for the new blog.
    $data = [
        'author_id' => '2',
        'title' => 'Job pilot testing',
        'locale' => 'en',
        'category_id' => '1',
        'short_description' => 'ok',
        'description' => 'this is a description for testing',
        'image' => UploadedFile::fake()->create('thumbnail.jpg', 1024),
    ];

    // Send a PUT request to the blog update route with the defined data.
    $response = $this->put(route('module.blog.update', 1), $data);

    // Assert that the response status code is 302 (a redirect).
    $response->assertStatus(302);

    // Expect that a Post record with ID 2 has been created and has the title 'Job pilot testing'.
    expect(Post::find(1)->title)->toBe('Job pilot testing');
});

it('admin fails to update any blog', function () {
    //  Define the data for the new blog.

    $title = Post::first()->title;
    $data = [
        //
    ];

    // Send a PUT request to the blog update route with the defined data for check session errors.
    $response = $this->put(route('module.blog.update', 1), $data)
        ->assertSessionHasErrors(['title', 'short_description', 'description', 'category_id', 'locale']);

    // Assert that the response status code is 302 (a redirect).
    $response->assertStatus(302);

    // Expect that a Post record with ID 2 has been created and has the title 'Job pilot testing'.
    expect(Post::find(1)->title)->toBe($title);
});

it('admin can delete any blog', function () {
    // Send a DELETE request to the 'module.blog.destroy' route, presumably to delete a specific blog.
    $response = $this->delete(route('module.blog.destroy', 1));

    // Assert that the response status code is 302 (typically indicates a redirection).
    $response->assertStatus(302);

    // Expect that the count of the 'Post' model (assuming it represents blogs) is now 1.
    expect(Post::count())->toBe(0);
});
