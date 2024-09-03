<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('settings')->updateOrInsert(
            ['id' => 1],
            [
                'facebook_app_id' => '1535917020611170',
                'facebook_app_secret' => '42d58036e70e127c2b875fc6dd49c748',
                'facebook_page_id' => '103121261078671',
                'facebook_access_token' => 'EAAV06IxrAmIBO5hdLYExeoYF8ups4QKY7uxZCsvsLUtNyzRUDdOonBLcVlbTUKNfBCumQXxH1C6Hp9iZCX7TZArU0p2JOhlL5pX4ZASaUsROgOnScNO1Q5ZC4t13lvSmxdJ1TF3kXwEnAffL7iGwPt6TVhnZC95trK4sRmMwfdHmi8RVviYsYZAWAst4eijdiHiS6hwSWssuHEhdswZD',
                'facebook_app_id_wl' => '528454279553416',
                'facebook_app_secret_wl' => 'e40fbde7506d32816e36a4f71c393c34',
                'facebook_page_id_wl' => '276526412210237',
                'facebook_access_token_wl' => 'EAAHgoFmc4YgBO2u0Ss8tvpdHi2BskFShD3x97I2oqyYP4nW1JvWpWup949luz0wl1vZBlDRAHAaS3jNT4GcInOLBLGHqB6NWeJklP9aED6aeOp8ZCDLfmdn7lN5NrB5yrin01icn0V6IZCCW95iMgqZAhZAPfxaEXSZAXdr4CZCrzaZCjDxW2ZC6YgLsPMZCwezTBwg2mFsKyZBb0L0uHIZD',
                'facebook_app_id_eh' => '907736201173798',
                'facebook_app_secret_eh' => '74aa2156e402b21fc994dd1b6de9f9c8',
                'facebook_page_id_eh' => '449251164931014',
                'facebook_access_token_eh' => 'EAAM5lM3SGyYBO2XEU5sFksAXhCobovx2yO0LTA7IiuxEaj2CkkaHhX3p0hr6ZBeaYlT3qk82wzxXQhGRWqjKJjUoiC0kjdzE1t4xgcZBjL6K5QCxdoCxLvbBOVdZCABwEZBZBN9KDNO4AqC4ZCX7WjiewOZCFaf83uPsJFINNFoIvPOUVODjZAMfw8YZALYWgKqobzJLknuSDuUNgUt8h',
            ]
        );
    }
}
