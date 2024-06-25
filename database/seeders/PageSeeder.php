<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            'Refund Policy',
            'Privacy Policy',
            'Terms of Service',
            // 'Contact Us',
            // Add more titles as needed
        ];
        foreach ($pages as $page) {
            Page::create([
                'title' => $page,
                'slug' => Str::slug($page),
                'footer_column_position' => rand(1, 4),
                'content' => 'Lorem Ipsum',
                'show_header' => rand(0, 1),
                'show_footer' => rand(0, 1),
            ]);
        }
    }
}
