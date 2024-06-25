<?php

use App\Imports\IndustryTypeImport;
use App\Imports\JobRoleImport;
use App\Imports\JobsCategoryImport;
use App\Imports\OrganizationTypeImport;
use App\Imports\ProfessionImport;
use App\Imports\SkillsImport;
use App\Imports\TagsImport;
use App\Models\Education;
use App\Models\Experience;
use App\Models\IndustryType;
use App\Models\IndustryTypeTranslation;
use App\Models\JobCategory;
use App\Models\JobCategoryTranslation;
use App\Models\JobRole;
use App\Models\JobRoleTranslation;
use App\Models\OrganizationType;
use App\Models\OrganizationTypeTranslation;
use App\Models\Profession;
use App\Models\ProfessionTranslation;
use App\Models\Skill;
use App\Models\SkillTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Language\Database\Seeders\LanguageDatabaseSeeder;

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

it('can bulk import job category', function () {
    // Fake storage and Excel imports
    Storage::fake('import_files');
    Excel::fake();

    // Create a fake Excel file
    $file = UploadedFile::fake()->create('import.xlsx', 500);
    // Trigger the import
    $response = $this->post(route('admin.job.category.bulk.import'), [
        'import_file' => $file,
    ])->assertStatus(302);

    // Assertions
    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    // Simulate the collection of rows being imported
    $import = new JobsCategoryImport();
    $rows = [
        ['name' => 'JobsCategory 1'],
        ['name' => 'JobsCategory 2'],
    ];
    $import->collection(collect($rows));

    // Assertions to check if the categories and translations were created correctly
    expect(JobCategory::count())->toBe(2); // Check the count of imported categories
    expect(JobCategoryTranslation::count())->toBe(16); // Check the count of imported translations

});

it('can bulk import industry types', function () {
    // Fake storage and Excel imports
    Storage::fake('import_files');
    Excel::fake();

    // Create a fake Excel file
    $file = UploadedFile::fake()->create('import.xlsx', 500);
    // Trigger the import
    $response = $this->post(route('admin.industry.type.bulk.import'), [
        'import_file' => $file,
    ])->assertStatus(302);

    // Assertions
    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    // Simulate the collection of rows being imported
    $import = new IndustryTypeImport();
    $rows = [
        ['name' => 'Industry 1'],
        ['name' => 'Industry 2'],
    ];
    $import->collection(collect($rows));

    // Assertions to check if the categories and translations were created correctly
    expect(IndustryType::count())->toBe(2); // Check the count of imported categories
    expect(IndustryTypeTranslation::count())->toBe(16); // Check the count of imported translations

});

it('can bulk import job role', function () {
    // Fake storage and Excel imports
    Storage::fake('import_files');
    Excel::fake();

    // Create a fake Excel file
    $file = UploadedFile::fake()->create('import.xlsx', 500);
    // Trigger the import
    $response = $this->post(route('admin.job.role.bulk.import'), [
        'import_file' => $file,
    ])->assertStatus(302);

    // Assertions
    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    // Simulate the collection of rows being imported
    $import = new JobRoleImport();
    $rows = [
        ['name' => 'Job Role 1'],
        ['name' => 'Job Role 2'],
    ];
    $import->collection(collect($rows));

    // Assertions to check if the categories and translations were created correctly
    expect(JobRole::count())->toBe(3); // Check the count of imported categories
    expect(JobRoleTranslation::count())->toBe(17); // Check the count of imported translations

});

it('can bulk import organization type', function () {
    // Fake storage and Excel imports
    Storage::fake('import_files');
    Excel::fake();

    // Create a fake Excel file
    $file = UploadedFile::fake()->create('import.xlsx', 500);
    // Trigger the import
    $response = $this->post(route('admin.organization.type.bulk.import'), [
        'import_file' => $file,
    ])->assertStatus(302);

    // Assertions
    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    // Simulate the collection of rows being imported
    $import = new OrganizationTypeImport();
    $rows = [
        ['name' => 'Organization 1'],
        ['name' => 'Organization 2'],
    ];
    $import->collection(collect($rows));

    // Assertions to check if the categories and translations were created correctly
    expect(OrganizationType::count())->toBe(2); // Check the count of imported categories
    expect(OrganizationTypeTranslation::count())->toBe(16); // Check the count of imported translations

});

it('can bulk import profession type', function () {
    // Fake storage and Excel imports
    Storage::fake('import_files');
    Excel::fake();

    // Create a fake Excel file
    $file = UploadedFile::fake()->create('import.xlsx', 500);
    // Trigger the import
    $response = $this->post(route('admin.profession.bulk.import'), [
        'import_file' => $file,
    ])->assertStatus(302);

    // Assertions
    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    // Simulate the collection of rows being imported
    $import = new ProfessionImport();
    $rows = [
        ['name' => 'Profession 1'],
        ['name' => 'Profession 2'],
    ];
    $import->collection(collect($rows));

    // Assertions to check if the categories and translations were created correctly
    expect(Profession::count())->toBe(30); // Check the count of imported categories
    expect(ProfessionTranslation::count())->toBe(240); // Check the count of imported translations

});

it('can bulk import skill type', function () {
    // Fake storage and Excel imports
    Storage::fake('import_files');
    Excel::fake();

    // Create a fake Excel file
    $file = UploadedFile::fake()->create('import.xlsx', 500);
    // Trigger the import
    $response = $this->post(route('admin.skill.bulk.import'), [
        'import_file' => $file,
    ])->assertStatus(302);

    // Assertions
    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    // Simulate the collection of rows being imported
    $import = new SkillsImport();
    $rows = [
        ['name' => 'Skills 1'],
        ['name' => 'Skills 2'],
    ];
    $import->collection(collect($rows));

    // Assertions to check if the categories and translations were created correctly
    expect(Skill::count())->toBe(2); // Check the count of imported categories
    expect(SkillTranslation::count())->toBe(16); // Check the count of imported translations

});

it('can bulk import tags type', function () {
    // Fake storage and Excel imports
    Storage::fake('import_files');
    Excel::fake();

    // Create a fake Excel file
    $file = UploadedFile::fake()->create('import.xlsx', 500);
    // Trigger the import
    $response = $this->post(route('admin.tags.bulk.import'), [
        'import_file' => $file,
    ])->assertStatus(302);

    // Assertions
    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    // Simulate the collection of rows being imported
    $import = new TagsImport();
    $rows = [
        ['name' => 'Tags 1'],
        ['name' => 'Tags 2'],
    ];
    $import->collection(collect($rows));

    // Assertions to check if the categories and translations were created correctly
    expect(Tag::count())->toBe(2); // Check the count of imported categories
    expect(TagTranslation::count())->toBe(16); // Check the count of imported translations

});
