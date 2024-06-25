<?php

namespace Database\Seeders;

use App\Models\SearchCountry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SearchCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $countriesCount = SearchCountry::count();
        if (! $countriesCount) {
            $this->makeCountry();
        }
    }

    protected function makeCountry()
    {
        $countries_list = json_decode(file_get_contents(base_path('resources/seed-data/search_countries.json')), true);

        for ($i = 0; $i < count($countries_list); $i++) {

            $country_data[] = [
                'id' => $countries_list[$i]['id'],
                'name' => $countries_list[$i]['name'],
                'short_name' => $countries_list[$i]['name'],
                'long' => $countries_list[$i]['longitude'],
                'lat' => $countries_list[$i]['latitude'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        $country_chunks = array_chunk($country_data, ceil(count($country_data) / 10));

        foreach ($country_chunks as $country) {
            SearchCountry::insert($country);
        }
    }
}
