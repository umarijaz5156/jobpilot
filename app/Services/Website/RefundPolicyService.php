<?php

namespace App\Services\Website;

use App\Models\Cms;
use App\Models\CmsContent;

class RefundPolicyService
{
    /**
     * Get refund policy
     *
     * @return void
     */
    public function execute(): array
    {
        $page_name = 'refund_page';
        $page_default = Cms::select($page_name)->first();
        $cms_content = CmsContent::query();

        $page = null;

        //check session current language
        $current_language = currentLanguage() ? currentLanguage() : '';

        //if has session current language
        if ($current_language) {
            $exist_cms_content = $cms_content
                ->where('translation_code', $current_language->code)
                ->where('page_slug', $page_name)
                ->first();

            if ($exist_cms_content) {
                $page = $exist_cms_content->text;
            }
        } else {
            //else push default one

            $exist_cms_content_en = $cms_content
                ->where('translation_code', 'en')
                ->where('page_slug', $page_name)
                ->first();

            if ($exist_cms_content_en) {
                $page = $exist_cms_content_en->text;
            } else {
                $page = $page_default->$page_name;
            }
        }

        return [
            'page_default' => $page_default,
            'page' => $page,
        ];
    }
}
