<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class CandidateSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $candidates = Candidate::all();
        $skills = Skill::all();

        foreach ($candidates as $candidate) {
            $candidate->skills()->attach($skills->random(2));
        }
    }
}
