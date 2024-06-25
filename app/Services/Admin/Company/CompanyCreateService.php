<?php

namespace App\Services\Admin\Company;

use App\Models\Setting;
use App\Models\User;
use App\Notifications\CompanyCreateApprovalPendingNotification;
use App\Notifications\CompanyCreatedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CompanyCreateService
{
    /**
     * Create company
     */

    // public function execute($request): void
    // {
    //     // location validation
    //     $this->locationValidation($request);

    //     // create user
    //     $name = $request->name ?? fake()->name();
    //     $username = $request->username ?? Str::slug($name).'_'.time();

    //     $company = User::create([
    //         'name' => $name,
    //         'username' => $username,
    //         'email' => $request->email,
    //         'password' => bcrypt($request->password),
    //         'role' => 'company',
    //     ]);

    //     // insert logo
    //     if ($request->logo) {
    //         $logo_url = uploadImage($request->logo, 'company');
    //     } else {
    //         $logo_url = createAvatar($name, 'uploads/images/company');
    //     }

    //     // insert banner
    //     if ($request->image) {
    //         $banner_url = uploadImage($request->image, 'company');
    //     } else {
    //         $banner_url = createAvatar($name, 'uploads/images/company');
    //     }

    //     // format date
    //     $dateTime = Carbon::parse($request->establishment_date);
    //     $date = $request['establishment_date'] = $dateTime->format('Y-m-d H:i:s') ?? null;

    //     // insert company
    //     $company->company()->update([
    //         'industry_type_id' => $request->industry_type_id,
    //         'organization_type_id' => $request->organization_type_id,
    //         'team_size_id' => $request->team_size_id,
    //         'establishment_date' => $date,
    //         'logo' => $logo_url ?? '',
    //         'banner' => $banner_url ?? '',
    //         'website' => $request->website,
    //         'bio' => $request->bio,
    //         'vision' => $request->vision,
    //     ]);

    //     // company contact info update
    //     $company->contactInfo()->update([
    //         'phone' => $request->contact_phone,
    //         'email' => $request->contact_email,
    //     ]);

    //     // Social media insert
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

    //     // Location insert
    //     updateMap($company->company());

    //     // make Notification
    //     $data[] = $company;
    //     $data[] = $request->password;

    //     // send mail notification
    //     $this->sendMailNotification($company, $request);
    // }

    public function execute($request): void
    {
        // location validation
        $this->locationValidation($request);

        // create user
        $name = $request->name;
        $username = $request->username ? Str::slug($request->username) : Str::slug($name).'_'.time();

        // Check if the username is unique
        while (User::where('username', $username)->exists()) {
            $username = Str::slug($name).'_'.time();
        }

        $company = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'company',
        ]);

        // insert logo
        if ($request->logo) {

            $path = 'uploads/images/company';

            $logo_url = uploadImage($request->logo, $path, [68, 68]);
        } else {
            $setDimension = [100, 100]; //Here needs to be [68, 68] but avatar image not looks good in view that's why increase value 100 from 68
            $path = 'uploads/images/company';
            $logo_url = createAvatar($name, $path, $setDimension);
        }

        // insert banner
        if ($request->image) {

            $path = 'uploads/images/company';

            $banner_url = uploadImage($request->image, $path, [1920, 312]);
        } else {
            $setDimension = [1920, 312];
            $path = 'uploads/images/company';
            $banner_url = createAvatar($name, $path, $setDimension);
        }

        // format date
        $dateTime = Carbon::parse($request->establishment_date);
        $date = $request['establishment_date'] = $dateTime->format('Y-m-d H:i:s') ?? null;

        // insert company
        $company->company()->update([
            'industry_type_id' => $request->industry_type_id,
            'organization_type_id' => $request->organization_type_id,
            'team_size_id' => $request->team_size_id,
            'establishment_date' => $date,
            'logo' => $logo_url ?? '',
            'banner' => $banner_url ?? '',
            'website' => $request->website,
            'bio' => $request->bio,
            'vision' => $request->vision,
        ]);

        // company contact info update
        $company->contactInfo()->update([
            'phone' => $request->contact_phone,
            'email' => $request->contact_email,
        ]);

        // Social media insert
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

        // Location insert
        updateMap($company->company());

        // make Notification
        $data[] = $company;
        $data[] = $request->password;

        // send mail notification
        $this->sendMailNotification($company, $request);
    }

    /**
     * Send mail notification
     *
     * @return void
     */
    protected function sendMailNotification($company, $request)
    {
        // if mail is configured
        if (checkMailConfig()) {
            $employer_auto_activation_enabled = Setting::where('employer_auto_activation', 1)->count();

            // if employer activation enabled, send account created mail else, send will be activated mail.
            if ($employer_auto_activation_enabled) {
                Notification::route('mail', $company->email)->notify(new CompanyCreatedNotification($company, $request->password));
            } else {
                Notification::route('mail', $company->email)->notify(new CompanyCreateApprovalPendingNotification($company, $request->password));
            }
        }
    }

    /**
     * Location validation
     *
     * @return void
     */
    protected function locationValidation($request)
    {
        $location = session()->get('location');
        if (! $location) {
            $request->validate(['location' => 'required']);
        }
    }
}
