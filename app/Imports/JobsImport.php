<?php

namespace App\Imports;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobCategoryTranslation;
use App\Models\JobRole;
use App\Models\JobRoleTranslation;
use App\Models\JobType;
use App\Models\SalaryType;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class JobsImport implements ToModel, WithBatchInserts, WithChunkReading, WithStartRow
{
    public $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function model(array $row)
    {
        $company_id = $this->company_id;
        $title = isset($row[0]) && ! empty($row[0]) ? $row[0] : fake()->unique()->jobTitle();
        $category_id = $this->getCategoryId($row[1]);
        $experience_id = $this->getExperienceId($row[2]);
        $education_id = $this->getEducationId($row[3]);
        $job_type_id = $this->getJobTypeId($row[4]);
        $job_role_id = $this->getJobRoleId($row[5]);
        $vacancies = isset($row[6]) && ! empty($row[6]) ? $row[6] : 1;
        $salary_type_id = $this->getSalaryTypeId($row[7]);
        $min_salary = isset($row[8]) && ! empty($row[8]) ? $row[8] : fake()->numberBetween(100, 1000);
        $max_salary = isset($row[9]) && ! empty($row[9]) ? $row[9] : fake()->numberBetween(1000, 10000);
        $deadline = isset($row[10]) && ! empty($row[10]) ? $this->dateFormat($row[10]) : Carbon::now()->addDays(30)->format('Y-m-d');
        $status = isset($row[11]) && ! empty($row[11]) ? $row[11] : 'active';
        $featured = isset($row[12]) && ! empty($row[12]) ? $row[12] : 0;
        $highlight = isset($row[13]) && ! empty($row[13]) ? $row[13] : 0;
        $remote = isset($row[14]) && ! empty($row[14]) ? $row[14] : 1;
        $long = isset($row[15]) && ! empty($row[15]) ? $row[15] : 90;
        $lat = isset($row[16]) && ! empty($row[16]) ? $row[16] : 90;
        $country = isset($row[17]) && ! empty($row[17]) ? $row[17] : 'United States';
        $full_address = isset($row[18]) && ! empty($row[18]) ? $row[18] : fake()->address;
        $description = isset($row[19]) && ! empty($row[19]) ? $row[19] : fake()->paragraph(5);

        if (! $company_id || ! $title || ! $category_id || ! $experience_id || ! $education_id || ! $job_type_id || ! $job_role_id || ! $vacancies || ! $salary_type_id || ! $min_salary || ! $max_salary || ! $deadline || ! $long || ! $lat || ! $country || ! $full_address || ! $description) {
            return;
        }

        return new Job([
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(6),
            'company_id' => $company_id,
            'category_id' => $category_id,
            'role_id' => $job_role_id,
            'experience_id' => $experience_id,
            'education_id' => $education_id,
            'job_type_id' => $job_type_id,
            'salary_type_id' => $salary_type_id,
            'vacancies' => $vacancies,
            'min_salary' => $min_salary,
            'max_salary' => $max_salary,
            'salary_mode' => 'range',
            'deadline' => Carbon::parse($deadline)->format('Y-m-d'),
            'description' => $description,
            'is_remote' => $remote ? 1 : 0,
            'status' => in_array($status, ['active', 'pending', 'expired']) ? $status : 'active',
            'featured' => $featured ? 1 : 0,
            'highlight' => $highlight ? 1 : 0,
            'apply_on' => 'app',
            'country' => $country,
            'exact_location' => $full_address,
            'lat' => $lat,
            'long' => $long,
        ]);
    }

    public function getCategoryId($data = null)
    {
        if (isset($data) && ! empty($data)) {
            $category = JobCategoryTranslation::where('name', 'LIKE', "%$data%")
                ->orWhere('id', 'LIKE', "%$data%")
                ->first();

            if ($category) {
                return $category->job_category_id;
            }
        }

        return JobCategory::inRandomOrder()->value('id');
    }

    public function getExperienceId($data = null)
    {

        if (isset($data) && ! empty($data)) {
            $slug = Str::slug($data);
            $experience = Experience::Where('slug', 'LIKE', "%$slug%")->orWhere('id', 'LIKE', "%$data%")->first();

            if ($experience) {
                return $experience->id;
            }
        }

        return Experience::inRandomOrder()->value('id');
    }

    public function getEducationId($data = null)
    {

        if (isset($data) && ! empty($data)) {
            $slug = Str::slug($data);
            $education = Education::Where('slug', 'LIKE', "%$slug%")->orWhere('id', 'LIKE', "%$data%")->first();

            if ($education) {
                return $education->id;
            }
        }

        return Education::inRandomOrder()->value('id');
    }

    public function getJobTypeId($data = null)
    {
        if (isset($data) && ! empty($data)) {
            $job_type = JobType::where('name', 'LIKE', "%$data%")->orWhere('slug', 'LIKE', "%$data%")->orWhere('id', 'LIKE', "%$data%")->first();

            if ($job_type) {
                return $job_type->id;
            }
        }

        return JobType::inRandomOrder()->value('id');
    }

    public function getJobRoleId($data = null)
    {
        if (isset($data) && ! empty($data)) {
            $role = JobRoleTranslation::where('name', 'LIKE', "%$data%")->orWhere('id', 'LIKE', "%$data%")->first();

            if ($role) {
                return $role->job_role_id;
            }
        }

        return JobRole::inRandomOrder()->value('id');
    }

    public function getSalaryTypeId($data = null)
    {
        if (isset($data) && ! empty($data)) {
            $slug = Str::slug($data);
            $salary_type = SalaryType::Where('slug', 'LIKE', "%$slug%")->orWhere('id', 'LIKE', "%$data%")->first();

            if ($salary_type) {
                return $salary_type->id;
            }
        }

        return SalaryType::inRandomOrder()->value('id');
    }

    // create date format
    public function dateFormat($date)
    {
        if ($date) {
            $date = intval($date);

            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
        }

        return null;
    }
}
