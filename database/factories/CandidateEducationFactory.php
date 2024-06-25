<?php

namespace Database\Factories;

use App\Models\CandidateEducation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateEducationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CandidateEducation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'level' => $this->faker->randomElement(['Secondary', 'Graduation', 'Masters']),
            'degree' => $this->faker->word,
            'year' => $this->faker->year,
            'notes' => $this->faker->sentence,
        ];
    }
}
