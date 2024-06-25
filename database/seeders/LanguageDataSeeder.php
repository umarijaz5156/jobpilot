<?php

namespace Database\Seeders;

use App\Models\LanguageData;
use Illuminate\Database\Seeder;

class LanguageDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        foreach ($languages as $language) {

            $jsonData = [
                'welcome' => 'Welcome',
                'hello' => 'Hello',
            ];

            LanguageData::create([
                'code' => $language->code,
                'data' => json_encode($jsonData),
            ]);
        }

        info(LanguageData::count());

    }
}
