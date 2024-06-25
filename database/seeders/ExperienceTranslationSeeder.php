<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\ExperienceTranslation;
use Illuminate\Database\Seeder;

class ExperienceTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        $types = Experience::all();
        if ($types && count($types) && count($types) != 0) {
            foreach ($types as $data) {
                foreach ($languages as $language) {
                    ExperienceTranslation::create([
                        'experience_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
