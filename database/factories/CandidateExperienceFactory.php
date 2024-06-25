<?php

namespace Database\Factories;

use App\Models\CandidateExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateExperienceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CandidateExperience::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company' => $this->faker->company,
            'department' => $this->faker->word,
            'designation' => $this->faker->jobTitle,
            'start' => $this->faker->dateTimeBetween('-10 years', '-1 year')->format('Y-m-d'),
            'end' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'responsibilities' => $this->faker->paragraph,
        ];
    }
}
