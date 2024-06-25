<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\EducationTranslation;
use Illuminate\Database\Seeder;

class EducationTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        $types = Education::all();
        if ($types) {
            foreach ($types as $data) {
                foreach ($languages as $language) {
                    EducationTranslation::create([
                        'education_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
