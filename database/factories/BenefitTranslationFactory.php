<?php

namespace Database\Factories;

use App\Models\Benefit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationTypeTranslation>
 */
class BenefitTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'benefit_id' => Benefit::inRandomOrder()->value('id'),
            'name' => $this->faker->word(),
            'locale' => $this->faker->locale(),
        ];
    }
}
