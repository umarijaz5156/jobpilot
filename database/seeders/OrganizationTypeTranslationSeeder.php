<?php

namespace Database\Seeders;

use App\Models\OrganizationType;
use App\Models\OrganizationTypeTranslation;
use Illuminate\Database\Seeder;

class OrganizationTypeTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        $types = OrganizationType::all();
        if ($types && count($types) && count($types) != 0) {
            foreach ($types as $data) {
                foreach ($languages as $language) {
                    OrganizationTypeTranslation::create([
                        'organization_type_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
