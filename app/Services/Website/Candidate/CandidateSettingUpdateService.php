<?php

namespace App\Services\Website\Candidate;

use App\Mail\SendEmailUpdateVerification;
use App\Models\Candidate;
use App\Models\ContactInfo;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profession;
use App\Models\ProfessionTranslation;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\SkillTranslation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Language\Entities\Language;

class CandidateSettingUpdateService
{
    /**
     * Candidate setting update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($request)
    {
        $user = User::FindOrFail(auth()->id());
        $candidate = Candidate::where('user_id', $user->id)->first();
        $contactInfo = ContactInfo::where('user_id', auth()->id())->first();
        $request->session()->put('type', $request->type);

        if ($request->type == 'basic') {
            $this->candidateBasicInfoUpdate($request, $user, $candidate);
            $candidate->update(['profile_complete' => $candidate->profile_complete != 0 ? $candidate->profile_complete - 25 : 0]);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'profile') {
            $this->candidateProfileInfoUpdate($request, $candidate);
            $candidate->update(['profile_complete' => $candidate->profile_complete != 0 ? $candidate->profile_complete - 25 : 0]);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'social') {
            $this->socialUpdate($request);
            $candidate->update(['profile_complete' => $candidate->profile_complete != 0 ? $candidate->profile_complete - 25 : 0]);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'contact') {
            $this->contactUpdate($request, $candidate);
            $candidate->update(['profile_complete' => $candidate->profile_complete != 0 ? $candidate->profile_complete - 25 : 0]);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'account') {

            $this->emailUpdate($request) ? flashSuccess(__('Mail Verification Sent')) : flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'alert') {
            $this->alertUpdate($request, $candidate);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'visibility') {
            $this->visibilityUpdate($request, $candidate);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'password') {
            $this->passwordUpdate($request, $user, $candidate);
            flashSuccess(__('profile_updated'));

            return back();
        }

        if ($request->type == 'account-delete') {
            $this->accountDelete($user);
        }
    }

    /**
     * Candidate basic setting update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @param  \App\Models\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function candidateBasicInfoUpdate($request, $user, $candidate)
    {
        $request->validate([
            'name' => 'required',
            'birth_date' => 'date',
            'birth_date' => 'required',
            'education' => 'required',
            'experience' => 'required',
        ]);
        $user->update(['name' => $request->name]);

        // Experience
        $experience_request = $request->experience;
        $experience = Experience::where('id', $experience_request)->first();

        if (! $experience) {
            $experience = Experience::create(['name' => $experience_request]);
        }

        // Education
        $education_request = $request->education;
        $education = Education::where('id', $education_request)->first();

        if (! $education) {
            $education = Education::create(['name' => $education_request]);
        }

        $dateTime = Carbon::parse($request->birth_date);
        $date = $request['birth_date'] = $dateTime->format('Y-m-d H:i:s');

        $candidate->update([
            'title' => $request->title,
            'experience_id' => $experience->id,
            'education_id' => $education->id,
            'website' => $request->website,
            'birth_date' => $date,
        ]);

        // image
        if ($request->image) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg',
            ]);

            deleteImage($candidate->photo);

            $path = 'uploads/images/candidates';
            $image = uploadImage($request->image, $path, [164, 164]);

            $candidate->update([
                'photo' => $image,
            ]);
        }
        // cv
        if ($request->cv) {
            $request->validate([
                'cv' => 'mimetypes:application/pdf,jpeg,docs|max:5048',
            ]);
            $pdfPath = '/file/candidates/';
            $pdf = pdfUpload($request->cv, $pdfPath);

            $candidate->update([
                'cv' => $pdf,
            ]);
        }

        return true;
    }

    /**
     * Candidate profile setting update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Candidate  $candidate
     * @return bool
     */
    public function candidateProfileInfoUpdate($request, $candidate)
    {
        $request->validate([
            'gender' => 'required',
            'marital_status' => 'required',
            'profession' => 'required',
            'status' => 'required',
        ]);

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
            'available_in' => $request->available_in ? Carbon::parse($request->available_in)->format('Y-m-d') : null,
        ]);

        // skill & language
        $skills = $request->skills;
        DB::table('candidate_skill')->where('candidate_id', $candidate->id)->delete();

        if ($skills) {
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

        $candidate->languages()->sync($request->languages);

        return true;
    }

    /**
     * Candidate contact setting update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Candidate  $candidate
     * @return bool
     */
    public function contactUpdate($request, $candidate)
    {
        $contact = ContactInfo::where('user_id', auth()->id())->first();

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
        updateMap(auth()->user()->candidate);

        return true;
    }

    /**
     * Candidate email setting update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Candidate  $candidate
     */
    public function emailUpdate($request): bool
    {
        $user = $request->user();
        $setting = Setting::query()->first();

        $validated = $request->validate([
            'account_email' => 'required|email|unique:users,email,'.$user->id,
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
     * Candidate social setting update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
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
     * Candidate visibility setting update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Candidate  $candidate
     * @return bool
     */
    public function visibilityUpdate($request, $candidate)
    {
        $candidate->update([
            'visibility' => $request->profile_visibility ? 1 : 0,
            'cv_visibility' => $request->cv_visibility ? 1 : 0,
        ]);

        return true;
    }

    /**
     * Candidate password setting update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Candidate  $candidate
     * @return bool
     */
    public function passwordUpdate($request, $user, $candidate)
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
     * Candidate account delete
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function accountDelete($user)
    {
        $user->delete();

        return true;
    }

    /**
     * Candidate alert setting update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Candidate  $candidate
     */
    public function alertUpdate($request, $candidate): bool
    {
        if ($request->has('received_job_alert') && $request->alert_type == 'status') {
            $candidate->update([
                'role_id' => $request->role_id,
                'received_job_alert' => $request->received_job_alert ? 1 : 0,
            ]);
        }

        if ($request->has('job_roles')) {
            $candidate->jobRoleAlerts()->delete();

            foreach ($request->job_roles as $role) {
                $candidate->jobRoleAlerts()->create([
                    'job_role_id' => $role,
                ]);
            }
        }

        if (! $request->has('job_roles') && $request->alert_type == 'role' && count($candidate->jobRoleAlerts) > 0) {
            $candidate->jobRoleAlerts()->delete();
        }

        return true;
    }
}
