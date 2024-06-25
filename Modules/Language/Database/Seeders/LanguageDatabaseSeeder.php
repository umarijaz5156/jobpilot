<?php

namespace Modules\Language\Database\Seeders;

use App\Models\LanguageData;
use Database\Seeders\LanguageDataSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Language\Entities\Language;

class LanguageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $languages = [
            [
                'name' => 'English',
                'code' => 'en',
                'icon' => 'flag-icon-gb',
                'direction' => 'ltr',
                'created_at' => now(),
            ],
            [
                'name' => 'Bengali',
                'code' => 'bn',
                'icon' => 'flag-icon-bd',
                'direction' => 'ltr',
                'created_at' => now(),
            ],
            [
                'name' => 'Hindi',
                'code' => 'hi',
                'icon' => 'flag-icon-in',
                'direction' => 'ltr',
                'created_at' => now(),
            ],
            [
                'name' => 'French',
                'code' => 'fr',
                'icon' => 'flag-icon-fr',
                'direction' => 'ltr',
                'created_at' => now(),
            ],
            [
                'name' => 'Spanish',
                'code' => 'es',
                'icon' => 'flag-icon-es',
                'direction' => 'ltr',
                'created_at' => now(),
            ],
            [
                'name' => 'Indonesian',
                'code' => 'id',
                'icon' => 'flag-icon-id',
                'direction' => 'ltr',
                'created_at' => now(),
            ],
            [
                'name' => ' German',
                'code' => 'de',
                'icon' => 'flag-icon-de',
                'direction' => 'ltr',
                'created_at' => now(),
            ],
            [
                'name' => 'Arabic',
                'code' => 'ar',
                'icon' => 'flag-icon-sa',
                'direction' => 'rtl',
                'created_at' => now(),
            ],

        ];
        foreach ($languages as $value) {
            if (! Language::where('code', $value['code'])->exists()) {
                Language::create($value);
            }
        }

        $count = LanguageData::count();

        if ($count == 0) {
            // Call the seeder directly using dependency injection
            $seeder = new LanguageDataSeeder();
            $seeder->run();
        }
    }
}
