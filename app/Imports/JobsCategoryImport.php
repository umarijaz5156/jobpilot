<?php

namespace App\Imports;

use App\Models\JobCategory;
use App\Models\JobCategoryTranslation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Language\Entities\Language;

class JobsCategoryImport implements ShouldQueue, ToCollection, WithChunkReading, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        //  Query to  languagesdata table
        $app_language = Language::latest()->get(['code', 'name']);

        foreach ($rows as $row) {
            $existingCategory = JobCategoryTranslation::where('name', $row['name'])->first();

            if (empty($existingCategory)) {
                // make slug user input title wise
                $userTitle = $row['name'];
                $makeSlug = Str::slug($userTitle);
                $category = JobCategory::create([
                    'image' => 'backend/image/default.png',
                    'icon' => 'fas fa-bars',
                    'slug' => $makeSlug,
                ]);

                foreach ($app_language as $language) {
                    JobCategoryTranslation::create([
                        'job_category_id' => $category->id,
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
