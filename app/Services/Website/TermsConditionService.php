<?php

namespace App\Services\Website;

use App\Models\Cms;
use App\Models\CmsContent;

class TermsConditionService
{
    /**
     * Get terms condition
     *
     * @return void
     */
    public function execute(): array
    {
        $termscondition = Cms::select('terms_page')->first();
        $cms_content = CmsContent::query();

        $terms_page = null;

        //check session current language
        $current_language = currentLanguage() ? currentLanguage() : '';
        if ($current_language) {
            $exist_cms_content = $cms_content
                ->where('translation_code', $current_language->code)
                ->where('page_slug', 'terms_condition_page')
                ->first();

            if ($exist_cms_content) {
                $terms_page = $exist_cms_content->text;
            }
        } else {
            $exist_cms_content_en = $cms_content
                ->where('translation_code', 'en')
                ->where('page_slug', 'terms_condition_page')
                ->first();

            if ($exist_cms_content_en) {
                $terms_page = $exist_cms_content_en->text;
            } else {
                $terms_page = $termscondition->terms_page;
            }
        }

        return [
            'termscondition' => $termscondition,
            'terms_page' => $terms_page,
        ];
    }
}
