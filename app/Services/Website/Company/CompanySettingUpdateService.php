<?php

namespace App\Services\Website\Company;

use App\Mail\SendEmailUpdateVerification;
use App\Models\Company;
use App\Models\ContactInfo;
use App\Models\IndustryType;
use App\Models\IndustryTypeTranslation;
use App\Models\OrganizationType;
use App\Models\OrganizationTypeTranslation;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CompanySettingUpdateService
{
    /**
     * Update company setting
     */
    public function update($request): mixed
    {
        $user = User::findOrFail(auth()->id());
        $request->session()->put('type', $request->type);

        if ($request->type == 'personal') {
            $this->personalUpdate($request, $user);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'profile') {
            $this->profileUpdate($request);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'social') {

            $this->socialUpdate($request);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'contact') {

            $this->contactUpdate($request);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'account') {

            $this->emailUpdate($request) ? flashSuccess(__('Mail Verification Sent')) : flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'password') {
            $this->passwordUpdate($request, $user);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'account-delete') {
            $this->accountDelete($user);
            flashSuccess(__('profile_updated'));

            return back();
        }
    }

    /**
     * Company personal info update
     *
     * @param  Request  $request
     */
    // public function personalUpdate($request, $user): bool
    // {
    //     $request->validate([
    //         'name' => 'required|unique:users,name,'.auth()->id(),
    //     ]);

    //     $company = Company::where('user_id', auth()->id())->first();

    //     if ($request->image) {
    //         $request->validate([
    //             'image' => 'required|image|mimes:jpeg,png,jpg,gif',
    //         ]);

    //         deleteImage($user->company->logo);
    //         $path = 'images/company';
    //         $image = uploadImage($request->image, $path);

    //         if ($company) {
    //             $company->update(['logo' => $image]);
    //         }
    //     }

    //     if ($request->banner) {
    //         $request->validate([
    //             'banner' => 'required|image|mimes:jpeg,png,jpg,gif',
    //         ]);

    //         deleteImage($user->company->banner);
    //         $path = 'images/company';
    //         $banner = uploadImage($request->banner, $path);

    //         if ($company) {
    //             $company->update(['banner' => $banner]);
    //         }
    //     }

    //     $user->update([
    //         'name' => $request->name,
    //         'username' => Str::slug($request->name),
    //     ]);

    //     if ($company) {
    //         $company->update(['bio' => $request->about_us]);
    //     }

    //     return true;
    // }

    public function personalUpdate($request, $user): bool
    {
        $request->validate([
            'name' => 'required|unique:users,name,'.auth()->id(),
        ]);

        $company = Company::where('user_id', auth()->id())->first();

        if ($request->image) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);

            deleteImage($user->company->logo);
            $path = 'uploads/images/company'; // Relative path within the public directory
            $image = uploadImage($request->image, $path, [68, 68]);

            // Assuming that $user->company->logo holds the relative path to the logo
            if ($company) {
                // deleteImage($user->company->logo); // Delete the old logo
                $company->update(['logo' => $image]);
            }
        }

        if ($request->banner) {
            $request->validate([
                'banner' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);

            deleteImage($user->company->banner);
            $path = 'uploads/images/company';
            $banner = uploadImage($request->banner, $path, [1920, 312]);

            if ($company) {
                $company->update(['banner' => $banner]);
            }
        }

        $user->update([
            'name' => $request->name,
            'username' => Str::slug($request->name),
        ]);

        if ($company) {
            $company->update(['bio' => $request->about_us]);
        }

        return true;
    }

    /**
     * Company profile update
     *
     * @param  Request  $request
     * @return Response
     */
    public function profileUpdate($request)
    {
        $request->validate([
            'organization_type' => 'required',
            'industry_type' => 'required',
            'team_size' => 'required',
            'establishment_date' => 'nullable',
        ]);

        $company = Company::where('user_id', auth()->id())->first();

        // Organization Type
        $organization_request = $request->organization_type;
        $organization_type = OrganizationTypeTranslation::where('organization_type_id', $organization_request)->orWhere('name', $organization_request)->first();

        if (! $organization_type) {
            $new_organization_type = new OrganizationType();

            $languages = loadLanguage();
            foreach ($languages as $language) {
                $new_organization_type->translateOrNew($language->code)->name = $organization_type;
            }
            $new_organization_type->save();

            $organization_type_id = $new_organization_type->id;
        } else {
            $organization_type_id = $organization_type->organization_type_id;
        }

        // Industry Type
        $industry_request = $request->industry_type;
        $industry_type = IndustryTypeTranslation::where('industry_type_id', $industry_request)->orWhere('name', $industry_request)->first();

        if (! $industry_type) {
            $new_industry_type = new IndustryType();

            $languages = loadLanguage();
            foreach ($languages as $language) {
                $new_industry_type->translateOrNew($language->code)->name = $industry_type;
            }
            $new_industry_type->save();

            $industry_type_id = $new_industry_type->id;
        } else {
            $industry_type_id = $industry_type->industry_type_id;
        }

        if ($company) {
            $company->update([
                'organization_type_id' => $organization_type_id,
                'industry_type_id' => $industry_type_id,
                'team_size_id' => $request->team_size,
                'establishment_date' => $request->establishment_date ?? null,
                'website' => $request->website,
                'vision' => $request->vision,
            ]);
        }

        return true;
    }

    /**
     * Company social info update
     *
     * @param  Request  $request
     * @return Response
     */
    public function socialUpdate($request)
    {
        $user = User::find(auth()->id());

        $user->socialInfo()->delete();

        $social_medias = $request->social_media;
        $urls = $request->url;

        if ($social_medias && $urls) {
            foreach ($social_medias as $key => $value) {
                if ($value && $urls[$key]) {
                    $user->socialInfo()->create([
                        'social_media' => $value,
                        'url' => $urls[$key],
                    ]);
                }
            }
        }

        return true;
    }

    /**
     * Company contact info update
     *
     * @param  Request  $request
     * @return Response
     */
    public function contactUpdate($request)
    {
        $contact = ContactInfo::where('user_id', auth()->id())->first();
        if (empty($contact)) {
            ContactInfo::create([
                'user_id' => auth()->id(),
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
        } else {
            $contact->update([
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
        }
        // =========== Location ===========
        updateMap(auth()->user()->company);

        return true;
    }

    /**
     * Company email update
     *
     * @param  Request  $request
     * @return Response
     */
    public function emailUpdate($request): bool
    {
        $user = $request->user();
        $setting = Setting::query()->first();

        $validated = $request->validate([
            'account_email' => 'required|email|unique:users,email,'.$user->id,
            'username' => 'required|unique:users,username,'.$user->id,
        ]);

        $user->update([
            'username' => $validated['username'],
        ]);

        if ($validated['account_email'] === $user->email) {
            return false;
        }

        if (! $setting->email_verification) {
            $user->update([
                'email' => $validated['account_email'],
            ]);

            return false;
        }

        // user changed his email
        // if email verification is on in settings
        // then send verify email and mark email as un verified

        Mail::to($validated['account_email'])->send(new SendEmailUpdateVerification($user, $validated['account_email']));
        session()->put('requested_email', $validated['account_email']);

        return true;

    }

    /**
     * Password Update
     *
     * @param  Request  $request
     * @param  User  $user
     * @return bool
     */
    public function passwordUpdate($request, $user)
    {

        $request->validate([
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
        ]);

        $user->update([
            'password' => bcrypt($request->password),
        ]);
        auth()->logout();

        return true;
    }

    /**
     * Company account delete
     *
     * @param  Request  $request
     * @return Response
     */
    public function accountDelete($user)
    {
        $user->delete();

        return true;
    }
}
