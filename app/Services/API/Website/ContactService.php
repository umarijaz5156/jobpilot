<?php

namespace App\Services\API\Website;

use App\Models\Admin;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Contact\Notifications\ContactNotification;

class ContactService
{
    use ApiResponseHelpers;

    public function execute($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|min:3",
            'email' => "required",
            'subject' => "required|min:5",
            'message' => "required|min:10",
            'g-recaptcha-response' => config('captcha.active') ? 'required|captcha' : ''
        ], [
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()],
                422
            );
        }

        // to form creator first step
        if (checkMailConfig()) {

            $name = $request->name;
            $email = $request->email;
            $subject = $request->subject;
            $message = $request->message;

            $contact = [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'token' => Str::random(12),
            ];

            Admin::all()->each(function ($admin) use ($contact) {
                $admin->notify(new ContactNotification($contact));
            });
            return $this->respondOk(__('your_contact_mail_has_been_sent'));
        }
        return $this->respondOk(__('your_contact_mail_has_been_sent'));
        // return $this->respondError('Please set mail config to send message.');


    }
}
