<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Plan\Entities\Plan;
use Modules\Plan\Entities\PlanDescription;

class PlanDescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Plans creating
        $plans = [
            [
                'label' => 'Free Plan',
                'description' => 'Company Essentials at No Cost: Boost Your Business',
                'price' => '0',
                'job_limit' => '1',
                'featured_job_limit' => '1',
                'highlight_job_limit' => '1',
                'candidate_cv_view_limit' => '3',
                'recommended' => false,
                'frontend_show' => true,
            ],
            [
                'label' => 'Basic Plan',
                'description' => 'Foundational Solutions: Propel Your Company Forward',
                'price' => '20',
                'job_limit' => '5',
                'featured_job_limit' => '3',
                'highlight_job_limit' => '2',
                'candidate_cv_view_limit' => '10',
                'recommended' => false,
                'frontend_show' => true,
            ],
            [
                'label' => 'Standard Plan',
                'description' => 'Premium Growth Tools: Accelerate Your Business Success',
                'price' => '50',
                'job_limit' => '20',
                'featured_job_limit' => '8',
                'highlight_job_limit' => '4',
                'candidate_cv_view_limit' => '20',
                'recommended' => true,
                'frontend_show' => true,
            ],
        ];

        collect($plans)->each(function ($plan) {
            if (! Plan::where('label', $plan['label'])->exists()) {
                Plan::create([
                    'label' => $plan['label'],
                    'price' => $plan['price'],
                    'job_limit' => $plan['job_limit'],
                    'featured_job_limit' => $plan['featured_job_limit'],
                    'highlight_job_limit' => $plan['highlight_job_limit'],
                    'candidate_cv_view_limit' => (int) $plan['candidate_cv_view_limit'],
                    'recommended' => $plan['recommended'],
                    'frontend_show' => $plan['frontend_show'],
                    'description' => $plan['description'],
                ]);
            }
        });

        // Plan descriptions creating
        $languages = DB::table('languages')->get();
        $plans = DB::table('plans')->get();

        if ($plans && count($plans) && count($plans) != 0) {
            foreach ($plans as $data) {
                if ($languages && count($languages) && count($languages) != 0) {
                    foreach ($languages as $language) {
                        PlanDescription::create([
                            'plan_id' => $data->id,
                            'locale' => $language->code,
                            'description' => $data->description ?? "{$language->code} description",
                        ]);
                    }
                } else {
                    $default_language = env('APP_DEFAULT_LANGUAGE') ?? 'en';

                    PlanDescription::create([
                        'plan_id' => $data->id,
                        'locale' => $default_language,
                        'description' => $data->description ?? "{$default_language} description",
                    ]);
                }
            }
        }
    }
}
