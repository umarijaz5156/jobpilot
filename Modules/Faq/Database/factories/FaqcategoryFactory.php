<?php

namespace Modules\Faq\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FaqCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Faq\Entities\FaqCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $categoryIcons = [
            'fas fa-mobile-alt',
            'fas fa-laptop',
            'fas fa-car',
            'fas fa-apple-alt',
            'fas fa-tshirt',
        ];

        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->unique()->slug,
            'icon' => $this->faker->randomElement($categoryIcons),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
