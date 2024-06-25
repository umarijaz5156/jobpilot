<?php

namespace App\Listeners;

use Illuminate\Contracts\Auth\MustVerifyEmail;

class SendEmailVerificationNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (setting('email_verification')) {
            try {
                if (checkMailConfig()) {
                    if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
                        $event->user->sendEmailVerificationNotification();
                    }
                } else {
                    flashError('Mail send failed because of invalid mail configuration');
                }
            } catch (\Throwable $th) {
                flashError($th->getMessage());
            }
        }
    }
}
