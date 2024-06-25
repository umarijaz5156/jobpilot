<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Database\Seeder;

class TagTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $languages = loadLanguage();

        $tags = Tag::all();

        if ($tags && count($tags) && count($tags) != 0) {
            foreach ($tags as $data) {
                foreach ($languages as $language) {
                    TagTranslation::create([
                        'tag_id' => $data->id,
                        'locale' => $language->code,
                        'name' => $data->name ?? "{$language->code} name",
                    ]);
                }
            }
        }
    }
}
