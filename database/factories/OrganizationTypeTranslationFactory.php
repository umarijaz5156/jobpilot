<?php

namespace Database\Factories;

use App\Models\OrganizationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationTypeTranslation>
 */
class OrganizationTypeTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'organization_type_id' => OrganizationType::inRandomOrder()->value('id'),
            'name' => $this->faker->word(),
            'locale' => $this->faker->locale(),
        ];
    }
}
