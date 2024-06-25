<?php

namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class WebsiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
}
