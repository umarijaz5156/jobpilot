<?php

namespace App\Imports;

use App\Models\Profession;
use App\Models\ProfessionTranslation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Language\Entities\Language;

class ProfessionImport implements ShouldQueue, ToCollection, WithChunkReading, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $app_language = Language::latest()->get(['code', 'name']);

        foreach ($rows as $row) {
            $existingProfession = ProfessionTranslation::where('name', $row['name'])->exists();

            if (empty($existingProfession)) {
                $profession = Profession::create();

                foreach ($app_language as $language) {
                    ProfessionTranslation::create([
                        'id' => ProfessionTranslation::count() + 1,
                        'profession_id' => $profession->id,
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
