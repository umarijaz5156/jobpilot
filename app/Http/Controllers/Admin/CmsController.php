<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\CmsContent;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation');
    }

    // Home Page
    public function home(Request $request)
    {
        try {
            session(['tab_part' => 'home']);

            $cms = Cms::first();

            if ($request->file('home_page_banner_image')) {
                $url = uploadImage($request->home_page_banner_image, 'uploads/images/home');
                $cms->home_page_banner_image = $url;
            }

            $cms->save();

            return back()->with('success', 'Home Page upadate successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    // About Page
    // public function aboutupdate(Request $request, Cms $cms)
    // {
    //     session(['tab_part' => 'about']);

    //     $cms = Cms::first();

    //     if ($request->file('about_brand_logo')) {
    //         $url = uploadImage($request->about_brand_logo, 'about');
    //         $cms->about_brand_logo = $url;
    //     }

    //     if ($request->file('about_brand_logo1')) {
    //         $url = uploadImage($request->about_brand_logo1, 'about');
    //         $cms->about_brand_logo1 = $url;
    //     }

    //     if ($request->file('about_brand_logo2')) {
    //         $url = uploadImage($request->about_brand_logo2, 'about');
    //         $cms->about_brand_logo2 = $url;
    //     }

    //     if ($request->file('about_brand_logo3')) {
    //         $url = uploadImage($request->about_brand_logo3, 'about');
    //         $cms->about_brand_logo3 = $url;
    //     }

    //     if ($request->file('about_brand_logo4')) {
    //         $url = uploadImage($request->about_brand_logo4, 'about');
    //         $cms->about_brand_logo4 = $url;
    //     }

    //     if ($request->file('about_brand_logo5')) {
    //         $url = uploadImage($request->about_brand_logo5, 'about');
    //         $cms->about_brand_logo5 = $url;
    //     }

    //     if ($request->file('about_banner_img')) {
    //         $url = uploadImage($request->about_banner_img, 'about');
    //         $cms->about_banner_img = $url;
    //     }

    //     if ($request->file('about_banner_img1')) {
    //         $url = uploadImage($request->about_banner_img1, 'about');
    //         $cms->about_banner_img1 = $url;
    //     }

    //     if ($request->file('about_banner_img2')) {
    //         $url = uploadImage($request->about_banner_img2, 'about');
    //         $cms->about_banner_img2 = $url;
    //     }

    //     if ($request->file('about_banner_img3')) {
    //         $url = uploadImage($request->about_banner_img3, 'about');
    //         $cms->about_banner_img3 = $url;
    //     }

    //     //our mission
    //     if ($request->hasFile('mission_image') && $request->file('mission_image')->isValid()) {
    //         $url = $request->mission_image->move('uploads/ourmission', $request->mission_image->hashName());
    //         $cms->mission_image = $url;
    //     }

    //     $cms->save();

    //     return back()->with('success', 'About Page upadate successfully!');
    // }
    public function aboutupdate(Request $request, Cms $cms)
    {
        try {
            session(['tab_part' => 'about']);
            forgetCache('cms_setting');
            $cms = Cms::first();

            if ($request->file('about_brand_logo')) {
                $path = 'uploads/images/about';
                $url = uploadImage($request->about_brand_logo, $path, [198, 45]);

                $cms->about_brand_logo = $url;
            }

            if ($request->file('about_brand_logo1')) {
                $path = 'uploads/images/about';
                $url = uploadImage($request->about_brand_logo1, $path, [198, 45]);

                $cms->about_brand_logo1 = $url;
            }

            if ($request->file('about_brand_logo2')) {
                $path = 'uploads/images/about';
                $url = uploadImage($request->about_brand_logo2, $path, [198, 45]);

                $cms->about_brand_logo2 = $url;
            }

            if ($request->file('about_brand_logo3')) {
                $path = 'uploads/images/about';
                $url = uploadImage($request->about_brand_logo3, $path, [198, 45]);

                $cms->about_brand_logo3 = $url;
            }

            if ($request->file('about_brand_logo4')) {
                $path = 'uploads/images/about';
                $url = uploadImage($request->about_brand_logo4, $path, [198, 45]);

                $cms->about_brand_logo4 = $url;
            }

            if ($request->file('about_brand_logo5')) {
                $path = 'uploads/images/about';
                $url = uploadImage($request->about_brand_logo5, $path, [198, 45]);

                $cms->about_brand_logo5 = $url;
            }

            if ($request->file('about_banner_img')) {
                $url = uploadImage($request->about_banner_img, 'uploads/images/about');
                $cms->about_banner_img = $url;
            }

            if ($request->file('about_banner_img1')) {
                $url = uploadImage($request->about_banner_img1, 'uploads/images/about');
                $cms->about_banner_img1 = $url;
            }

            if ($request->file('about_banner_img2')) {
                $url = uploadImage($request->about_banner_img2, 'uploads/images/about');
                $cms->about_banner_img2 = $url;
            }

            if ($request->file('about_banner_img3')) {
                $url = uploadImage($request->about_banner_img3, 'uploads/images/about');
                $cms->about_banner_img3 = $url;
            }

            //our mission
            if ($request->hasFile('mission_image') && $request->file('mission_image')->isValid()) {
                $url = $request->mission_image->move('uploads/ourmission', $request->mission_image->hashName());
                $cms->mission_image = $url;
            }

            $cms->save();

            return back()->with('success', 'About Page update successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function paymentupdate(Request $request, Cms $cms)
    {
        try {
            session(['tab_part' => 'pricing_plan']);

            $cms = Cms::first();

            if ($request->file('payment_logo1')) {
                $path = 'uploads/images/payment';
                $url = uploadImage($request->payment_logo1, $path, [198, 45]);

                $cms->payment_logo1 = $url;
            }

            if ($request->file('payment_logo2')) {
                $path = 'uploads/images/payment';
                $url = uploadImage($request->payment_logo2, $path, [198, 45]);

                $cms->payment_logo2 = $url;
            }

            if ($request->file('payment_logo3')) {
                $path = 'uploads/images/payment';
                $url = uploadImage($request->payment_logo3, $path, [198, 45]);

                $cms->payment_logo3 = $url;
            }

            if ($request->file('payment_logo4')) {
                $path = 'uploads/images/payment';
                $url = uploadImage($request->payment_logo4, $path, [198, 45]);

                $cms->payment_logo4 = $url;
            }

            if ($request->file('payment_logo5')) {
                $path = 'uploads/images/payment';
                $url = uploadImage($request->payment_logo5, $path, [198, 45]);

                $cms->payment_logo5 = $url;
            }

            if ($request->file('payment_logo6')) {
                $path = 'uploads/images/payment';
                $url = uploadImage($request->payment_logo6, $path, [198, 45]);

                $cms->payment_logo6 = $url;
            }

            $cms->save();

            return back()->with('success', 'Pricing Page update successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    // Login & Register Page
    public function auth(Request $request)
    {
        try {
            session(['tab_part' => 'auth']);

            $cms = Cms::first();

            if ($request->file('login_page_image')) {
                $url = uploadImage($request->login_page_image, 'uploads/images/login');
                $cms->login_page_image = $url;
            }

            if ($request->file('register_page_image')) {
                $url = uploadImage($request->register_page_image, 'uploads/images/register');
                $cms->register_page_image = $url;
            }

            $cms->save();

            return back()->with('success', 'Authentication Page upadate successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    // Error Pages
    public function updateErrorPages(Request $request)
    {
        try {
            session(['tab_part' => 'error']);

            $cms = Cms::first();

            if ($request->type == '403') {
                if ($request->hasFile('page403_image')) {
                    deleteImage($cms->page403_image);
                    $url = uploadImage($request->page403_image, '403');
                    $cms->page403_image = $url;
                }
            }

            if ($request->type == '404') {
                if ($request->hasFile('page404_image')) {
                    deleteImage($cms->page404_image);
                    $url = uploadImage($request->page404_image, '404');
                    $cms->page404_image = $url;
                }
            }

            if ($request->type == '500') {
                if ($request->hasFile('page500_image')) {
                    deleteImage($cms->page500_image);
                    $url = uploadImage($request->page500_image, '500');
                    $cms->page500_image = $url;
                }
            }

            if ($request->type == '503') {
                if ($request->hasFile('page503_image')) {
                    deleteImage($cms->page503_image);
                    $url = uploadImage($request->page503_image, '503');
                    $cms->page503_image = $url;
                }
            }

            $cms->save();

            return back()->with('success', 'Error Page Upadate Successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    // Privacy and Terms Condition
    public function termsConditionsUpdate(Request $request)
    {
        try {
            $cms = Cms::first();

            if ($request->type == 'terms') {
                session(['tab_part' => 'terms']);
                $request->validate([
                    'terms_page' => 'required',
                ]);

                if ($request->has('translation_code')) {
                    $exist_cms_content = CmsContent::where('translation_code', $request->translation_code)
                        ->where('page_slug', 'terms_condition_page')
                        ->first();

                    if ($exist_cms_content) {
                        session()->put('terms_condition_page', $request->translation_code);
                        $exist_cms_content->update([
                            'text' => $request->terms_page,
                        ]);
                    } else {
                        session()->put('terms_condition_page', $request->translation_code);
                        CmsContent::create([
                            'page_slug' => 'terms_condition_page',
                            'translation_code' => $request->translation_code,
                            'text' => $request->terms_page,
                        ]);
                    }
                } else {
                    $cms->terms_page = $request->terms_page;
                }
            }

            if ($request->type == 'privary') {
                session(['tab_part' => 'privacy']);
                $request->validate([
                    'privary_page' => 'required',
                ]);

                if ($request->has('translation_code')) {
                    $exist_cms_content = CmsContent::where('translation_code', $request->translation_code)
                        ->where('page_slug', 'privacy_page')
                        ->first();

                    if ($exist_cms_content) {
                        session()->put('privacy_page', $request->translation_code);
                        $exist_cms_content->update([
                            'text' => $request->privary_page,
                        ]);
                    } else {
                        session()->put('privacy_page', $request->translation_code);
                        CmsContent::create([
                            'page_slug' => 'privacy_page',
                            'translation_code' => $request->translation_code,
                            'text' => $request->privary_page,
                        ]);
                    }
                } else {
                    $cms->privary_page = $request->privary_page;
                }
            }

            if ($request->type == 'refund') {
                session(['tab_part' => 'refund']);
                $request->validate([
                    'refund_page' => 'required',
                ]);

                if ($request->has('translation_code')) {
                    $exist_cms_content = CmsContent::where('translation_code', $request->translation_code)
                        ->where('page_slug', 'refund_page')
                        ->first();

                    if ($exist_cms_content) {
                        session()->put('refund_page', $request->translation_code);
                        $exist_cms_content->update([
                            'text' => $request->refund_page,
                        ]);
                    } else {
                        session()->put('refund_page', $request->translation_code);
                        CmsContent::create([
                            'page_slug' => 'refund_page',
                            'translation_code' => $request->translation_code,
                            'text' => $request->refund_page,
                        ]);
                    }
                } else {
                    $cms->refund_page = $request->refund_page;
                }
            }

            $cms->save();

            flashSuccess(__('page_content_updated'));

            return redirect()->back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    // Account Complete
    public function accountCompleteUpdate(Request $request)
    {
        return redirect()->back();
    }

    // Comingsoon
    public function comingsoon(Request $request)
    {
        try {
            session(['tab_part' => 'coming_soon']);

            $cms = Cms::first();

            if ($request->file('comingsoon_image')) {
                $url = uploadImage($request->comingsoon_image, 'comingsoon');
                $cms->comingsoon_image = $url;
            }

            $cms->save();

            return back()->with('success', 'comeingsoon_page_upadate_successfully');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    // Maintenance Mode
    public function maintenanceModeUpdate(Request $request)
    {
        try {
            session(['tab_part' => 'maintenance']);

            $request->validate([
                'maintenance_image' => 'required',
            ]);
            $cms = Cms::first();

            if ($request->file('maintenance_image')) {
                deleteImage($cms->maintenance_image);
                $url = uploadImage($request->maintenance_image, 'maintenance');
                $cms->maintenance_image = $url;
            }
            $cms->save();

            flashSuccess(__('page_content_updated'));

            return redirect()->back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    // Others
    // public function othersupdate(Request $request)
    // {
    //     session(['tab_part' => 'others']);

    //     $cms = Cms::first();

    //     if ($request->file('candidate_image')) {
    //         deleteImage($cms->candidate_image);

    //         $url = uploadImage($request->candidate_image, 'candidate');
    //         $cms->candidate_image = $url;
    //     }

    //     if ($request->file('employers_image')) {
    //         deleteImage($cms->employers_image);
    //         $url = uploadImage($request->employers_image, 'employes');
    //         $cms->employers_image = $url;
    //     }

    //     $cms->save();

    //     return back()->with('success', 'Website setting Others upadate successfully!');
    // }
    public function othersupdate(Request $request)
    {
        try {
            session(['tab_part' => 'others']);

            $cms = Cms::first();

            if ($request->file('candidate_image')) {
                deleteImage($cms->candidate_image);

                $path = 'uploads/images/candidate';
                $url = uploadImage($request->candidate_image, $path, [1296, 580]);

                $cms->candidate_image = $url;
            }

            if ($request->file('employers_image')) {
                deleteImage($cms->employers_image);

                $path = 'uploads/images/company';
                $url = uploadImage($request->employers_image, $path, [1296, 580]);

                $cms->employers_image = $url;
            }

            $cms->save();

            return back()->with('success', 'Website setting Others upadate successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function aboutLogoDelete($name)
    {
        try {
            session(['tab_part' => 'about']);

            $cms = Cms::first();
            deleteImage($cms->$name);
            $cms->$name = '';
            $cms->save();

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function paymentLogoDelete($name)
    {
        try {
            session(['tab_part' => 'pricing_plan']);

            $cms = Cms::first();
            deleteImage($cms->$name);
            $cms->$name = '';
            $cms->save();

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
