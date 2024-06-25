<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\CandidateLanguage;
use Illuminate\Database\Seeder;

class CandidateLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert language
        $path = base_path('Modules/Language/Resources/json/languages.json');
        $languages = json_decode(file_get_contents($path), true);

        foreach ($languages as $language) {
            CandidateLanguage::create(['name' => $language['name']]);
        }

        // Attach language to candidate language
        $candidates = Candidate::all();
        $languages = CandidateLanguage::all();

        foreach ($candidates as $candidate) {
            $candidate->languages()->attach($languages->random(2));
        }
    }
}
