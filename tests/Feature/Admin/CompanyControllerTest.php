<?php

use App\Models\Company;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\IndustryTypeSeeder;
use Database\Seeders\OrganizationTypeSeeder;
use Database\Seeders\TeamSizeSeeder;
use Illuminate\Http\UploadedFile;

// Setup before each test
beforeEach(function () {

    // Create an admin user
    $this->admin = createAdmin();

    // Log in as the admin user
    $this->actingAs($this->admin, 'admin');

    // Seed the database with required data
    $this->seed([
        OrganizationTypeSeeder::class,
        IndustryTypeSeeder::class,
        TeamSizeSeeder::class,
    ]);
});

it('admin can visit company index page', function () {
    // Seed the database with company data using CompanySeeder
    $this->seed(CompanySeeder::class);

    // Send a GET request to the company index page
    $response = $this->get(route('company.index'));

    // Assert that the response status is 200 (success)
    $response->assertStatus(200);

    // Assert that the view being returned is 'admin.company.index'
    $response->assertViewIs('backend.company.index');

    // Assert that the view has specific data variables: 'companies', 'industry_types', 'organization_types'
    $response->assertViewHas(['companies', 'industry_types', 'organization_types']);
});

it('admin can visit company details page', function () {
    // Seed the database with company data using CompanySeeder
    $this->seed(CompanySeeder::class);

    // Send a GET request to the company index page
    $response = $this->get(route('company.show', 1));

    // Assert that the response status is 200 (success)
    $response->assertStatus(200);

    // Assert that the view being returned is 'admin.company.index'
    $response->assertViewIs('backend.company.show');

});

// Test: Admin can create a company
it('admin can create company', function () {

    // Store location to session as a reuired data
    $data = [
        'lat' => '23.757853442383',
        'lng' => '90.411270491741',
        'country' => 'Bangladesh',
        'region' => 'Dhaka Division',
        'district' => 'Dhaka District',
        'place' => 'Dhaka',
        'exact_location' => 'Dhaka District, Dhaka Division, Bangladesh',
    ];

    // Store the data in the session
    session()->put('location', $data);

    // Send a POST request to create a company
    $response = $this->post(
        route('company.store'),
        [
            'name' => 'Airtel',
            'username' => 'admina@mail.com',
            'email' => 'sahaapo@gmail.com',
            'password' => 'password',
            'contact_phone' => '01616657585',
            'contact_email' => 'sahaapo@gmail.com',
            'social_media' => [
                0 => 'twitter',
            ],
            'url' => [
                0 => 'https://facebookasd.com',
            ],
            'organization_type_id' => '1',
            'industry_type_id' => '1',
            'team_size_id' => '3',
            'website' => 'https://www.facebook.com',
            'establishment_date' => '21-08-2023',
            'bio' => 'ok',
            'vision' => 'ok',
            'logo' => UploadedFile::fake()->create('logo.jpg', 1024),
            'image' => UploadedFile::fake()->create('image.jpg', 1024),
        ]
    );

    // Assert that the response status is 302 (redirect)
    $response->assertStatus(302);

    // Expect that no company has been created in the database
    expect(Company::count())->toBe(1);
});

// Test: Admin fails to create a company due to validation errors
it('admin fails to create company for validation', function () {

    // Send a POST request to create a company with missing data
    $response = $this->post(
        route('company.store'),
        [
            'name' => 'Airtel',
            'username' => 'admina@mail.com',
            'social_media' => [
                0 => 'twitter',
            ],
            'url' => [
                0 => 'https://facebookasd.com',
            ],
            'team_size_id' => '3',
            'website' => 'https://www.facebook.com',
            'establishment_date' => '21-08-2023',
            'bio' => 'ok',
            'vision' => 'ok',
            'logo' => UploadedFile::fake()->create('logo.jpg', 1024),
            'image' => UploadedFile::fake()->create('image.jpg', 1024),
        ]
    )->assertSessionHasErrors(['email', 'password', 'contact_phone', 'contact_email', 'organization_type_id', 'industry_type_id']);

    // Assert that the response status is 302 (redirect)
    $response->assertStatus(302);

    // Expect that no company has been created in the database
    expect(Company::count())->toBe(0);
});

it('admin can visit company edit page', function () {
    // create a company using factory
    Company::factory()->create();

    loadSetting();
    // Send a GET request to the company index page
    $response = $this->get(route('company.edit', 1));

    // Assert that the response status is 200 (success)
    $response->assertStatus(200);

    // Assert that the view being returned is 'admin.company.index'
    $response->assertViewIs('backend.company.edit');

});

// Test: Admin can update a company
it('admin can update company', function () {

    // Create a company using the factory
    $company = Company::factory()->create();

    // Send a PUT request to update the company
    $response = $this->put(
        route('company.update', $company),
        [
            'name' => 'opusaha',
            'username' => 'vWc64rIUSK',
            'email' => 'sahaapos@gmail.com',
            'password' => null,
            'contact_phone' => 'dkadBTLZYV',
            'contact_email' => 'fwzyz@c2gn.com',
            'social_media' => [
                0 => 'facebook',
            ],
            'url' => [
                0 => 'https://facebook.com',
            ],
            'organization_type_id' => '2',
            'industry_type_id' => '11',
            'team_size_id' => '4',
            'website' => 'https://www.facebook.com',
            'establishment_date' => '11-08-2023',
            'bio' => '<p>okok</p>',
            'vision' => '<p>okok</p>',
        ]
    );

    // Assert that the response status is 302 (redirect)
    $response->assertStatus(302);

    // Expect that the company's current data is updated like current inputed value'
    expect(Company::first()->organization_type_id)->toBe(2);
    expect(Company::first()->industry_type_id)->toBe(11);
    expect(Company::first()->team_size_id)->toBe(4);
});

// // Test: Admin fails to create a company due to validation errors
// it('admin fails to update company for validation', function () {

//     // Create a company using the factory
//     $company = Company::factory()->create(['name' => 'Bangladesh']);
//     // Send a POST request to create a company with missing data
//     $response = $this->put(
//         route('company.update', $company),
//         [
//             "contact_phone" => "01616657585",
//             "contact_email" => "sahaapo@gmail.com",
//             "social_media" => [
//                 0 => "twitter",
//             ],
//             "url" => [
//                 0 => "https://youtube.com",
//             ],
//             "team_size_id" => "3",
//             "website" => "https://www.facebook.com",
//             "establishment_date" => "21-08-2023",
//             "bio" => "ok",
//             "vision" => "ok",
//             "logo" => UploadedFile::fake()->create('logo.jpg', 1024),
//             "image" => UploadedFile::fake()->create('image.jpg', 1024),
//         ]
//     )->assertSessionHasErrors(['email', 'organization_type_id', 'industry_type_id']);

//     // Assert that the response status is 302 (redirect)
//     $response->assertStatus(302);

//     // Expect that no company has been created in the database
//     expect(Company::count())->toBe(1);
// });

it('admin can delete a company', function () {
    // Create a company using the factory
    $company = Company::factory()->create();

    // Send a DELETE request to delete the company
    $response = $this->delete(route('company.destroy', $company));

    // Assert that the response status is 302 (redirect)
    $response->assertStatus(302);

    // Expect that the company has been deleted from the database
    expect(Company::count())->toBe(0);
});
