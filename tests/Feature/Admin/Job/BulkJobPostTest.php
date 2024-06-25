<?php

use App\Models\Company;
use App\Models\IndustryType;
use App\Models\Job;
use App\Models\OrganizationType;
use App\Models\OrganizationTypeTranslation;
use App\Models\TeamSize;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->admin = createAdmin();
});

it('requires company and import file', function () {
    actingAs($this->admin, 'admin')
        ->post(route('admin.job.bulk.import'), [
            'company' => '',
            'import_file' => '',
        ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['company', 'import_file'])
        ->assertInvalid(['company', 'import_file']);
});

it('import file ', function () {
    IndustryType::factory(10)->create();
    OrganizationType::factory(10)->create();
    OrganizationTypeTranslation::factory(10)->create();
    TeamSize::factory(10)->create();
    $file = UploadedFile::fake()->image('/backend/dummy/job_example.xlsx');

    // Post job import
    actingAs($this->admin, 'admin')
        ->post(route('admin.job.bulk.import'), [
            'company' => Company::factory()->create()->id,
            'import_file' => $file,
        ])
        ->assertStatus(302)
        ->assertSessionHasNoErrors();

    // Expect 4 jobs to be created
    $jobs = Job::count();
    expect(4)->toBe(4, $jobs);
});
