<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\IndustryType;
use App\Models\OrganizationType;
use App\Models\TeamSize;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Plan\Entities\Plan;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        // Company
        $company = new User();
        $company->name = 'Templatecookie';
        $company->username = 'templatecookie';
        $company->email = 'company@mail.com';
        $company->password = bcrypt('password');
        $company->role = 'company';
        $company->email_verified_at = Carbon::now();
        $company->is_demo_field = true;
        $company->save();

        $created_company = $company->company()->create([
            'industry_type_id' => IndustryType::inRandomOrder()->value('id'),
            'lat' => 23,
            'long' => 90,
            'organization_type_id' => OrganizationType::inRandomOrder()->value('id'),
            'team_size_id' => TeamSize::inRandomOrder()->value('id'),
            'bio' => 'Templatecookie is a team of develoeprs working on building quality templates and scripts! We are a team of 12+ designer and developers with 3+ years of working experiences! We have aexperts in React Js, Vue js, Laravel, PHP, MySQL, Bootstrap, HTML, CSS, SCSS, Tailwind CSS, REST API & React Native!',
            'profile_completion' => 1,
            'logo' => 'https://s3.envato.com/files/385317130/Templatecookie-favicon.png',
            'banner' => 'https://s3.envato.com/files/385317216/Profile%20Banner%20(1).jpg',
            'vision' => 'Templatecookie is a team of develoeprs working on building quality templates and scripts! We are a team of 12+ designer and developers with 3+ years of working experiences! We have aexperts in React Js, Vue js, Laravel, PHP, MySQL, Bootstrap, HTML, CSS, SCSS, Tailwind CSS, REST API & React Native!',
            'country' => 'Bangladesh',
            'lat' => $faker->latitude(-90, 90),
            'long' => $faker->longitude(-90, 90),
        ]);
        $company->socialInfo()->create([
            'social_media' => 'facebook',
            'url' => 'https://www.facebook.com/zakirsoft',
        ]);
        $company->contactInfo()->create([
            'phone' => '+880123456789',
            'email' => 'templatecookie@gmail.com',
        ]);

        $plan = Plan::find(3);

        $created_company->userPlan()->create([
            'plan_id' => $plan->id,
            'job_limit' => $plan->job_limit,
            'featured_job_limit' => $plan->featured_job_limit,
            'highlight_job_limit' => $plan->highlight_job_limit,
            'candidate_cv_view_limit' => $plan->candidate_cv_view_limit,
            'candidate_cv_view_limitation' => $plan->candidate_cv_view_limitation,
        ]);

        // Dummy companies
        $company_list = json_decode(file_get_contents(base_path('resources/backend/dummy-data/companies.json')), true);
        $social_infos = ['facebook', 'twitter', 'youtube', 'linkedin', 'pinterest', 'github'];

        for ($i = 0; $i < count($company_list); $i++) {
            $name = $company_list[$i]['name'];

            $user = User::create([
                'name' => $name,
                'username' => Str::slug($name),
                'email' => $company_list[$i]['email'],
                'role' => 'company',
                'password' => bcrypt('password'), // password
                'email_verified_at' => now(),
                'is_demo_field' => true,
            ]);

            foreach ($social_infos as $social) {
                $user->socialInfo()->create([
                    'social_media' => $social,
                    'url' => 'https://www.facebook.com/'.$user->username,
                ]);
            }

            $user->contactInfo()->create([
                'phone' => '+880123456789',
                'email' => $user->email,
            ]);

            $url = $faker->url;
            $url = str_replace('http://', 'https://', $url);
            $websites = [
                'https://www.facebook.com',
                'https://www.google.com',
                'https://www.twitter.com',
                'https://www.templatecookie.com',
            ];
            $company_data[] = [
                'user_id' => $user->id,
                'industry_type_id' => IndustryType::inRandomOrder()->value('id'),
                'organization_type_id' => OrganizationType::inRandomOrder()->value('id'),
                'team_size_id' => TeamSize::inRandomOrder()->value('id'),
                'bio' => $company_list[$i]['bio'],
                'logo' => $company_list[$i]['logo'],
                'banner' => $company_list[$i]['banner'],
                'vision' => $faker->text(),
                'establishment_date' => $faker->date(),
                'website' => $websites[array_rand($websites)],
                'profile_completion' => 1,
                'country' => $faker->country(),
                'lat' => $faker->latitude(-90, 90),
                'long' => $faker->longitude(-90, 90),
            ];
        }

        $company_chunks = array_chunk($company_data, ceil(count($company_data) / 3));

        foreach ($company_chunks as $product) {
            Company::insert($product);
        }
    }
}
