<?php

namespace Database\Seeders;

use App\Models\SalaryType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SalaryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'Monthly', 'Project Basis', 'Hourly', 'Yearly',
        ];

        $languages = loadLanguage();

        foreach ($types as $data) {
            $translation = SalaryType::create(['slug' => Str::slug($data)]);

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }

            $translation->save();
        }
    }
}
