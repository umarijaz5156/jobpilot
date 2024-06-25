<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\CmsContent;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class WebsiteSettingController extends Controller
{
    /**
     * Website Setting Page
     *
     * @return void
     */
    public function website_setting()
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            if (! session('tab_part')) {
                session(['tab_part' => 'home']);
            }

            $about_list = Cms::first();
            $website_setting = WebsiteSetting::first();
            $path = base_path('Modules/Language/Resources/json/languages.json');
            $languages = loadLanguage();

            $cms_content = CmsContent::with('language')
                ->whereIn('page_slug', ['terms_condition_page', 'privacy_page', 'refund_page'])
                ->get();

            $privacy_page_list = $cms_content->where('page_slug', 'privacy_page');
            $terms_condition_page_list = $cms_content->where('page_slug', 'terms_condition_page');
            $refund_page_list = $cms_content->where('page_slug', 'refund_page');

            $terms_page = null;
            $privacy_page = null;
            $refund_page = null;

            $exist_cms_content = $cms_content
                ->where('page_slug', 'terms_condition_page')
                ->where('translation_code', session()->get('terms_condition_page'))
                ->first();
            if ($exist_cms_content) {
                $terms_page = $exist_cms_content->text;
            }

            $exist_cms_content_privacy = $cms_content
                ->where('page_slug', 'privacy_page')
                ->where('translation_code', session()->get('privacy_page'))
                ->first();
            if ($exist_cms_content_privacy && session()->get('privacy_page')) {
                $privacy_page = $exist_cms_content_privacy->text;
            }

            $exist_cms_content_privacy = $cms_content
                ->where('page_slug', 'refund_page')
                ->where('translation_code', session()->get('refund_page'))
                ->first();
            if ($exist_cms_content_privacy && session()->get('refund_page')) {
                $refund_page = $exist_cms_content_privacy->text;
            }

            return view('backend.settings.pages.website_setting', compact('about_list', 'languages', 'terms_page', 'privacy_page', 'refund_page', 'privacy_page_list', 'terms_condition_page_list'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Website Setting Page Show
     *
     * @param  Request  $request
     * @return void
     */
    public function show()
    {
        try {
            $websitesetting = WebsiteSetting::all();

            return view('backend.websitesetting.index', compact('websitesetting'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Website Terms, Refund & Privacy Page Update
     *
     * @return void
     */
    public function sessionUpdateTermsPrivacy(Request $request)
    {
        try {
            $cms = Cms::first();

            if ($request->has('session')) {
                session(['tab_part' => $request->session]);
            }

            // terms page
            if ($request->type == 'terms-page') {
                session()->put('terms_condition_page', $request->exist_check);
                $exist_cms_content = CmsContent::where('translation_code', $request->exist_check)
                    ->where('page_slug', 'terms_condition_page')
                    ->first();
                if ($exist_cms_content) {
                    $exist_cms_content->update([
                        'text' => $exist_cms_content->text,
                    ]);
                } else {
                    CmsContent::create([
                        'page_slug' => 'terms_condition_page',
                        'translation_code' => $request->exist_check,
                        'text' => $cms->terms_page,
                    ]);
                }
            }

            // privacy page
            if ($request->type == 'privacy-page') {
                $exist_cms_content2 = CmsContent::where('translation_code', $request->exist_check)
                    ->where('page_slug', 'privacy_page')
                    ->first();

                session()->put('privacy_page', $request->exist_check);

                if ($exist_cms_content2) {
                    $exist_cms_content2->update([
                        'text' => $exist_cms_content2->text,
                    ]);
                } else {
                    CmsContent::create([
                        'page_slug' => 'privacy_page',
                        'translation_code' => $request->exist_check,
                        'text' => $cms->privary_page,
                    ]);
                }
            }

            // refund page
            if ($request->type == 'refund-page') {
                $exist_cms_content3 = CmsContent::where('translation_code', $request->exist_check)
                    ->where('page_slug', 'refund_page')
                    ->first();
                session()->put('refund_page', $request->exist_check);

                if ($exist_cms_content3) {
                    $exist_cms_content3->update([
                        'text' => $exist_cms_content3->text,
                    ]);
                } else {
                    CmsContent::create([
                        'page_slug' => 'refund_page',
                        'translation_code' => $request->exist_check,
                        'text' => $cms->privary_page,
                    ]);
                }
            }

            forgetCache('cms_setting');

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            } else {
                return redirect()->back();
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Cms Content Delete
     */
    public function cmsContentDestroy(Request $request)
    {
        try {
            $content = CmsContent::FindOrFail($request->content_id);

            if ($content->page_slug == 'privacy_page') {
                session()->put('privacy_page', 'en');
            } else {
                session()->put('terms_condition_page', 'en');
            }
            $content->delete();

            flashSuccess(__('website_setting_update_successfully'));

            return redirect()->back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
