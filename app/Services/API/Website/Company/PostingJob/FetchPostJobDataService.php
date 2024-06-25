<?php

namespace App\Services\API\Website\Company\PostingJob;

use App\Models\Tag;
use App\Models\Benefit;
use App\Models\JobRole;
use App\Models\JobType;
use App\Models\Education;
use App\Models\Experience;
use App\Models\SalaryType;
use App\Models\JobCategory;
use F9Web\ApiResponseHelpers;

class FetchPostJobDataService
{
    use ApiResponseHelpers;

    public function execute(){
        $data['jobCategories'] = JobCategory::all()
            ->map(fn($data) => ['id' => $data->id,'name' => $data->name]);
        $data['roles'] = JobRole::all()
            ->map(fn($data) => ['id' => $data->id,'name' => $data->name]);
        $data['benefits'] = Benefit::all()
            ->map(fn($data) => ['id' => $data->id,'name' => $data->name]);
        $data['tags'] = Tag::all()
            ->map(fn($data) => ['id' => $data->id,'name' => $data->name]);
        $data['experiences'] = Experience::all(['id', 'name']);
        $data['educations'] = Education::all(['id', 'name']);
        $data['job_types'] = JobType::all(['id', 'name']);
        $data['salary_types'] = SalaryType::all(['id', 'name']);

        return $this->respondWithSuccess([
            'data' => $data
        ]);
    }
}
