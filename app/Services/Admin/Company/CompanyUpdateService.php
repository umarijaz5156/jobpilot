<?php

namespace App\Services\Admin\Company;

use App\Notifications\UpdateCompanyPassNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CompanyUpdateService
{
    /**
     * Update company
     */

    // public function execute($request, $company): void
    // {
    //     // update user
    //     $company = $company->user;
    //     $data['name'] = $request->name ?? fake()->name();
    //     $data['email'] = $request->email;
    //     $data['username'] = $request->username ?? Str::slug($data['name']).'_'.time();

    //     if ($request->password) {
    //         $data['password'] = bcrypt($request->password);
    //     }

    //     $company->update($data);

    //     // update company
    //     $company->company()->update([
    //         'industry_type_id' => $request->industry_type_id,
    //         'organization_type_id' => $request->organization_type_id,
    //         'team_size_id' => $request->team_size_id,
    //         'establishment_date' => Carbon::parse($request->establishment_date)->format('Y-m-d') ?? null,
    //         'website' => $request->website,
    //         'bio' => $request->bio,
    //         'vision' => $request->vision,
    //     ]);

    //     // update logo
    //     if ($request->logo) {

    //         $logo_url = uploadFileToPublic($request->logo, 'company');
    //         $company->company()->update(['logo' => $logo_url]);
    //     }

    //     // update banner
    //     if ($request->image) {
    //         $banner_url = uploadFileToPublic($request->image, 'company');
    //         $company->company()->update(['banner' => $banner_url]);
    //     }

    //     // update contact info
    //     $company->contactInfo()->update([
    //         'phone' => $request->contact_phone,
    //         'email' => $request->contact_email,
    //     ]);

    //     // Social media update
    //     $company->socialInfo()->delete();

    //     $social_medias = $request->social_media;
    //     $urls = $request->url;

    //     foreach ($social_medias as $key => $value) {
    //         if ($value && $urls[$key]) {
    //             $company->socialInfo()->create([
    //                 'social_media' => $value ?? '',
    //                 'url' => $urls[$key] ?? '',
    //             ]);
    //         }
    //     }

    //     // Location
    //     updateMap($company->company());

    //     // Send mail notification
    //     $this->sendMailNotification($request, $company);
    // }

    public function execute($request, $company): void
    {
        // update user
        $company = $company->user;
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['username'] = $request->username ?? Str::slug($data['name']).'_'.time();

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $company->update($data);

        // update company
        $company->company()->update([
            'industry_type_id' => $request->industry_type_id,
            'organization_type_id' => $request->organization_type_id,
            'team_size_id' => $request->team_size_id,
            'establishment_date' => Carbon::parse($request->establishment_date)->format('Y-m-d') ?? null,
            'website' => $request->website,
            'bio' => $request->bio,
            'vision' => $request->vision,
        ]);

        // update logo
        if ($request->logo) {

            deleteImage($company->company->logo);
            $path = 'uploads/images/company';
            $logo_url = uploadImage($request->logo, $path, [68, 68]);

            if ($company) {
                $company->company()->update(['logo' => $logo_url]);
            }
        }

        // update banner
        if ($request->image) {

            deleteImage($company->company->image);
            $path = 'uploads/images/company';
            $banner_url = uploadImage($request->image, $path, [1920, 312]);

            if ($company) {
                $company->company()->update(['banner' => $banner_url]);
            }
        }

        // update contact info
        $company->contactInfo()->update([
            'phone' => $request->contact_phone,
            'email' => $request->contact_email,
        ]);

        // Social media update
        $company->socialInfo()->delete();

        $social_medias = $request->social_media;
        $urls = $request->url;

        foreach ($social_medias as $key => $value) {
            if ($value && $urls[$key]) {
                $company->socialInfo()->create([
                    'social_media' => $value ?? '',
                    'url' => $urls[$key] ?? '',
                ]);
            }
        }

        // Location
        updateMap($company->company());

        // Send mail notification
        $this->sendMailNotification($request, $company);
    }

    /**
     * Send mail notification
     */
    protected function sendMailNotification($request, $company): void
    {
        if ($request->password) {
            $data[] = $company;
            $data[] = $request->password;
            $data[] = 'Company';

            checkMailConfig() ? Notification::route('mail', $company->email)->notify(new UpdateCompanyPassNotification($data)) : '';
        }
    }
}
