<?php

namespace Database\Seeders;

use App\Models\JobType;
use App\Models\JobTypeTranslation;
use Illuminate\Database\Seeder;

class JobTypeTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        $job_types = JobType::all();
        if ($job_types && count($job_types) && count($job_types) != 0) {
            foreach ($job_types as $data) {
                foreach ($languages as $language) {
                    JobTypeTranslation::create([
                        'skill_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
