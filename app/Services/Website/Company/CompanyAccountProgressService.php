<?php

namespace App\Services\Website\Company;

use App\Models\Company;
use App\Models\ContactInfo;
use App\Models\IndustryType;
use App\Models\IndustryTypeTranslation;
use App\Models\OrganizationType;
use App\Models\OrganizationTypeTranslation;
use App\Models\User;
use Illuminate\Support\Str;

class CompanyAccountProgressService
{
    /**
     * Get company account progress
     *
     * @return void
     */
    public function execute($request)
    {
        $company = currentCompany();

        switch ($request->field) {
            case 'personal':
                $image_validation = $company->logo ? 'sometimes|image|mimes:jpeg,png,jpg' : 'required|image|mimes:jpeg,png,jpg';
                $banner_validation = $company->banner ? 'sometimes|image|mimes:jpeg,png,jpg' : 'required|image|mimes:jpeg,png,jpg';

                $request->validate([
                    'image' => $image_validation,
                    'banner' => $banner_validation,
                    'name' => 'nullable|max:255',
                    'bio' => 'required',
                ], [
                    'image.required' => 'The logo field is required.',
                ]);

                $update = $this->personalProfileUpdate($request);
                if ($update) {
                    return redirect('company/account-progress?profile');
                }

                return back();
                break;
            case 'profile':
                $request->validate([
                    'organization_type_id' => 'required|string',
                    'industry_type_id' => 'required|string',
                    'establishment_date' => 'nullable',
                    'website' => 'nullable|url',
                    'vision' => 'required',
                ]);

                $update = $this->companyProfileUpdate($request);
                if ($update) {
                    return redirect('company/account-progress?social')->send();
                }

                return back()->send();
                break;
            case 'social':
                $update = $this->socialProfileUpdate($request);
                if ($update) {
                    return redirect('company/account-progress?contact')->send();
                }

                return back()->send();
                break;
            case 'contact':
                $request->validate([
                    'email' => 'required|email',
                    'phone' => 'required',
                ]);

                $location = session()->get('location');
                if (! $location) {
                    $request->validate([
                        'location' => 'required',
                    ]);
                }

                $request->validate([
                    'phone' => 'required|min:4|max:16',
                    'email' => 'required|email',
                ]);

                $update = $this->contactProfileUpdate($request);
                if ($update) {
                    return redirect('company/account-progress?complete')->send();
                }

                return back()->send();
                break;
            case 'complete':
                return view('frontend.pages.company.account-progress.complete');
                break;
            default:
                return back()->send();
        }
    }

    /**
     * Personal Profile Update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function personalProfileUpdate($request)
    // {
    //     $user = User::findOrFail(auth()->user()->id);
    //     $company = Company::where('user_id', $user->id)->firstOrFail();
    //     $name = $request->name ?? fake()->name();
    //     $newUsername = Str::slug($name);
    //     $user->update(['name' => $name,  'username' => $newUsername]);

    //     if ($request->hasFile('image')) {
    //         $image = uploadImage($request->image, 'images/company');
    //         $company->logo = $image;
    //     } else {
    //         if (! $company->logo) {
    //             $company->logo = createAvatar($name, 'uploads/images/company');
    //         }
    //     }

    //     if ($request->hasFile('banner')) {
    //         $banner = uploadImage($request->banner, 'images/company');
    //         $company->banner = $banner;
    //     } else {
    //         if (! $company->banner) {
    //             $company->banner = createAvatar($name, 'uploads/images/company');
    //         }
    //     }

    //     $company->bio = $request->bio;
    //     $company->save();

    //     return true;
    // }
    public function personalProfileUpdate($request)
    {
        $user = User::findOrFail(auth()->user()->id);
        $company = Company::where('user_id', $user->id)->firstOrFail();
        $name = $request->name;
        $newUsername = Str::slug($name);
        $user->update(['name' => $name,  'username' => $newUsername]);

        if ($request->hasFile('image')) {
            // $image = uploadImage($request->image, 'images/company');

            $path = 'uploads/images/company';

            $image = uploadImage($request->image, $path, [68, 68]);

            $company->logo = $image;
        } else {
            if (! $company->logo) {
                // $company->logo = createAvatar($name, 'uploads/images/company');

                $setDimension = [100, 100]; //Here needs to be [68, 68] but avatar image not looks good in view that's why increase value 100 from 68
                $path = 'uploads/images/company';
                $image = createAvatar($name, $path, $setDimension);
            }
        }

        if ($request->hasFile('banner')) {
            // $banner = uploadImage($request->banner, 'images/company');

            $path = 'uploads/images/company';
            $banner = uploadImage($request->banner, $path, [1920, 312]);

            $company->banner = $banner;
        } else {
            if (! $company->banner) {
                // $company->banner = createAvatar($name, 'uploads/images/company');
                $setDimension = [1920, 312];
                $path = 'uploads/images/company';
                $banner = createAvatar($name, $path, $setDimension);
            }
        }

        $company->bio = $request->bio;
        $company->save();

        return true;
    }

    /**
     * Contact Profile Update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function companyProfileUpdate($request)
    {
        // Organization Type
        $organization_request = $request->organization_type_id;
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
        $industry_request = $request->industry_type_id;
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

        $company = Company::where('user_id', auth()->user()->id);
        $company->update([
            'organization_type_id' => $organization_type_id,
            'industry_type_id' => $industry_type_id,
            'establishment_date' => $request->establishment_date ? date('Y-m-d', strtotime($request->establishment_date)) : null,
            'team_size_id' => $request->team_size_id,
            'website' => $request->website,
            'vision' => $request->vision,
        ]);

        return $company;
    }

    /**
     * Social Profile Update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function socialProfileUpdate($request)
    {
        $social_medias = $request->social_media;
        $urls = $request->url;

        $user = User::find(auth()->id());
        $user->socialInfo()->delete();

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
     * Contact Profile Update
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function contactProfileUpdate($request): mixed
    {
        $user = User::findOrFail(auth()->user()->id);
        $contact = ContactInfo::where('user_id', $user->id)->update($request->only('phone', 'email'));

        // =========== Location ===========
        updateMap($user->company());

        if ($contact) {
            Company::where('user_id', $user->id)->update([
                'profile_completion' => 1,
            ]);

            return $contact;
        }

        return false;
    }
}
