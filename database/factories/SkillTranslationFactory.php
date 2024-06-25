<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationTypeTranslation>
 */
class SkillTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'skill_id' => Skill::inRandomOrder()->value('id'),
            'name' => $this->faker->word(),
            'locale' => $this->faker->locale(),
        ];
    }
}
