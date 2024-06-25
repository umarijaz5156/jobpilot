<?php

namespace App\Services\API\Website\Candidate;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Skill;
use App\Models\JobRole;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profession;
use App\Models\ContactInfo;
use F9Web\ApiResponseHelpers;
use App\Enums\SocialMediaEnum;
use App\Models\SkillTranslation;
use App\Models\CandidateLanguage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ProfessionTranslation;
use Illuminate\Support\Facades\Validator;

class UpdateCandidateSettingService
{
    use ApiResponseHelpers;

    public function execute($request){
        $user = auth('sanctum')->user();
        $candidate = $user->candidate;

        if ($request->type == 'personal') {
            return $this->updatePersonalInfo($request, $user, $candidate);
        }elseif ($request->type == 'profile') {
            return $this->updateProfileInfo($request, $candidate);
        }elseif ($request->type == 'social') {
            return $this->updateSocialInfo($request, $user, $candidate);
        }elseif ($request->type == 'contact') {
            $contactInfo = $user->contactInfo;

            return $this->updateContactInfo($request, $candidate, $contactInfo);
        }elseif ($request->type == 'password') {
            return $this->updatePasswordInfo($request, $user);
        }elseif ($request->type == 'account-delete') {
            return $this->deleteAccount($request, $user);
        }
    }

