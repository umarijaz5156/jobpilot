<?php

namespace App\Imports;

use App\Models\OrganizationType;
use App\Models\OrganizationTypeTranslation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Language\Entities\Language;

class OrganizationTypeImport implements ShouldQueue, ToCollection, WithChunkReading, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $app_language = Language::latest()->get(['code', 'name']);

        foreach ($rows as $row) {
            $existingOrganization = OrganizationTypeTranslation::where('name', $row['name'])->exists();

            if (empty($existingOrganization)) {
                $organization_type = OrganizationType::create();

                foreach ($app_language as $language) {
                    OrganizationTypeTranslation::create([
                        'organization_type_id' => $organization_type->id,
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
