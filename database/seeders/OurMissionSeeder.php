<?php

namespace Database\Seeders;

use App\Models\OurMission;
use Illuminate\Database\Seeder;

class OurMissionSeeder extends Seeder
{
    public function run()
    {

        $ourmission = new OurMission();
        // $ourmission->image = '';
        $ourmission->save();
    }
}
