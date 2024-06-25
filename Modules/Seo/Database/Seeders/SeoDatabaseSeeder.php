<?php

namespace Modules\Seo\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Seo\Entities\Seo;

class SeoDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Model::unguard();
        Seo::query()->delete();

        $pages = [
            [
                'page_slug' => 'home',
                'title' => 'Welcome To Jobpilot',
            ],
            [
                'page_slug' => 'jobs',
                'title' => 'Jobs',
            ],
            [
                'page_slug' => 'job-details',
                'title' => 'Job Details',
            ],
            [
                'page_slug' => 'candidates',
                'title' => 'Candidates',
            ],
            [
                'page_slug' => 'candidate-details',
                'title' => 'Candidate Details',
            ],
            [
                'page_slug' => 'company',
                'title' => 'Company',
            ],
            [
                'page_slug' => 'company-details',
                'title' => 'Company Details',
            ],
            [
                'page_slug' => 'blog',
                'title' => 'Blog',
            ],
            [
                'page_slug' => 'post-details',
                'title' => 'Post Details',
            ],
            [
                'page_slug' => 'pricing',
                'title' => 'Pricing',
            ],
            [
                'page_slug' => 'login',
                'title' => 'Login',
            ],
            [
                'page_slug' => 'register',
                'title' => 'Register',
            ],
            [
                'page_slug' => 'about',
                'title' => 'About',
            ],
            [
                'page_slug' => 'contact',
                'title' => 'Contact',
            ],
            [
                'page_slug' => 'faq',
                'title' => 'FAQ',
            ],
            [
                'page_slug' => 'terms-condition',
                'title' => 'Terms Condition',
            ],

        ];

        foreach ($pages as $item) {
            $page = Seo::create([
                'page_slug' => $item['page_slug'],
            ]);
            $page->contents()->create([
                'language_code' => 'en',
                'title' => $item['title'],
                'description' => 'Jobpilot is job portal laravel script designed to create, manage and publish jobs posts. Companies can create their profile and publish jobs posts. Candidate can apply job posts.',
                'image' => 'frontend/assets/images/jobpilot.png',
            ]);
        }
    }
}
