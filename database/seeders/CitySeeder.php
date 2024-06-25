<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Eloquent\Model; // Import your City model
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $countriesCount = City::count();
        if (! $countriesCount) {
            $this->makeCountry();
        }
    }

    protected function makeCountry()
    {
        $countries_list = json_decode(file_get_contents(base_path('resources/seed-data/cities.json')), true);

        for ($i = 0; $i < count($countries_list); $i++) {
            $country_data[] = [
                'id' => $countries_list[$i]['id'],
                'name' => $countries_list[$i]['name'],
                'long' => $countries_list[$i]['longitude'],
                'lat' => $countries_list[$i]['latitude'],
                'state_id' => $countries_list[$i]['state_id'],
            ];
        }

        $country_chunks = array_chunk($country_data, ceil(count($country_data) / 1000));

        foreach ($country_chunks as $country) {
            City::insert($country);
        }
    }
}
