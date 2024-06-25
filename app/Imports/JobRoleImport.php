<?php

namespace App\Imports;

use App\Models\JobRole;
use App\Models\JobRoleTranslation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Language\Entities\Language;

class JobRoleImport implements ShouldQueue, ToCollection, WithChunkReading, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $app_language = Language::latest()->get(['code', 'name']);

        foreach ($rows as $row) {
            $existingJobRole = JobRoleTranslation::where('name', $row['name'])->exists();

            if (empty($existingJobRole)) {
                $job_role = JobRole::create();

                foreach ($app_language as $language) {
                    JobRoleTranslation::create([
                        'job_role_id' => $job_role->id,
                        'name' => $row['name'],
                        'locale' => $language->code,
                    ]);
                }
            } else {
                return;
            }
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
