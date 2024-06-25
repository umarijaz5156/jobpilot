<?php

namespace Database\Seeders;

use App\Models\SalaryType;
use App\Models\SalaryTypeTranslation;
use Illuminate\Database\Seeder;

class SalaryTypeTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        $types = SalaryType::all();
        if ($types && count($types) && count($types) != 0) {
            foreach ($types as $data) {
                foreach ($languages as $language) {
                    SalaryTypeTranslation::create([
                        'salary_type_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
