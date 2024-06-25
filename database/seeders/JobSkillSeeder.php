<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobs = Job::all();
        $skills = Skill::all();

        foreach ($jobs as $job) {
            //  job can have 3 randomly assigned skills
            $randomSkills = $skills->random(2);
            foreach ($randomSkills as $skill) {
                DB::table('job_skills')->insert([
                    'job_id' => $job->id,
                    'skill_id' => $skill->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
