<?php

namespace Database\Seeders;

use App\Models\ApplicationGroup;
use App\Models\Candidate;
use App\Models\CandidateResume;
use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CandidateAppliedJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $candidates = Candidate::all();
        $jobs = Job::all();

        foreach ($candidates as $candidate) {
            foreach ($jobs->random(100) as $job) {
                DB::table('applied_jobs')->insert([
                    'candidate_id' => $candidate->id,
                    'job_id' => $job->id,
                    'cover_letter' => 'lorem ipsum dolor sit amet',
                    'candidate_resume_id' => CandidateResume::inRandomOrder()->first()->id,
                    'application_group_id' => ApplicationGroup::inRandomOrder()->first()->id,
                    // 'application_group_id' => $job->company->applicationGroups()->inRandomOrder()->value('id'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

    }
}
