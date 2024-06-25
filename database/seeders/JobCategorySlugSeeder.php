<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\JobCategoryTranslation;

class JobCategorySlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobCategory::chunk(100, function ($categories) {
            $jobCategoryTranslations = JobCategoryTranslation::whereIn('job_category_id', $categories->pluck('id'))
                ->where('locale', 'en')
                ->get();

            foreach ($categories as $category) {
                $translation = $jobCategoryTranslations->firstWhere('job_category_id', $category->id);

                if ($translation) {
                    $category->update([
                        'slug' => Str::slug($translation->name),
                    ]);
                }
            }
        });

        info(JobCategory::count());
        // $job_categories = JobCategory::all();

        // if ($job_categories && count($job_categories)) {
        //     foreach ($job_categories as $category) {
        //         if (!$category->slug) {
        //             JobCategory::chunk(100, function ($categories) {
        //                 $jobCategoryTranslations = JobCategoryTranslation::whereIn('job_category_id', $categories->pluck('id'))
        //                     ->where('locale', 'en')
        //                     ->get();

        //                 foreach ($categories as $category) {
        //                     $translation = $jobCategoryTranslations->firstWhere('job_category_id', $category->id);

        //                     if ($translation) {
        //                         $category->update([
        //                             'slug' => Str::slug($translation->name),
        //                         ]);
        //                     }
        //                 }
        //             });
        //         }
        //     }
        // }
    }
}
