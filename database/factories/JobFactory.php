<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobRole;
use App\Models\JobType;
use App\Models\SalaryType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->unique()->jobTitle();
        $company = Company::with('user:id,name')->inRandomOrder()->first();
        $job_type = JobType::inRandomOrder()->first();
        $min_salary = random_int(10, 100);
        $max_salary = random_int(100, 1000);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'company_id' => $company->id,
            'category_id' => JobCategory::inRandomOrder()->value('id'),
            'role_id' => JobRole::inRandomOrder()->value('id'),
            'experience_id' => Experience::inRandomOrder()->value('id'),
            'education_id' => Education::inRandomOrder()->value('id'),
            'job_type_id' => $job_type->id,
            'salary_type_id' => SalaryType::inRandomOrder()->value('id'),
            'vacancies' => $this->faker->randomElement(['1-2', '2-3', '3-5', '5-10', '10-20']),
            'min_salary' => $min_salary,
            'max_salary' => $max_salary,
            'salary_mode' => Arr::random(['range', 'custom']),
            'custom_salary' => 'Competitive',
            'deadline' => $this->faker->dateTimeBetween('now', '+07 days'),
            'description' => $this->faker->text(),
            'is_remote' => rand(0, 1),
            'status' => $this->faker->randomElement(['pending', 'active', 'expired']),
            'featured' => Arr::random([0, 1, 0, 0, 1]),
            'highlight' => rand(0, 1),
            'apply_on' => Arr::random(['app', 'email', 'custom_url', 'app', 'app']),
            'apply_email' => 'templatecookie@gmail.com',
            'apply_url' => 'https://forms.gle/qhUeH3qte7N3rSJ5A',
            'country' => $this->faker->country(),
            'lat' => $this->faker->latitude(-90, 90),
            'long' => $this->faker->longitude(-90, 90),
            'description' => $this->getDescription(
                $title,
                $company,
                $job_type,
                $min_salary,
                $max_salary
            ),
        ];
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
