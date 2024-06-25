<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\MessengerUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Messenger>
 */
class MessengerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = $this->faker->sentence($nbWords = 10, $variableNbWords = true);
        $user_1 = 1;
        // $user_2 = 19; //
        // $user_2 = 15; // Johndoe
        // $user_1 = Company::inRandomOrder()->value('id');
        $user_2 = Arr::random([15]);
        // $user_2 = Arr::random([15, 16]);
        // $user_2 = Candidate::inRandomOrder()->value('id');
        // $chat_id = MessengerUser::where('company_id', $user_1)->where('candidate_id', $user_2)->value('id');

        return [
            'to' => Arr::random([$user_1, $user_2]),
            'from' => Arr::random([$user_1, $user_2]),
            'body' => $title,
            'read' => rand(0, 1),
            'messenger_user_id' => $user_2 == 15 ? 1 : 2,
            'created_at' => $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
        ];
    }
}
