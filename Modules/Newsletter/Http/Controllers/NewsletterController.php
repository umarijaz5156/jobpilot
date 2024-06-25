<?php

namespace Modules\Newsletter\Http\Controllers;

use App\Notifications\VerifySubscriptionNotification;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Newsletter\Emails\NewsletterMail;
use Modules\Newsletter\Entities\Email;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:emails,email',
            ]);

            if (checkMailConfig()) {
                $email = $request->email;
                $subscribe_mail = [
                    'email' => $email,
                    'token' => Str::random(12),
                ];
                session()->put('subscribe_mail', $subscribe_mail);

                Notification::route('mail', $email)->notify(new VerifySubscriptionNotification($subscribe_mail['token']));
            }

            flashSuccess(__('we_sent_a_verify_mail_to_your_email_please_check_your_email'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * sent contact email to admin after your verify by email.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function subscribeDataSave($token = null)
    {
        try {
            $subscribe_mail = session()->get('subscribe_mail');

            if ($subscribe_mail && $subscribe_mail['token'] == $token) {
                Email::create(['email' => $subscribe_mail['email']]);
                flashSuccess(__('your_subscription_added_successfully'));

                $subscribe_mail = session()->forget('subscribe_mail');

                return redirect()->route('website.home');
            } else {
                flashWarning(__('your_verify_link_is_not_valid'));

                return redirect()->route('website.home');
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function sendMail()
    {
        try {
            abort_if(! userCan('newsletter.sendmail'), 403);

            $data['emails'] = Email::get();

            return view('newsletter::send-mail', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        try {
            abort_if(! userCan('newsletter.view'), 403);

            $data['emails'] = Email::latest()->paginate(20);

            return view('newsletter::index', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function destroy(Email $email)
    {
        try {
            $deleted = $email->delete();
            $deleted ? flashSuccess(__('email_deleted_successfully')) : flashError();

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function submitMail(Request $request)
    {
        try {
            abort_if(! userCan('newsletter.sendmail'), 403);

            $request->validate([
                'emails' => 'required',
                'subject' => 'required',
                'body' => 'required',
            ]);

            $arrayEmails = $request->emails;
            $emailSubject = $request->subject;
            $emailBody = $request->body;

            foreach ($arrayEmails as $email) {
                Mail::to($email)->send(new NewsletterMail($emailSubject, $emailBody));
            }

            flashSuccess(__('mail_sent_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
