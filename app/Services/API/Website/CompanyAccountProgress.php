<?php

namespace App\Services\API\Website;

use App\Http\Resources\Company\CompanyResource;
use App\Models\Cms;
use App\Models\User;
use App\Models\Company;
use App\Models\TeamSize;
use App\Models\ContactInfo;
use App\Models\IndustryType;
use F9Web\ApiResponseHelpers;
use App\Models\OrganizationType;
use Modules\Location\Entities\Country;
use App\Models\IndustryTypeTranslation;
use Modules\Language\Entities\Language;
use Faker\Factory;
use Illuminate\Support\Facades\Validator;

class CompanyAccountProgress
{
    use ApiResponseHelpers;

    public function fetchAccountProgressData()
    {
        $data['user'] = User::with('company', 'contactInfo', 'socialInfo')->findOrFail(auth('sanctum')->user()->id);
        $data['industry_types'] = IndustryType::all();
        $data['organization_types'] = OrganizationType::all();
        $data['team_sizes'] = TeamSize::all();
        $data['socials'] = $data['user']->socialInfo;
        $data['countries'] = Country::all();

        return $this->respondWithSuccess([
            'data' => $data
        ]);
    }

    public function submitAccountProgressData($request)
    {
        $company = auth('sanctum')->user()->company;

        try {
            switch ($request->step) {
                case "company":
                    $image_validation = $company->logo ? 'sometimes|image|mimes:jpeg,png,jpg|max:2048' :  "required|image|mimes:jpeg,png,jpg|max:2048";
                    $banner_validation = $company->banner ? 'sometimes|image|mimes:jpeg,png,jpg|max:5120' :  "required|image|mimes:jpeg,png,jpg|max:5120";

                    $validator = Validator::make($request->all(), [
                        'image' =>  $image_validation,
                        'banner' =>  $banner_validation,
                        'name' => 'nullable|max:255',
                        'bio' => 'required'
                    ], [
                        'image.required' => 'The logo field is required.'
                    ]);

                    if ($validator->fails()) {
                        return response()->json(
                            ['errors' => $validator->messages()],
                            422
                        );
                    }

                    $company = $this->personalProfileUpdate($request);

                    if ($company) {
                        return new CompanyResource($company);
                    } else {
                        return $this->respondError('Something went wrong.');
                    }

                    break;
                case "founding":

                    $validator = Validator::make($request->all(), [
                        'organization_type_id' =>  'required',
                        'industry_type_id' =>  'required',
                        'establishment_date' => 'nullable',
                        'website' => 'nullable|url',
                        'vision' => 'required'
                    ]);

                    if ($validator->fails()) {
                        return response()->json(
                            ['errors' => $validator->messages()],
                            422
                        );
                    }

                    $update = $this->companyProfileUpdate($request);

                    if ($update) {
                        return $this->respondOk('Company profile updated successfully.');
                    } else {
                        return $this->respondError('Something went wrong.');
                    }
                    break;
                case "social":
                    $update = $this->socialProfileUpdate($request);

                    if ($update) {
                        return $this->respondOk('Company profile updated successfully.');
                    } else {
                        return $this->respondError('Something went wrong.');
                    }
                    break;
                case "contact":

                    // $location = session()->get('location');
                    // if (!$location) {
                    //     $request->validate([
                    //         'location' => 'required',
                    //     ]);
                    // }


                    $validator = Validator::make($request->all(), [
                        'phone' => 'required|min:4|max:16',
                        'email' => 'required|email',
                    ]);

                    if ($validator->fails()) {
                        return response()->json(
                            ['errors' => $validator->messages()],
                            422
                        );
                    }

                    $update = $this->contactProfileUpdate($request);

                    if ($update) {
                        return $this->respondOk('Company profile updated successfully.');
                    } else {
                        return $this->respondError('Something went wrong.');
                    }
                    break;
                case "complete":
                    return view('website.pages.company.account-progress.complete');
                    break;
                default:
                    return back();
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([
                'success' => false,
                'message' =>  $th->getMessage()
            ], 500);
        }
    }

    public function personalProfileUpdate($request)
    {
        $faker = Factory::create();

        $user = User::findOrFail(auth('sanctum')->user()->id);
        $company = Company::where('user_id', $user->id)->firstOrFail();
        $name =  $request->name ?? $faker->name();
        $user->update(['name' => $name]);

        if ($request->hasFile('image')) {
            $image = uploadImage($request->image, 'images/company');
            $company->logo = $image;
        }
        //  else {
        //     $company->logo = createAvatar($name, 'uploads/images/company');
        // }

        if ($request->hasFile('banner')) {
            $banner = uploadImage($request->banner, 'images/company');
            $company->banner = $banner;
        }
        //  else {
        //     $company->logo = createAvatar($name, 'uploads/images/company');
        // }

        $company->bio = $request->bio;
        $company->save();

        return $company->load('user');
    }

    public function companyProfileUpdate($request)
    {
        // Organization Type
        $organization_request = $request->organization_type_id;
        $organization_type = OrganizationType::where('id', $organization_request)->orWhere('name', $organization_request)->first();

        if (!$organization_type) {
            $organization_type = OrganizationType::create(['name' => $organization_request]);
        }

        // Industry Type
        $industry_request = $request->industry_type_id;
        $industry_type = IndustryTypeTranslation::where('industry_type_id', $industry_request)->orWhere('name', $industry_request)->first();

        if (!$industry_type) {
            $new_industry_type = IndustryType::create(['name' => $industry_type]);

            $languages = Language::all();
            foreach ($languages as $language) {
                $new_industry_type->translateOrNew($language->code)->name = $industry_type;
            }
            $new_industry_type->save();


            $industry_type_id = $new_industry_type->id;
        } else {
            $industry_type_id = $industry_type->industry_type_id;
        }

        $company = Company::where('user_id', auth('sanctum')->user()->id);
        $company->update([
            'organization_type_id' => $organization_type->id,
            'industry_type_id' => $industry_type_id,
            'establishment_date' => $request->establishment_date ? date('Y-m-d', strtotime($request->establishment_date)) : null,
            'team_size_id' => $request->team_size_id,
            'website' => $request->website,
            'vision' => $request->vision
        ]);

        return $company;
    }

    public function socialProfileUpdate($request)
    {
        $user = User::find(auth('sanctum')->id());

        $user->socialInfo()->delete();
        $social_medias = $request->social_media;

        if ($social_medias) {
            foreach ($social_medias as $key => $value) {
                if ($value['social_media'] && $value['url']) {
                    $user->socialInfo()->create([
                        'social_media' => $value['social_media'],
                        'url' => $value['url'],
                    ]);
                }
            }
        }

        // $social_medias = $request->social_media;
        // $urls = $request->url;

        // $user = User::find(auth('sanctum')->id());
        // $user->socialInfo()->delete();

        // if ($social_medias && $urls) {

        //     foreach ($social_medias as $key => $value) {
        //         if ($value && $urls[$key]) {
        //             $user->socialInfo()->create([
        //                 'social_media' => $value,
        //                 'url' => $urls[$key],
        //             ]);
        //         }
        //     }
        // }

        return true;
    }

    public function contactProfileUpdate($request)
    {
        $user = User::findOrFail(auth('sanctum')->user()->id);

        $contact = ContactInfo::where('user_id', $user->id)
            ->update($request->except('_method', '_token', 'step', 'location'));

        // =========== Location ===========
        updateMap($user->company());

        if ($contact) {
            Company::where('user_id', $user->id)->update([
                'profile_completion' => 1
            ]);

            return $contact;
        }

        return false;
    }
}
