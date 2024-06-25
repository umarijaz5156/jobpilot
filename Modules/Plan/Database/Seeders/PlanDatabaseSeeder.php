<?php

namespace Modules\Plan\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Plan\Entities\Plan;

class PlanDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $plans_count = Plan::count();
        if ($plans_count == 0) {
            $this->call(PlanDatabaseSeeder::class);
        }

        $plans = [
            [
                'label' => 'Free Plan',
                'description' => 'Company Essentials at No Cost: Boost Your Business',
                'price' => '0',
                'job_limit' => '20',
                'featured_job_limit' => '10',
                'highlight_job_limit' => '10',
                'candidate_cv_view_limit' => 1000,
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
                'candidate_cv_view_limit' => 10,
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
                'candidate_cv_view_limit' => 20,
                'recommended' => true,
                'frontend_show' => true,
            ],
        ];

        collect($plans)->each(function ($plan) {
            Plan::create($plan);
        });
    }
}
