<?php

namespace Database\Seeders;

use App\Models\Cookies;
use Illuminate\Database\Seeder;

class CookiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cookies = new Cookies();
        $cookies->allow_cookies = true;
        $cookies->cookie_name = 'gdpr_cookie';
        $cookies->cookie_expiration = 30;
        $cookies->force_consent = false;
        $cookies->darkmode = false;
        $cookies->save();
    }
}
