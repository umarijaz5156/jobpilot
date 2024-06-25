<?php

namespace Database\Seeders;

use App\Models\Benefit;
use App\Models\BenefitTranslation;
use Illuminate\Database\Seeder;

class BenefitTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = loadLanguage();

        $benefits = Benefit::all();

        if ($benefits && count($benefits) && count($benefits) != 0) {
            foreach ($benefits as $data) {
                foreach ($languages as $language) {
                    BenefitTranslation::create([
                        'benefit_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
