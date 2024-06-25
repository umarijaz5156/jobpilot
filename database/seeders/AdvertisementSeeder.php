<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use Illuminate\Database\Seeder;

class AdvertisementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $ads = [
            [
                'page_slug' => 'home_page_ad',
                'ad_code' => '',
                'place_example_image' => '',
            ],
            [
                'page_slug' => 'home_page_thin_ad_after_signin_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/1300x230.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p1.webp',
            ],
            [
                'page_slug' => 'home_page_thin_ad_after_counter_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/1300x230.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p1.webp',
            ],
            [
                'page_slug' => 'home_page_fat_ad_after_chooseus_section',
                'ad_code' => '<img width="100%" height="100%" src="dummy/adsense/1300x230.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p2.webp',
            ],
            [
                'page_slug' => 'home_page_fat_ad_after_vacancies_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/1300x230.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p3.webp',
            ],
            [
                'page_slug' => 'home_page_fat_ad_after_workingprocess_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/1300x230.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p3.webp',
            ],
            [
                'page_slug' => 'home_page_fat_ad_after_featuredjob_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/1300x230.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p3.webp',
            ],
            [
                'page_slug' => 'home_page_fat_ad_after_client_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/1300x230.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p3.webp',
            ],
            [
                'page_slug' => 'job_page_ad',
                'ad_code' => '',
                'place_example_image' => '',
            ],
            [
                'page_slug' => 'job_page_fat_ad_after_filter_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/1300x230.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p4.webp',
            ],
            [
                'page_slug' => 'job_page_fat_ad_after_featured_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/1300x230.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p5.webp',
            ],
            [
                'page_slug' => 'job_detailpage_ad',
                'ad_code' => '',
                'place_example_image' => '',
            ],
            [
                'page_slug' => 'job_detailpage_right_ad',
                'ad_code' => '',
                'place_example_image' => '',
            ],
            [
                'page_slug' => 'job_detailpage_fat_ad_header_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/300x500.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p4.webp',
            ],
            [
                'page_slug' => 'job_detailpage_fat_ad_before_salary_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/300x500.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p4.webp',
            ],
            [
                'page_slug' => 'job_detailpage_fat_ad_after_jobbenefits_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/300x500.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p5.webp',
            ],
            [
                'page_slug' => 'job_detailpage_fat_ad_after_share_section',
                'ad_code' => '<img class="max-h-[400px] width="100%" height="100%" src="dummy/adsense/300x500.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p5.webp',
            ],
            [
                'page_slug' => 'bloglist_page_left',
                'ad_code' => '<img width="100%" height="100%" src="dummy/adsense/300x500.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p7.webp',
            ],
            [
                'page_slug' => 'blog_detailpage_inside_blog',
                'ad_code' => '<img width="100%" height="100%" src="dummy/adsense/585x250.webp" alt="">',
                'place_example_image' => 'frontend/images/advertisement_place/p8.webp',
            ],

        ];

        foreach ($ads as $ad) {
            Advertisement::create([
                'page_slug' => $ad['page_slug'],
                'ad_code' => $ad['ad_code'],
                'place_example_image' => $ad['place_example_image'],
            ]);
        }
    }
}
