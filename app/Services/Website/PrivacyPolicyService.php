<?php

namespace App\Services\Website;

use App\Models\Cms;
use App\Models\CmsContent;

class PrivacyPolicyService
{
    /**
     * Get privacy policy
     *
     * @return void
     */
    public function execute(): array
    {
        $privacy_page_default = Cms::select('privary_page')->first();
        $cms_content = CmsContent::query();

        $privacy_page = null;

        //check session current language
        $current_language = currentLanguage() ? currentLanguage() : '';

        //if has session current language
        if ($current_language) {
            $exist_cms_content = $cms_content
                ->where('translation_code', $current_language->code)
                ->where('page_slug', 'privacy_page')
                ->first();

            if ($exist_cms_content) {
                $privacy_page = $exist_cms_content->text;
            }
        } else {
            //else push default one

            $exist_cms_content_en = $cms_content
                ->where('translation_code', 'en')
                ->where('page_slug', 'privacy_page')
                ->first();

            if ($exist_cms_content_en) {
                $privacy_page = $exist_cms_content_en->text;
            } else {
                $privacy_page = $privacy_page_default->privary_page;
            }
        }

        return [
            'privacy_page_default' => $privacy_page_default,
            'privacy_page' => $privacy_page,
        ];
    }
}
