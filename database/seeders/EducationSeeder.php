<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('templatecookie.testing_mode')) {
            $educations = [
                'High School', 'Intermediate', 'Bachelor Degree', 'Master Degree', 'Graduated', 'PhD', 'Any',
            ];
        } else {
            $educations = [
                'Bachelor Degree', 'Graduated',
            ];
        }

        $languages = loadLanguage();
        foreach ($educations as $data) {
            $translation = Education::create(['slug' => Str::slug($data)]);
            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }
            $translation->save();
        }
    }
}
