<?php

namespace Database\Seeders;

use App\Models\cms;
use App\Models\CmsContent;
use Illuminate\Database\Seeder;

class CmsContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cms = cms::first();

        CmsContent::create([
            'page_slug' => 'terms_condition_page',
            'translation_code' => 'en',
            'text' => $cms->terms_page,
        ]);

        CmsContent::create([
            'page_slug' => 'privacy_page',
            'translation_code' => 'en',
            'text' => $cms->privary_page,
        ]);

        CmsContent::create([
            'page_slug' => 'refund_page',
            'translation_code' => 'en',
            'text' => $cms->refund_page,
        ]);

        CmsContent::create([
            'page_slug' => 'terms_condition_page',
            'translation_code' => 'bn',
            'text' => $cms->terms_page,
        ]);

        CmsContent::create([
            'page_slug' => 'privacy_page',
            'translation_code' => 'bn',
            'text' => $cms->privary_page,
        ]);

        CmsContent::create([
            'page_slug' => 'refund_page',
            'translation_code' => 'bn',
            'text' => $cms->refund_page,
        ]);

        CmsContent::create([
            'page_slug' => 'terms_condition_page',
            'translation_code' => 'ar',
            'text' => $cms->terms_page,
        ]);

        CmsContent::create([
            'page_slug' => 'privacy_page',
            'translation_code' => 'ar',
            'text' => $cms->privary_page,
        ]);

        CmsContent::create([
            'page_slug' => 'refund_page',
            'translation_code' => 'ar',
            'text' => $cms->refund_page,
        ]);

        CmsContent::create([
            'page_slug' => 'terms_condition_page',
            'translation_code' => 'de',
            'text' => $cms->terms_page,
        ]);

        CmsContent::create([
            'page_slug' => 'privacy_page',
            'translation_code' => 'de',
            'text' => $cms->privary_page,
        ]);

        CmsContent::create([
            'page_slug' => 'refund_page',
            'translation_code' => 'de',
            'text' => $cms->refund_page,
        ]);

        CmsContent::create([
            'page_slug' => 'terms_condition_page',
            'translation_code' => 'hi',
            'text' => $cms->terms_page,
        ]);

        CmsContent::create([
            'page_slug' => 'privacy_page',
            'translation_code' => 'hi',
            'text' => $cms->privary_page,
        ]);

        CmsContent::create([
            'page_slug' => 'refund_page',
            'translation_code' => 'hi',
            'text' => $cms->refund_page,
        ]);

        CmsContent::create([
            'page_slug' => 'terms_condition_page',
            'translation_code' => 'es',
            'text' => $cms->terms_page,
        ]);

        CmsContent::create([
            'page_slug' => 'privacy_page',
            'translation_code' => 'es',
            'text' => $cms->privary_page,
        ]);

        CmsContent::create([
            'page_slug' => 'refund_page',
            'translation_code' => 'es',
            'text' => $cms->refund_page,
        ]);

        CmsContent::create([
            'page_slug' => 'terms_condition_page',
            'translation_code' => 'fr',
            'text' => $cms->terms_page,
        ]);

        CmsContent::create([
            'page_slug' => 'privacy_page',
            'translation_code' => 'fr',
            'text' => $cms->privary_page,
        ]);

        CmsContent::create([
            'page_slug' => 'refund_page',
            'translation_code' => 'fr',
            'text' => $cms->refund_page,
        ]);

        CmsContent::create([
            'page_slug' => 'terms_condition_page',
            'translation_code' => 'id',
            'text' => $cms->terms_page,
        ]);

        CmsContent::create([
            'page_slug' => 'privacy_page',
            'translation_code' => 'id',
            'text' => $cms->privary_page,
        ]);

        CmsContent::create([
            'page_slug' => 'refund_page',
            'translation_code' => 'id',
            'text' => $cms->refund_page,
        ]);
    }
}
