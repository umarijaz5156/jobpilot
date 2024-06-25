<?php

namespace App\Imports;

use App\Models\Skill;
use App\Models\SkillTranslation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Language\Entities\Language;

class SkillsImport implements ShouldQueue, ToCollection, WithChunkReading, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $app_language = Language::latest()->get(['code', 'name']);

        foreach ($rows as $row) {
            $existingSkill = SkillTranslation::where('name', $row['name'])->exists();

            if (empty($existingSkill)) {
                $skill = Skill::create();

                foreach ($app_language as $language) {
                    SkillTranslation::create([
                        'skill_id' => $skill->id,
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
