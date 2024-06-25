<?php

namespace Database\Seeders;

use App\Models\TeamSize;
use App\Models\TeamSizeTranslation;
use Illuminate\Database\Seeder;

class TeamSizeTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        $skills = TeamSize::all();
        if ($skills && count($skills) && count($skills) != 0) {
            foreach ($skills as $data) {
                foreach ($languages as $language) {
                    TeamSizeTranslation::create([
                        'team_size_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
