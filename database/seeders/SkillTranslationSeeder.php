<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillTranslation;
use Illuminate\Database\Seeder;

class SkillTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        $skills = Skill::all();
        if ($skills && count($skills) && count($skills) != 0) {
            foreach ($skills as $data) {
                foreach ($languages as $language) {
                    SkillTranslation::create([
                        'skill_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
