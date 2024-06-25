<?php

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    // Create an admin user using the 'createAdmin' function.
    $this->admin = createAdmin();

    // Act as the authenticated admin user for the testing session.
    actingAs($this->admin, 'admin');
    $this->seed([SettingSeeder::class]);
});

it('admin can visit genral settings page', function () {
    $this->get(route('settings.general')) // Send a GET request to the 'settings.general' route.
        ->assertStatus(200) // Assert that the response status code is 200 (OK).
        ->assertViewIs('backend.settings.pages.general') // Assert that the view being returned is 'backend.settings.pages.general'.
        ->assertViewHas(['timezones', 'currencies', 'countries', 'setting']); // Assert that the view has specific data variables.
});

// This test checks if an admin can successfully update the general settings.
it('admin can update genral settings', function () {
    // Prepare data for updating the general settings.
    $data = [
        'name' => 'Jobpilot test',
        'email' => 'test@lomeyo.com',
        // Uncomment the following lines if you need to upload files.
        // "dark_logo" => UploadedFile::fake()->create('dark_logo.jpg', 1024),
        // "light_logo" => UploadedFile::fake()->create('light_logo.jpg', 1024),
        'favicon_image' => UploadedFile::fake()->create('favicon_image.jpg', 1024),
    ];

    // Send a PUT request to update the general settings and assert the response status.
    $this->put(route('settings.general.update'), $data)
        ->assertStatus(302); // Assuming a 302 is the expected redirect status code.

    // Check that the email setting has been updated to 'test@lomeyo.com'.
    expect(Setting::first()->email)->toBe('test@lomeyo.com');
});

it('admin can update app configuartion', function () {
    // Prepare data for updating the general settings.
    $data = [
        'name' => 'Jobpilot test',
        'email' => 'test@lomeyo.com',
        // Uncomment the following lines if you need to upload files.
        // "dark_logo" => UploadedFile::fake()->create('dark_logo.jpg', 1024),
        // "light_logo" => UploadedFile::fake()->create('light_logo.jpg', 1024),
        'favicon_image' => UploadedFile::fake()->create('favicon_image.jpg', 1024),
    ];

    // Send a PUT request to update the general settings and assert the response status.
    $this->put(route('settings.general.update'), $data)
        ->assertStatus(302); // Assuming a 302 is the expected redirect status code.

    // Check that the email setting has been updated to 'test@lomeyo.com'.
    expect(Setting::first()->email)->toBe('test@lomeyo.com');
});

it('admin can update recapta configuartion', function () {

    Config::set('app.env', 'testing');
    // Prepare data for updating the general settings.
    $data = [
        'nocaptcha_key' => 'Testing',
        'nocaptcha_secret' => 'Secret',
        'status' => '1',
    ];

    // Send a PUT request to update the general settings and assert the response status.
    $this->put(route('settings.recaptcha.update'), $data)
        ->assertStatus(302); // Assuming a 302 is the expected redirect status code.

    expect(env('NOCAPTCHA_SITEKEY'))->toBe('Testing');
    expect(env('NOCAPTCHA_SECRET'))->toBe('Secret');
});

it('admin fails to update recapta configuartion for validation', function () {

    Config::set('app.env', 'testing');
    // Prepare data for updating the general settings.
    $data = [

    ];

    // Send a PUT request to update the general settings and assert the response status.
    $this->put(route('settings.recaptcha.update'), $data)
        ->assertSessionHasErrors(['nocaptcha_key', 'nocaptcha_secret'])
        ->assertStatus(302); // Assuming a 302 is the expected redirect status code.
});

it('admin can visit theme index page', function () {
    $this->get(route('settings.theme'))
        ->assertStatus(200)
        ->assertViewIs('backend.settings.pages.theme');
});

it('admin can update color form settings', function () {
    $data = [
        'sidebar_color' => '#092433',
        'nav_color' => '#0A243E',
        'sidebar_txt_color' => '#C1D6F0',
        'nav_txt_color' => '#C1D6F0',
        'main_color' => '#0A65CC',
        'accent_color' => '#487CB8',
        'frontend_primary_color' => '#0A65CC',
        'frontend_secondary_color' => '#487CB8',
    ];

    $this->put(route('settings.theme.update'), $data)
        ->assertStatus(302);

    expect(Setting::first()->sidebar_color)->toBe('#092433');
});

it('admin can visit mail index page', function () {
    $this->get(route('settings.email'))
        ->assertStatus(200)
        ->assertViewIs('backend.settings.pages.mail');
});
