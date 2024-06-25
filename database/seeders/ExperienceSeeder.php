<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('templatecookie.testing_mode')) {
            $experiences = [
                'Fresher', '1 Year', '2 Years', '3+ Years', '5+ Years', '8+ Years', '10+ Years', '15+ Years',
            ];
        } else {
            $experiences = [
                'Fresher', '2 Years',
            ];
        }

        $languages = loadLanguage();
        foreach ($experiences as $data) {
            $translation = Experience::create(['slug' => Str::slug($data)]);

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }

            $translation->save();
        }
    }
}
