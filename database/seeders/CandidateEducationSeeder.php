<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\CandidateEducation;
use Illuminate\Database\Seeder;

class CandidateEducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $candidates = Candidate::all();
        $educations = [
            [
                'level' => 'Secondary',
                'degree' => 'SSC',
                'year' => '2002',
                'notes' => 'Secondary school is defined as schooling after elementary school, therefore in the U.S. that would be grades 6 through 12. However, once a student reaches grade 9, they are considered to be a high school student.',
            ],
            [
                'level' => 'Graduation',
                'degree' => 'BSC',
                'year' => '2004',
                'notes' => "A graduate student is someone who has earned a bachelor's degree and is pursuing additional education in a specific field.",
            ],
            [
                'level' => 'Masters',
                'degree' => 'MSC',
                'year' => '2010',
                'notes' => "Students who graduate with a master's degree should possess advanced knowledge of a specialized body of theoretical",
            ],
        ];

        foreach ($candidates as $candidate) {
            foreach ($educations as $education) {
                $education['candidate_id'] = $candidate->id;
                CandidateEducation::create($education);

            }
        }
    }
}
