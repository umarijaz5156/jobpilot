<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;
use Modules\Currency\Entities\Currency;
use Modules\Language\Database\Seeders\LanguageDatabaseSeeder;
use Modules\Language\Entities\Language;
use Modules\Location\Entities\Country;
use Modules\Seo\Database\Seeders\SeoDatabaseSeeder;

class ImportTestingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Setting Seed
        $this->settingSeed();

        // Location Seed
        $this->locationSeed();

        // Location Seed
        $this->currencySeed();

        $this->call([
            CmsSeeder::class,
            SeoDatabaseSeeder::class,
            LanguageDatabaseSeeder::class,
            CookiesSeeder::class,
        ]);
    }

    protected function settingSeed()
    {
        $setting = new Setting();
        $setting->email = 'jobpilot@templatecookie.com';
        $setting->favicon_image = 'frontend/assets/images/logo/fav.png';
        $setting->sidebar_color = '#092433';
        $setting->sidebar_txt_color = '#C1D6F0';
        $setting->nav_color = '#0A243E';
        $setting->nav_txt_color = '#C1D6F0';
        $setting->main_color = '#0A65CC';
        $setting->accent_color = '#487CB8';
        $setting->frontend_primary_color = '#0A65CC';
        $setting->frontend_secondary_color = '#487CB8';
        $setting->default_map = 'leaflet';
        $setting->default_long = 90.4112704917406;
        $setting->default_lat = 23.757853442382867;
        $setting->save();

        $website = new WebsiteSetting();
        $website->phone = '(319) 555-0115';
        $website->address = 'Discover tailored opportunities for job seekers and top talent for employers';
        $website->map_address = 'Zakir Soft Map';
        $website->facebook = 'https://www.facebook.com/zakirsoft';
        $website->instagram = 'https://www.instagram.com/zakirsoft';
        $website->twitter = 'https://www.twitter.com/zakirsoft';
        $website->youtube = 'https://www.youtube.com/zakirsoft';
        $website->title = 'Who we are';
        $website->sub_title = 'We’re highly skilled and professionals team.';
        $website->description = 'Praesent non sem facilisis, hendrerit nisi vitae, volutpat quam. Aliquam metus mauris, semper eu eros vitae, blandit tristique metus. Vestibulum maximus nec justo sed maximus.';
        $website->live_job = '175,324';
        $website->companies = '97,354';
        $website->candidates = '3,847,154';
        $website->save();
    }

    protected function locationSeed()
    {
        $countries = [
            [
                'name' => 'Bangladesh',
                'sortname' => 'BD',
                'image' => 'backend/image/flags/flag-of-bangladesh.jpg',
                'icon' => 'flag-icon-bd',
                'longitude' => '90',
                'latitude' => '24',
            ],
            [
                'name' => 'United States',
                'sortname' => 'US',
                'image' => 'backend/image/flags/flag-of-united-states-of-america.jpg',
                'icon' => 'flag-icon-us',
                'longitude' => '-100',
                'latitude' => '40',
            ],
            [
                'name' => 'United Kingdom',
                'sortname' => 'UK',
                'image' => 'backend/image/flags/flag-of-united-kingdom.jpg',
                'icon' => 'flag-icon-uk',
                'longitude' => '-2',
                'latitude' => '54',
            ],
            [
                'name' => 'Canada',
                'sortname' => 'CA',
                'image' => 'backend/image/flags/flag-of-canada.jpg',
                'icon' => 'flag-icon-ca',
                'longitude' => '60',
                'latitude' => '-95',
            ],
        ];

        $countries_chunk = array_chunk($countries, ceil(count($countries) / 3));

        foreach ($countries_chunk as $country) {
            Country::insert($country);
        }

    }

    protected function currencySeed()
    {
        Currency::create([
            'name' => 'United State Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'symbol_position' => 'left',
        ]);
    }

    protected function languageSeed()
    {
        Language::create([
            'name' => 'English',
            'code' => 'en',
            'icon' => 'flag-icon-gb',
            'direction' => 'ltr',
            'created_at' => now(),
        ]);
    }
}
