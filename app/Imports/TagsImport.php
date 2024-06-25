<?php

namespace App\Imports;

use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Language\Entities\Language;

class TagsImport implements ShouldQueue, ToCollection, WithChunkReading, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $app_language = Language::latest()->get(['code', 'name']);

        foreach ($rows as $row) {
            $existingTag = TagTranslation::where('name', $row['name'])->exists();

            if (empty($existingTag)) {
                $tag = Tag::create();

                foreach ($app_language as $language) {
                    TagTranslation::create([
                        'tag_id' => $tag->id,
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
