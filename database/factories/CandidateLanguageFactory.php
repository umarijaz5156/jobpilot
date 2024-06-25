<?php

namespace Database\Factories;

use App\Models\CandidateLanguage;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateLanguageFactory extends Factory
{
    protected $model = CandidateLanguage::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word, // Generate a random word as the language name
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
