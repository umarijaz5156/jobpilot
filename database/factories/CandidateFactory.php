<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use App\Models\Profession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CandidateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Candidate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->name;

        $candidate = User::create([
            'name' => $name,
            'username' => Str::slug($name),
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // password
            'role' => 'candidate',
            'email_verified_at' => now(),
            'is_demo_field' => true,
        ]);

        $candidate->socialInfo()->create([
            'social_media' => 'facebook',
            'url' => 'https://www.facebook.com/zakirsoft',
        ]);

        $candidate->contactInfo()->create([
            'phone' => '+880123456789',
            'secondary_phone' => '+880123456789',
            'email' => $this->faker->unique()->safeEmail,
            'secondary_email' => $this->faker->unique()->safeEmail,
        ]);

        return [
            'user_id' => $candidate->id,
            'role_id' => JobRole::inRandomOrder()->value('id'),
            'profession_id' => Profession::inRandomOrder()->value('id'),
            'experience_id' => Experience::inRandomOrder()->value('id'),
            'education_id' => Education::inRandomOrder()->value('id'),
            'gender' => Arr::random(['male', 'female', 'other']),
            'website' => $this->faker->url(),
            'title' => $this->faker->word,
            'birth_date' => Carbon::now(),
            'marital_status' => Arr::random(['married', 'single']),
            'photo' => $this->faker->imageUrl,
            'bio' => $this->faker->text(),
            'profile_complete' => 0,
            'country' => $this->faker->country(),
            'lat' => $this->faker->latitude(-90, 90),
            'long' => $this->faker->longitude(-90, 90),
            'status' => Arr::random(['available', 'not_available', 'available_in']),
            'whatsapp_number' => $this->faker->phoneNumber,
        ];
    }
}
