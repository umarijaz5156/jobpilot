<?php

use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use App\Models\Page;
use Database\Seeders\PageSeeder;
use Database\Seeders\ProfessionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

test('get custom page index blade in admin', function () {
    $response = $this->get(route('settings.pages.index'));
    $response->assertStatus(200); // Change to 200 if the page is expected to load successfully
});

test('get custom page create blade in admin', function () {
    $response = $this->get(route('settings.pages.create'));
    $response->assertStatus(200); // Change to 200 if the page is expected to load successfully
});

test('can store a custom page data', function () {
    $data = [
        'title' => 'Test Title',
        'slug' => 'test-slug',
        'footer_column_position' => 1,
        'content' => 'Test content',
        'meta_title' => 'Test Meta Title',
        'meta_description' => 'Test Meta Description',
    ];

    $response = $this->post(route('settings.pages.store'), $data);

    $response->assertStatus(302);
});

test('can view the edit page', function () {
    $this->seed(PageSeeder::class);
    $page = Page::first();
    $response = $this->get(route('settings.pages.edit', ['page' => $page->id]));
    $response->assertStatus(200);
    $response->assertViewIs('backend.pages.edit');
    $response->assertViewHas('page', $page);
});

test('can update a page successfully', function () {
    $this->seed(PageSeeder::class);
    $page = Page::first();
    $newTitle = 'Updated Title';
    $newContent = 'Updated Content';
    $newPosition = 2;
    $newMetaTitle = 'Updated Meta Title';
    $newMetaDescription = 'Updated Meta Description';
    $newSlug = 'updated-title';
    $fakeImage = UploadedFile::fake()->image('meta_image.jpg');
    $filePath = Storage::disk('public')->put('images/pages', $fakeImage);
    $response = $this->actingAs(createAdmin())
        ->put(route('settings.pages.update', ['page' => $page->id]), [
            'title' => $newTitle,
            'slug' => $newSlug,
            'footer_column_position' => $newPosition,
            'content' => $newContent,
            'meta_title' => $newMetaTitle,
            'meta_description' => $newMetaDescription,
            'meta_image' => $filePath,
        ]);
    $response->assertStatus(302);
    $this->assertEquals($newTitle, $page->fresh()->title);
    $this->assertEquals($newSlug, $page->fresh()->slug);
    $this->assertEquals($newPosition, $page->fresh()->footer_column_position);
    $this->assertEquals($newContent, $page->fresh()->content);
    $this->assertEquals($newMetaTitle, $page->fresh()->meta_title);
    $this->assertEquals($newMetaDescription, $page->fresh()->meta_description);
    Storage::disk('public')->assertExists($page->fresh()->meta_image);
    $response->assertSessionHas('success', 'Page updated successfully');
});

test('can delete a page', function () {
    $this->seed(PageSeeder::class);
    $page = Page::first();
    $this->assertNotNull($page);
    $response = $this->actingAs(createAdmin())
        ->delete(route('settings.pages.delete', ['page' => $page->id]));
    $response->assertStatus(302);
    $this->assertNull(Page::find($page->id));
    $response->assertSessionHasNoErrors();
});

test('show page on header', function () {
    $this->seed(PageSeeder::class);
    $page = Page::first();

    $response = $this->json('GET', route('settings.pages.header.status', ['id' => $page->id, 'status' => 1]));

    $response->assertStatus(200);
    $this->assertDatabaseHas('pages', [
        'id' => $page->id,
        'show_header' => 1,
    ]);
});

test('show page on footer', function () {
    $this->seed(PageSeeder::class);
    $page = Page::first();

    $response = $this->json('GET', route('settings.pages.footer.status', ['id' => $page->id, 'status' => 1]));

    $response->assertStatus(200);
    $this->assertDatabaseHas('pages', [
        'id' => $page->id,
        'show_footer' => 1,
    ]);
});

// test('show custom page', function () {
//         $this->seed(PageSeeder::class);
//         $page = Page::first();
//         $response = $this->get(route('showCustomPage', ['slug' => $page->slug]));
//         $response->assertStatus(200);
// });

test('change show in header', function () {
    $this->seed(PageSeeder::class);
    $page = Page::first();

    $response = $this->json('get', route('settings.pages.header.status', ['id' => $page->id, 'status' => 1]));

    $response->assertStatus(200);
    $this->assertDatabaseHas('pages', [
        'id' => $page->id,
        'show_header' => 1,
    ]);
});

test('change show in footer', function () {
    $this->seed(PageSeeder::class);
    $page = Page::first();

    $response = $this->json('get', route('settings.pages.footer.status', ['id' => $page->id, 'status' => 1]));

    $response->assertStatus(200);
    $this->assertDatabaseHas('pages', [
        'id' => $page->id,
        'show_footer' => 1,
    ]);
});
