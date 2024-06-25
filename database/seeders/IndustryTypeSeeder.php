<?php

namespace Database\Seeders;

use App\Models\IndustryType;
use Illuminate\Database\Seeder;

class IndustryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('templatecookie.testing_mode')) {
            $industry_types = [
                'Agro Based Industry',
                'Archi/Enggr/Construction',
                'Automobile/Industrial Machine',
                'Bank/Mon-Bank Fin. Institute',
                'Electronics/Consumer Durables',
                'Energy/Power/Fuel',
                'Garments/Textile',
                'Govt./Semi-Govt./Autonomous',
                'Pharmaceuticals',
                'Hospital/Diagnostic Center',
                'Airline/Travel/Tourism',
                'Manufacturing (Light Industry)',
                'Manufacturing (Heavy Industry)',
                'Hotel/Restaurant',
                'Information Technology',
                'Logistics/Transportation',
                'Entertainment/Recreation',
                'Media/Advertising/Event Mgt.',
                'NGO/Development',
                'Real Estate/Development',
                'Wholesale/Retail/Export-Import',
                'Telecommunication',
                'Food & Beverage Industry',
                'Security Service',
                'Fire, Safety & Protection',
            ];
        } else {
            $industry_types = [
                'Agro Based Industry',
                'Archi/Enggr/Construction',
            ];
        }

        // foreach ($industry_types as $data) {
        //     IndustryType::create([
        //         'name' => $data
        //     ]);
        // }

        $languages = loadLanguage();

        foreach ($industry_types as $data) {
            $translation = new IndustryType();
            $translation->save();

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
                // $translation->translateOrNew($language->code)->name = autoTransLation($language->code, $translation);
            }

            $translation->save();
        }
    }
}
