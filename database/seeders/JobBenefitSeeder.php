<?php

namespace Database\Seeders;

use App\Models\Benefit;
use App\Models\Job;
use Illuminate\Database\Seeder;

class JobBenefitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobs = Job::all();
        $benefits = Benefit::all();

        foreach ($jobs as $job) {
            $job->benefits()->attach($benefits->random(10));
        }
    }
}
