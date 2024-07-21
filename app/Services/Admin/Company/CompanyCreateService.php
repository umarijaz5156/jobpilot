<?php

namespace App\Services\Admin\Company;

use App\Models\Setting;
use App\Models\User;
use App\Notifications\CompanyCreateApprovalPendingNotification;
use App\Notifications\CompanyCreatedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

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

    // dd($request->all());

    public function execute($request): void
    {
      
        $reqData = $request;
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
            'email_verified_at' =>now(),
            'status' => 1
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
            'video_url' => $request->video_url ?? '',
            'is_profile_verified' => 1
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
        $company->update([
            'status' => 1,
        ]);

        if($request->createWaterLandCompany === 'on'){
            try {
                 $this->sendCompanyDataToAnotherSite($request);
            } catch (\Exception $e) {
                
            }
        }
        if($request->EngineeringJobsHubCompany === 'on'){
            try {
                $this->EngineeringJobsHubCompany($request);
            } catch (\Exception $e) {
                
            }
        }

        if($request->PlanningJobsCompany === 'on'){
            try {
                $this->PlanningJobsCompany($request);
            } catch (\Exception $e) {
                
            }
        }

        
    


        // send mail notification
        $this->sendMailNotification($company, $request);


    }

    private function PlanningJobsCompany($request) {

        $client = new Client();
        $url = env('WEBSITE_URL_COMPANY_PlanningJob');  
    
    
              // Get location data from session
            $locationData = session()->get('location');

            // Prepare location array
            $location = [
                'lat' => $locationData['lat'] ?? '',
                'lng' => $locationData['lng'] ?? '',
                'country' => $locationData['country'] ?? 'Australia',
                'region' => $locationData['region'] ?? '',
                'district' => $locationData['district'] ?? '',
                'place' => $locationData['place'] ?? '',
                'exact_location' => $locationData['exact_location'] ?? '',
            ];
        
            $multipart = [
                [
                    'name'     => 'name',
                    'contents' => $request->name,
                ],
                [
                    'name'     => 'username',
                    'contents' => $request->username,
                ],
                [
                    'name'     => 'email',
                    'contents' => $request->email,
                ],
                [
                    'name'     => 'password',
                    'contents' => $request->password,
                ],
                [
                    'name'     => 'contact_phone',
                    'contents' => $request->contact_phone,
                ],
                [
                    'name'     => 'contact_email',
                    'contents' => $request->contact_email,
                ],
                [
                    'name'     => 'organization_type_id',
                    'contents' => $request->organization_type_id,
                ],
                [
                    'name'     => 'industry_type_id',
                    'contents' => $request->industry_type_id,
                ],
                [
                    'name'     => 'team_size_id',
                    'contents' => $request->team_size_id,
                ],
                [
                    'name'     => 'website',
                    'contents' => $request->website,
                ],
                [
                    'name'     => 'video_url',
                    'contents' => $request->video_url,
                ],
                [
                    'name'     => 'bio',
                    'contents' => (string) $request->bio,
                ],
                [
                    'name'     => 'vision',
                    'contents' => (string) $request->vision,
                ],
                [
                    'name'     => 'location',
                    'contents' => json_encode($location),
                ],
              
            ];
    
            // Add social media and URLs
            if ($request->social_media) {
                foreach ($request->social_media as $index => $social_media) {
                    $multipart[] = [
                        'name'     => "social_media[{$index}]",
                        'contents' => $social_media,
                    ];
                    $multipart[] = [
                        'name'     => "url[{$index}]",
                        'contents' => $request->url[$index] ?? '',
                    ];
                }
            }

          
           
    
            // Add logo and image files if present
            if ($request->hasFile('logo')) {
                $multipart[] = [
                    'name'     => 'logo',
                    'contents' => fopen($request->file('logo')->getPathname(), 'r'),
                    'filename' => $request->file('logo')->getClientOriginalName(),
                ];
            }
    
            if ($request->hasFile('image')) {
                $multipart[] = [
                    'name'     => 'image',
                    'contents' => fopen($request->file('image')->getPathname(), 'r'),
                    'filename' => $request->file('image')->getClientOriginalName(),
                ];
            }
    
            // Send the request
            $response = $client->post($url, [
                'multipart' => $multipart,
            ]);
    
    
            // Handle the response
            if ($response->getStatusCode() != 200) {
                throw new \Exception('Error sending data to another site');
            }
    
            return json_decode($response->getBody(), true);
       
    }

    private function EngineeringJobsHubCompany($request) {

        $client = new Client();
        $url = env('WEBSITE_URL_COMPANY_EngineeringJobsHub');  
    
    
              // Get location data from session
            $locationData = session()->get('location');

            // Prepare location array
            $location = [
                'lat' => $locationData['lat'] ?? '',
                'lng' => $locationData['lng'] ?? '',
                'country' => $locationData['country'] ?? 'Australia',
                'region' => $locationData['region'] ?? '',
                'district' => $locationData['district'] ?? '',
                'place' => $locationData['place'] ?? '',
                'exact_location' => $locationData['exact_location'] ?? '',
            ];
        
            $multipart = [
                [
                    'name'     => 'name',
                    'contents' => $request->name,
                ],
                [
                    'name'     => 'username',
                    'contents' => $request->username,
                ],
                [
                    'name'     => 'email',
                    'contents' => $request->email,
                ],
                [
                    'name'     => 'password',
                    'contents' => $request->password,
                ],
                [
                    'name'     => 'contact_phone',
                    'contents' => $request->contact_phone,
                ],
                [
                    'name'     => 'contact_email',
                    'contents' => $request->contact_email,
                ],
                [
                    'name'     => 'organization_type_id',
                    'contents' => $request->organization_type_id,
                ],
                [
                    'name'     => 'industry_type_id',
                    'contents' => $request->industry_type_id,
                ],
                [
                    'name'     => 'team_size_id',
                    'contents' => $request->team_size_id,
                ],
                [
                    'name'     => 'website',
                    'contents' => $request->website,
                ],
                [
                    'name'     => 'video_url',
                    'contents' => $request->video_url,
                ],
                [
                    'name'     => 'bio',
                    'contents' => (string) $request->bio,
                ],
                [
                    'name'     => 'vision',
                    'contents' => (string) $request->vision,
                ],
                [
                    'name'     => 'location',
                    'contents' => json_encode($location),
                ],
              
            ];
    
            // Add social media and URLs
            if ($request->social_media) {
                foreach ($request->social_media as $index => $social_media) {
                    $multipart[] = [
                        'name'     => "social_media[{$index}]",
                        'contents' => $social_media,
                    ];
                    $multipart[] = [
                        'name'     => "url[{$index}]",
                        'contents' => $request->url[$index] ?? '',
                    ];
                }
            }

          
           
    
            // Add logo and image files if present
            if ($request->hasFile('logo')) {
                $multipart[] = [
                    'name'     => 'logo',
                    'contents' => fopen($request->file('logo')->getPathname(), 'r'),
                    'filename' => $request->file('logo')->getClientOriginalName(),
                ];
            }
    
            if ($request->hasFile('image')) {
                $multipart[] = [
                    'name'     => 'image',
                    'contents' => fopen($request->file('image')->getPathname(), 'r'),
                    'filename' => $request->file('image')->getClientOriginalName(),
                ];
            }
    
            // Send the request
            $response = $client->post($url, [
                'multipart' => $multipart,
            ]);
    
    
            // Handle the response
            if ($response->getStatusCode() != 200) {
                throw new \Exception('Error sending data to another site');
            }
    
            return json_decode($response->getBody(), true);
       
    }

    private function sendCompanyDataToAnotherSite($request)
    {
        $client = new Client();
        $url = env('WEBSITE_URL_COMPANY');  
    
    
              // Get location data from session
            $locationData = session()->get('location');

            // Prepare location array
            $location = [
                'lat' => $locationData['lat'] ?? '',
                'lng' => $locationData['lng'] ?? '',
                'country' => $locationData['country'] ?? 'Australia',
                'region' => $locationData['region'] ?? '',
                'district' => $locationData['district'] ?? '',
                'place' => $locationData['place'] ?? '',
                'exact_location' => $locationData['exact_location'] ?? '',
            ];
        
            $multipart = [
                [
                    'name'     => 'name',
                    'contents' => $request->name,
                ],
                [
                    'name'     => 'username',
                    'contents' => $request->username,
                ],
                [
                    'name'     => 'email',
                    'contents' => $request->email,
                ],
                [
                    'name'     => 'password',
                    'contents' => $request->password,
                ],
                [
                    'name'     => 'contact_phone',
                    'contents' => $request->contact_phone,
                ],
                [
                    'name'     => 'contact_email',
                    'contents' => $request->contact_email,
                ],
                [
                    'name'     => 'organization_type_id',
                    'contents' => $request->organization_type_id,
                ],
                [
                    'name'     => 'industry_type_id',
                    'contents' => $request->industry_type_id,
                ],
                [
                    'name'     => 'team_size_id',
                    'contents' => $request->team_size_id,
                ],
                [
                    'name'     => 'website',
                    'contents' => $request->website,
                ],
                [
                    'name'     => 'video_url',
                    'contents' => $request->video_url,
                ],
                [
                    'name'     => 'bio',
                    'contents' => (string) $request->bio,
                ],
                [
                    'name'     => 'vision',
                    'contents' => (string) $request->vision,
                ],
                [
                    'name'     => 'location',
                    'contents' => json_encode($location),
                ],
              
            ];
    
            // Add social media and URLs
            if ($request->social_media) {
                foreach ($request->social_media as $index => $social_media) {
                    $multipart[] = [
                        'name'     => "social_media[{$index}]",
                        'contents' => $social_media,
                    ];
                    $multipart[] = [
                        'name'     => "url[{$index}]",
                        'contents' => $request->url[$index] ?? '',
                    ];
                }
            }

          
           
    
            // Add logo and image files if present
            if ($request->hasFile('logo')) {
                $multipart[] = [
                    'name'     => 'logo',
                    'contents' => fopen($request->file('logo')->getPathname(), 'r'),
                    'filename' => $request->file('logo')->getClientOriginalName(),
                ];
            }
    
            if ($request->hasFile('image')) {
                $multipart[] = [
                    'name'     => 'image',
                    'contents' => fopen($request->file('image')->getPathname(), 'r'),
                    'filename' => $request->file('image')->getClientOriginalName(),
                ];
            }
    
            // Send the request
            $response = $client->post($url, [
                'multipart' => $multipart,
            ]);
    
    
            // Handle the response
            if ($response->getStatusCode() != 200) {
                throw new \Exception('Error sending data to another site');
            }
    
            return json_decode($response->getBody(), true);
       
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
