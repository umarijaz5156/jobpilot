<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->company();

        $company = User::create([
            'name' => $name,
            'username' => Str::slug($name),
            'email' => $this->faker->unique()->safeEmail,
            'role' => 'company',
            'password' => bcrypt('password'), // password
            'email_verified_at' => now(),
            'is_demo_field' => true,
        ]);
        $company->socialInfo()->create([
            'social_media' => 'facebook',
            'url' => 'https://www.facebook.com/zakirsoft',
        ]);

        $company->contactInfo()->create([
            'phone' => '+880123456789',
            'email' => $this->faker->unique()->safeEmail,
        ]);

        return [
            'user_id' => $company->id,
            'industry_type_id' => 1,
            'organization_type_id' => 1,
            'team_size_id' => 1,
            'bio' => $this->faker->text(),
            'logo' => $this->faker->imageUrl,
            'banner' => $this->faker->imageUrl(1024, 300),
            'vision' => $this->faker->text(),
            'establishment_date' => $this->faker->date(),
            'website' => $this->faker->url,
            'profile_completion' => 1,
            'country' => $this->faker->country(),
            'lat' => $this->faker->latitude(-90, 90),
            'long' => $this->faker->longitude(-90, 90),
        ];
    }
}
