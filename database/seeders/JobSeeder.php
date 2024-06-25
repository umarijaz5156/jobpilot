<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobRole;
use App\Models\JobType;
use App\Models\Education;
use App\Models\Experience;
use App\Models\SalaryType;
use App\Models\JobCategory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\CandidateJobAlert;
use Modules\Location\Entities\Country;
use App\Notifications\Website\Candidate\RelatedJobNotification;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Job::factory(50)->create();
        $faker = Factory::create();

        $job_list = json_decode(file_get_contents(base_path('resources/backend/dummy-data/jobs.json')), true);

        for ($i = 0; $i < count($job_list); $i++) {

            $company = Company::with('user:id,name')->inRandomOrder()->first();
            $job_type = JobType::inRandomOrder()->first();

            $job_data[] = [
                'title' => $job_list[$i]['title'],
                'slug' => Str::slug($job_list[$i]['title']).'_'.time().'_'.rand(1111111111, 9999999999),
                'company_id' => $company->id,
                'category_id' => JobCategory::inRandomOrder()->value('id'),
                'role_id' => JobRole::inRandomOrder()->value('id'),
                'experience_id' => Experience::inRandomOrder()->value('id'),
                'education_id' => Education::inRandomOrder()->value('id'),
                'job_type_id' => $job_type->id,
                'salary_type_id' => SalaryType::inRandomOrder()->value('id'),
                'vacancies' => $faker->randomElement(['1-2', '2-3', '3-5', '5-10', '10-20']),
                'min_salary' => $job_list[$i]['min_salary'],
                'max_salary' => $job_list[$i]['max_salary'],
                'salary_mode' => Arr::random(['range', 'custom']),
                'custom_salary' => 'Competitive',
                'deadline' => $faker->dateTimeBetween('now', '+07 days'),
                'is_remote' => $job_list[$i]['is_remote'],
                'status' => 'active',
                'featured' => Arr::random([0, 1, 0, 0, 1]),
                'highlight' => rand(0, 1),
                'apply_on' => Arr::random(['app', 'email', 'custom_url', 'app', 'app', 'app', 'app', 'app']),
                'apply_email' => 'templatecookie@gmail.com',
                'apply_url' => 'https://forms.gle/qhUeH3qte7N3rSJ5A',
                'country' => $faker->country(),
                'lat' => $faker->latitude(-90, 90),
                'long' => $faker->longitude(-90, 90),
                'created_at' => now(),
                'updated_at' => now(),
                'description' => $this->getDescription(
                    $job_list[$i]['title'],
                    $company,
                    $job_type,
                    $job_list[$i]['min_salary'],
                    $job_list[$i]['max_salary']
                ),
            ];
        }

        $job_chunks = array_chunk($job_data, ceil(count($job_data) / 3));

        foreach ($job_chunks as $product) {
            Job::insert($product);
        }

        $candidates = CandidateJobAlert::all();

        foreach ($candidates as $candidate) {
            $jobCreated = Job::inRandomOrder()->first();
            $candidate->candidate->user->notify(new RelatedJobNotification($jobCreated));
        }

        Job::factory(50)->create();

        // Every country has jobs => 5
        $this->everyCountryJobs();
    }

    public function everyCountryJobs()
    {
        $job_list = json_decode(file_get_contents(base_path('resources/backend/dummy-data/jobs.json')), true);
        $faker = Factory::create();

        $countries = Country::active()->get();
        foreach ($countries as $key => $country) {

            for ($i = 0; $i < 5; $i++) {
                $company = Company::with('user:id,name')->inRandomOrder()->first();
                $job_type = JobType::inRandomOrder()->first();

                $product1 = [
                    'title' => $job_list[$i]['title'],
                    'slug' => Str::slug($job_list[$i]['title']).'_'.time().'_'.rand(1111111111, 9999999999),
                    'company_id' => $company->id,
                    'category_id' => JobCategory::inRandomOrder()->value('id'),
                    'role_id' => JobRole::inRandomOrder()->value('id'),
                    'experience_id' => Experience::inRandomOrder()->value('id'),
                    'education_id' => Education::inRandomOrder()->value('id'),
                    'job_type_id' => $job_type->id,
                    'salary_type_id' => SalaryType::inRandomOrder()->value('id'),
                    'vacancies' => $faker->randomElement(['1-2', '2-3', '3-5', '5-10', '10-20']),
                    'min_salary' => $job_list[$i]['min_salary'],
                    'max_salary' => $job_list[$i]['max_salary'],
                    'salary_mode' => Arr::random(['custom', 'range']),
                    'custom_salary' => 'Competitive',
                    'deadline' => $faker->dateTimeBetween('now', '+07 days'),
                    'description' => $job_list[$i]['description'],
                    'is_remote' => $job_list[$i]['is_remote'],
                    'status' => 'active',
                    'featured' => rand(0, 1),
                    'highlight' => rand(0, 1),
                    'apply_on' => Arr::random(['app', 'email', 'custom_url', 'app', 'app']),
                    'apply_email' => 'templatecookie@gmail.com',
                    'apply_url' => 'https://forms.gle/qhUeH3qte7N3rSJ5A',
                    'country' => $country->name,
                    'lat' => $country->lat,
                    'long' => $country->long,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'description' => $this->getDescription(
                        $job_list[$i]['title'],
                        $company,
                        $job_type,
                        $job_list[$i]['min_salary'],
                        $job_list[$i]['max_salary']
                    ),
                ];
                Job::insert($product1);

                // wait for 50 milliseconds
                usleep(50000);
            }
        }
    }

    public function getDescription($title, $company, $job_type, $min_salary, $max_salary)
    {
        $salary = getFormattedNumber($min_salary).' - '.getFormattedNumber($max_salary).' '.currentCurrencyCode();

        return "<h4>Job Description</h4>

        <h6>Title: $title</h6>
        <h6>Company: {$company->user->name}</h6>
        <h6>Location: Remote</h6>
        <h6>Position Type: {$job_type->name}</h6>

        <h6>Who Are We?</h6>
        <p>{$company->user->name} is a dynamic and innovative [industry] company with a passion for delivering exceptional products and services to our customers. We're on a mission to provide unparalleled customer experiences, and we're looking for a talented Customer Support Specialist to join our remote team and help us achieve our goals. If you're a dedicated professional who excels in communication, problem-solving, and customer service, we want you to be part of our team.</p>

        <h6>Who We Are Looking For:</h6>
        <p>We need someone who is able to work on multiple projects without hand holding, be a team player who wants to work remotely but also be active in our company culture, values a good work/life balance and believes in getting results.</p>
        <p>We build technology solutions for small and medium sized businesses which means you will get to work on a wide range of technologies including single page web applications, complex systems integration and large-scale eCommerce deployments. You will play a crucial role in ensuring our customers receive top-notch assistance and solutions. You will be the first point of contact for customers seeking help, support, or information, and your dedication to delivering excellent service will be instrumental in our continued success.</p>

        <h6>Requirements:</h6>
        <ul>
            <li>Experience building real-world PHP applications</li>
            <li>Extensive use of Laravel</li>
            <li>Experience with configuring and managing infrastructure in AWS</li>
            <li>Strong front end programming skills (HTML5, CSS, Javascript)</li>
            <li>Experience with VueJs</li>
            <li>Understanding of UI/UX design principles</li>
            <li>Comfortable with command line Linux and/or OSX</li>
            <li>Experience with Git version control</li>
            <li>Develop an in-depth understanding of our products and services to provide accurate and informative assistance to customers</li>
            <li>Good attitude and willingness to learn – We are a drama-free zone!</li>
            <li>Investigate and resolve customer issues, including technical problems, billing inquiries, and product-related challenges, in a timely and satisfactory manner</li>
            <li>Stay updated on industry trends, product updates, and customer service best practices to continuously improve your skills and knowledge.</li>
            <li>Collaborate with the team to provide insights from customer interactions to improve our products, services, and support processes.</li>
            <li>Good Communicator—has good communication skills</li>
            <li>Dependable -- more reliable than spontaneous</li>
            <li>Adaptable/flexible -- enjoys doing work that requires frequent shifts in direction</li>
            <li>Detail-oriented -- would rather focus on the details of work than the bigger picture</li>
            <li>Autonomous/Independent -- enjoys working with little direction</li>
            <li>Innovative -- prefers working in unconventional ways or on tasks that require creativity</li>
        </ul>

        <h6>Qualifications:</h6>
        <ul>
            <li>Excellent written and verbal communication skills in English</li>
            <li>Strong problem-solving skills and the ability to think on your feet</li>
            <li>Empathy and patience when dealing with customer inquiries and concerns</li>
            <li>Proficiency in using CRM software and support tools</li>
            <li>Self-motivated and capable of working independently in a remote setting</li>
            <li>Availability to work flexible hours</li>
            <li>A strong commitment to delivering exceptional customer service</li>
        </ul>

        <h6>Benefits:</h6>
        <ul>
            <li>Paid time off and holiday pay</li>
            <li>Health, dental, and retirement benefits</li>
            <li>Ongoing training and professional development opportunities</li>
            <li>Remote work flexibility with a supportive and collaborative team</li>
            <li>Performance-based bonuses</li>
            <li>Competitive salary</li>
            <li>Dental insurance</li>
            <li>Flexible schedule</li>
            <li>Health insurance</li>
            <li>Paid time off</li>
            <li>Vision insurance</li>
        </ul>

        <h6>Visa Sponsorship Potentially Available:</h6>
        <ul>
            <li>No: Not providing sponsorship for this job</li>
        </ul>

        <h6>Schedule:</h6>
        <ul>
            <li>Monday to Friday</li>
        </ul>

        <h6>Company's website:</h6>
        <ul>
            <li>https://templatecookie.com</li>
        </ul>

        <p><h6>Pay:</h6> $salary</p>";
    }
}
