<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Seeder;

class JobTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('templatecookie.testing_mode')) {
            $types = [
                'Full Time', 'Part Time', 'Contractual', 'Intern', 'Freelance',
            ];
        } else {
            $types = [
                'Full Time', 'Part Time',
            ];
        }

        $languages = loadLanguage();

        foreach ($types as $data) {
            $translation = new JobType();
            $translation->save();

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }

            $translation->save();
        }
    }
}
