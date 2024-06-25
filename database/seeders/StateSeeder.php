<?php

namespace Database\Seeders;

use App\Models\State;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $countriesCount = State::count();
        if (! $countriesCount) {
            $this->makeCountry();
        }
    }

    protected function makeCountry()
    {
        $countries_list = json_decode(file_get_contents(base_path('resources/seed-data/states.json')), true);

        for ($i = 0; $i < count($countries_list); $i++) {

            $country_data[] = [
                'id' => $countries_list[$i]['id'],
                'name' => $countries_list[$i]['name'],
                'country_id' => $countries_list[$i]['country_id'],
                'long' => $countries_list[$i]['longitude'],
                'lat' => $countries_list[$i]['latitude'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        $country_chunks = array_chunk($country_data, ceil(count($country_data) / 100));

        foreach ($country_chunks as $country) {
            State::insert($country);
        }
    }
}