    protected function updatePersonalInfo($request, $user, $candidate){
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg,png,jpg',
            'name' => 'required|max:100',
            'title' => 'required|max:100',
            'experience_id' =>  'required',
            'education_id' =>  'required',
            'date_of_birth' => 'required|date',
        ], [
            'experience_id.required' => 'The experience field is required.',
            'education_id.required' => 'The education field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()], 422
            );
        }

        $user->update(['name' => $request->name]);
        $dateTime = Carbon::parse($request->date_of_birth);
        $date = $request['date_of_birth'] = $dateTime->format('Y-m-d H:i:s');

        $candidate->update([
            'title' => $request->title,
            'experience_id' => $request->experience_id,
            'education_id' => $request->education_id,
            'website' => $request->website,
            'birth_date' => Carbon::parse($date)->format('Y-m-d'),
        ]);

        // image
        if ($request->hasFile('image')) {
            $request->validate([
                'image' =>  'image|mimes:jpeg,png,jpg,|max:2048'
            ]);

            deleteImage($candidate->photo);
            $path = 'images/candidates';
            $image = uploadImage($request->image, $path);

            $candidate->update([
                "photo" => $image,
            ]);
            $user->update([
                "image" => $image,
            ]);
        }

        return $this->respondWithSuccess([
            'data' => [
                'message' => 'Basic Info Updated Successful!',
                'data' => [
                    'name' => $user->name,
                    'title' => $candidate->title,
                    'experience_id' => (int) $candidate->experience_id,
                    'education_id' => (int) $candidate->education_id,
                    'website' => $candidate->website,
                    'date_of_birth' => $candidate->birth_date,
                    'image_url' => $candidate->photo
                ],
            ]
        ]);
    }

    protected function updateProfileInfo($request, $candidate){
        $validator = Validator::make($request->all(), [
            'gender' => 'required',
            'marital_status' => 'required',
            'profession' => 'required',
            'status' => 'required',
            'bio' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()], 422
            );
        }

        if ($request->status == 'available_in') {
            $request->validate([
                'available_in' => 'required',
            ]);
        }

        // Profession
        $profession_request = $request->profession;
        $profession = ProfessionTranslation::where('profession_id', $profession_request)->orWhere('name', $profession_request)->first();

        if (! $profession) {
            $new_profession = Profession::create(['name' => $profession_request]);

            $languages = loadLanguage();
            foreach ($languages as $language) {
                $new_profession->translateOrNew($language->code)->name = $profession_request;
            }
            $new_profession->save();

            $profession_id = $new_profession->id;
        } else {
            $profession_id = $profession->profession_id;
        }

        $candidate->update([
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'bio' => $request->bio,
            'profession_id' => $profession_id,
            'status' => $request->status,
            'available_in' => $request->available_in ? Carbon::parse($request->available_in)?->format('Y-m-d') : null,
        ]);

        // skill & language
        $skills = $request->skills;

        if ($skills) {
            DB::table('candidate_skill')
                ->where('candidate_id', $candidate->id)
                ->delete();

            $skillsArray = [];

            foreach ($skills as $skill) {
                $skill_exists = SkillTranslation::where('skill_id', $skill)->orWhere('name', $skill)->first();

                if (! $skill_exists) {
                    $select_tag = Skill::create(['name' => $skill]);

                    $languages = loadLanguage();
                    foreach ($languages as $language) {
                        $select_tag->translateOrNew($language->code)->name = $skill;
                    }
                    $select_tag->save();

                    array_push($skillsArray, $select_tag->id);
                } else {
                    array_push($skillsArray, $skill_exists->skill_id);
                }
            }

            $candidate->skills()->attach($skillsArray);
        }

        if ($request->languages) {
            $candidate->languages()->sync($request->languages);
        }

        return $this->respondWithSuccess([
            'data' => [
                'message' => 'Profile Info Updated Successful!',
                'data' => [
                    'gender' => $candidate->gender,
                    'marital_status' => $candidate->marital_status,
                    'profession_id' => (int) $candidate->profession_id,
                    'bio' => $candidate->bio,
                    'availability' => $candidate->status,
                    'available_in' => $candidate->available_in ?? null,
                    'skills' => $candidate->skills->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                        ];
                    }),
                    'languages' => $candidate->languages->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                        ];
                    }),
                    'profession_list' => Profession::all()->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                        ];
                    }),
                    'skill_list' => Skill::all()->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                        ];
                    }),
                    'language_list' => CandidateLanguage::all()->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                        ];
                    }),
                ],
            ]
        ]);
    }

    protected function updateSocialInfo($request, $user, $candidate){
        $validator = Validator::make($request->all(), [
            'social_media.*' => 'required',
            'url.*' => ['required', 'regex:(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})'],
        ], [
            'social_media.*.required' => 'The social media field is required.',
            'url.*.required' => 'The url field is required.',
            'url.*.regex' => 'The url field must be a valid URL with www or https.',
        ]);
      

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()], 422
            );
        }

        $social_medias = $request->social_media;
        $urls = $request->url;

        if ($social_medias && $urls && count($social_medias)) {
            $user->socialInfo()->delete();

            foreach ($social_medias as $key => $value) {
                if ($value && $urls[$key]) {
                    $url = $urls[$key];
                    if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
                        // If not, prepend "https://"
                        $url = 'https://' . $url;
                    }
                    $user->socialInfo()->create([
                        'social_media' => $value,
                        'url' => $url,
                    ]);
                }
            }
        }

        return $this->respondWithSuccess([
            'data' => [
                'message' => 'Social Link Updated Successful!',
                'social_media' => $user->socialInfo->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'social_media' => $item->social_media,
                        'url' => $item->url,
                    ];
                })
            ],
        ]);
    }

    protected function updateContactInfo($request, $candidate, $contact){
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'secondary_phone' => 'nullable',
            'email' => 'required|email',
            'secondary_email' => 'nullable|email',
            'country' => 'sometimes',
            'city' => 'sometimes',
            'address' => 'sometimes',
            'lat' => 'sometimes',
            'long' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()], 422
            );
        }

        if (empty($contact)) {
            ContactInfo::create([
                'user_id' => auth()->id(),
                'phone' => $request->phone,
                'secondary_phone' => $request->secondary_phone,
                'email' => $request->email,
                'secondary_email' => $request->secondary_email,
            ]);
        } else {
            $contact->update([
                'phone' => $request->phone,
                'secondary_phone' => $request->secondary_phone,
                'email' => $request->email,
                'whatsapp_number' => $request->whatsapp_number,
                'secondary_email' => $request->secondary_email,
            ]);
        }

        if (! empty($request->whatsapp_number)) {
            $candidate->update(['whatsapp_number' => $request->whatsapp_number]);
        }

        // Location
        $candidate->update([
            'country' => $request->country,
            'address' => $request->address,
            'exact_location' => $request->exact_location,
            'lat' => $request->lat,
            'long' => $request->long,
        ]);

        return $this->respondWithSuccess([
            'data' => [
                'message' => 'Contact Information Updated Successful!',
                'data' => [
                    'contact_info' => [
                        'phone' => $request->phone,
                        'secondary_phone' => $request->secondary_phone,
                        'whatsapp_no' => $request->whatsapp_number,
                        'secondary_email' => $request->secondary_email,
                    ],
                    'location' => [
                        'country' => $request->country,
                        'address' => $request->address,
                        'exact_location' => $request->exact_location,
                        'latitude' => $request->lat,
                        'longitude' => $request->long,
                    ],
                ],
            ]
        ]);
    }

    protected function updatePasswordInfo($request, $user){
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required|min:8|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()], 422
            );
        }

        if (Hash::check($request->current_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        } else {
            return $this->respondError('Current password does not match!');
        }

        return $this->respondWithSuccess([
            'data' => [
                'message' => 'Password Updated Successful!',
            ]
        ]);
    }

    protected function deleteAccount($request, $user){
        $user = User::where('id', auth('sanctum')->id())->first();

        if ($user) {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    ['errors' => $validator->messages()], 422
                );
            }

            if (Hash::check($request->current_password, $user->password)) {
                tap($user, function($user){
                    $user->tokens()->delete();
                    $user->delete();
                });

                return $this->respondWithSuccess([
                    'data' => [
                        'message' => 'Account Deleted Successful!',
                    ]
                ]);
            } else {
                return $this->respondError('Current password does not match!');
            }
        }

        return $this->respondError('User not found!');
    }
}
