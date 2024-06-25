<?php

namespace Database\Seeders;

use App\Models\TeamSize;
use Illuminate\Database\Seeder;

class TeamSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('templatecookie.testing_mode')) {
            $team_sizes = [
                'Only Me', '10 Members', '10-20 Members', '20-50 Members', '50-100 Members', '100-200 Members', '200-500 Members', '500+ Members',
            ];
        } else {
            $team_sizes = [
                'Only Me', '10 - 20 Members',
            ];
        }

        $languages = loadLanguage();

        foreach ($team_sizes as $data) {
            $translation = new TeamSize();
            $translation->save();

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }

            $translation->save();
        }
    }
}
