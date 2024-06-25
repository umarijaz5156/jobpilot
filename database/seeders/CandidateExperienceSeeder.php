<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\CandidateExperience;
use Illuminate\Database\Seeder;

class CandidateExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $candidates = Candidate::all();
        $experiences = [
            [
                'company' => 'Google',
                'department' => 'Software',
                'designation' => 'Software Engineer',
                'start' => '2021-01-01',
                'end' => '2022-01-01',
                'responsibilities' => 'Execute full software development life cycle (SDLC), Develop flowcharts, layouts and documentation to identify requirements and solutions, Write well-designed, testable code, Produce specifications and determine operational feasibility',
            ],
            [
                'company' => 'Facebook',
                'department' => 'Software',
                'designation' => 'Product Manager',
                'start' => '2020-01-01',
                'end' => '2021-12-01',
                'responsibilities' => 'Execute full software development life cycle (SDLC), Develop flowcharts, layouts and documentation to identify requirements and solutions, Write well-designed, testable code, Produce specifications and determine operational feasibility',
            ],
            [
                'company' => 'Twitter',
                'department' => 'Software',
                'designation' => 'Senior Software Engineer',
                'start' => '2015-06-01',
                'end' => '2019-12-01',
                'responsibilities' => 'Execute full software development life cycle (SDLC), Develop flowcharts, layouts and documentation to identify requirements and solutions, Write well-designed, testable code, Produce specifications and determine operational feasibility',
            ],
        ];

        foreach ($candidates as $candidate) {
            foreach ($experiences as $experience) {
                $experience['candidate_id'] = $candidate->id;
                CandidateExperience::create($experience);
            }
        }
    }
}
