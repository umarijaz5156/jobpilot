<?php

namespace Database\Seeders;

use App\Models\IndustryType;
use App\Models\IndustryTypeTranslation;
use Illuminate\Database\Seeder;

class IndustryTypeTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        $types = IndustryType::all();
        if ($types && count($types) && count($types) != 0) {
            foreach ($types as $data) {
                foreach ($languages as $language) {
                    IndustryTypeTranslation::create([
                        'industry_type_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
