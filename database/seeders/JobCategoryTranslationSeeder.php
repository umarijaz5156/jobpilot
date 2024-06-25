<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use App\Models\JobCategoryTranslation;
use Illuminate\Database\Seeder;

class JobCategoryTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        $categories = JobCategory::all();
        if ($categories && count($categories) && count($categories) != 0) {
            foreach ($categories as $data) {
                foreach ($languages as $language) {
                    JobCategoryTranslation::create([
                        'job_category_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
