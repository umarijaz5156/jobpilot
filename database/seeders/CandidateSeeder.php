<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Faker\Factory;
use App\Models\Job;
use App\Models\User;
use App\Models\JobRole;
use App\Models\Candidate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profession;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\CandidateJobAlert;
use App\Notifications\Website\Candidate\RelatedJobNotification;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Candidate
        $candidate = new User();
        $candidate->name = 'John Doe';
        $candidate->username = 'johndoe';
        $candidate->email = 'candidate@mail.com';
        $candidate->password = bcrypt('password');
        $candidate->role = 'candidate';
        $candidate->email_verified_at = Carbon::now();
        $candidate->is_demo_field = true;
        $candidate->save();
        $candidate->candidate()->create([
            'role_id' => JobRole::inRandomOrder()->value('id'),
            'profession_id' => Profession::inRandomOrder()->value('id'),
            'experience_id' => Experience::inRandomOrder()->value('id'),
            'education_id' => Education::inRandomOrder()->value('id'),
            'gender' => 'male',
            'website' => 'https://johndoe.com',
            'title' => 'This is candidate Title !',
            'birth_date' => Carbon::now(),
            'marital_status' => 'married',
            'photo' => 'dummy-data/images/candidates/candidate-04.jpg',
            'bio' => 'Sometimes you may wish to stop running validation rules on an attribute after the first validation  failure. To do so, assign the bail rule to the attribute:',
            'profile_complete' => 0,
        ]);
        $candidate->socialInfo()->create([
            'social_media' => 'facebook',
            'url' => 'https://www.facebook.com/zakirsoft',
        ]);
        $candidate->contactInfo()->create([
            'phone' => '+880123456789',
            'secondary_phone' => '+880123456789',
            'email' => 'jhondoe@gmail.com',
            'secondary_email' => 'doejhon@gmail.com',
        ]);

        $job_roles = JobRole::all('id');

        foreach ($job_roles as $key => $job_role) {
            CandidateJobAlert::create([
                'candidate_id' => 1,
                'job_role_id' => $job_role->id
            ]);
        }


        // Dummy companies
        // Candidate::factory(50)->create();
        $faker = Factory::create();
        $candidate_list = json_decode(file_get_contents(base_path('resources/backend/dummy-data/candidates.json')), true);

        for ($i = 0; $i < count($candidate_list); $i++) {
            $name = $candidate_list[$i]['name'];

            $user = User::create([
                'name' => $name,
                'username' => Str::slug($name),
                'image' => $candidate_list[$i]['image'],
                'email' => $candidate_list[$i]['email'],
                'role' => 'candidate',
                'password' => bcrypt('password'), // password
                'email_verified_at' => now(),
                'is_demo_field' => true,
            ]);

            $candidate_data[] = [
                'user_id' => $user->id,
                'photo' => $candidate_list[$i]['image'],
                'role_id' => JobRole::inRandomOrder()->value('id'),
                'profession_id' => Profession::inRandomOrder()->value('id'),
                'experience_id' => Experience::inRandomOrder()->value('id'),
                'education_id' => Education::inRandomOrder()->value('id'),
                'gender' => Arr::random(['male', 'female', 'other']),
                'website' => $faker->url(),
                'title' => $faker->jobTitle(),
                'birth_date' => Carbon::now(),
                'marital_status' => Arr::random(['married', 'single']),
                'bio' => $faker->text(),
                'profile_complete' => 0,
                'country' => $faker->country(),
                'lat' => $faker->latitude(-90, 90),
                'long' => $faker->longitude(-90, 90),
                'status' => Arr::random(['available', 'not_available', 'available_in']),
            ];
        }

        $candidate_chunks = array_chunk($candidate_data, ceil(count($candidate_data) / 3));

        foreach ($candidate_chunks as $candidate) {
            Candidate::insert($candidate);
        }
    }
}
