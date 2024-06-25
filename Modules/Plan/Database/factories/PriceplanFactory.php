<?php

namespace Modules\Plan\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Plan\Entities\Plan;

class PriceplanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'label' => $this->faker->sentence(1),
            'price' => rand(10, 99),
            'job_limit' => rand(1, 100),
            'featured_job_limit' => rand(0, 10),
            'highlight_job_limit' => rand(1, 10),
            'candidate_cv_view_limit' => rand(1, 10),
            'candidate_cv_view_limitation' => rand(1, 10),
        ];
    }
}
