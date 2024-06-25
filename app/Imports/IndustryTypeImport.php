<?php

namespace App\Imports;

use App\Models\IndustryType;
use App\Models\IndustryTypeTranslation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Language\Entities\Language;

class IndustryTypeImport implements ShouldQueue, ToCollection, WithChunkReading, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $app_languages = Language::latest()->get(['code', 'name']);

        foreach ($rows as $row) {
            $existingIndustry = IndustryTypeTranslation::where('name', $row['name'])->exists();

            if (empty($existingIndustry)) {
                $industry_type = IndustryType::create();

                foreach ($app_languages as $language) {
                    IndustryTypeTranslation::create([
                        'industry_type_id' => $industry_type->id,
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
