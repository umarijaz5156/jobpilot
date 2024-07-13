<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserPdfMail  extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $pdf)
    {
        $this->user = $user;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->user->user->name;
        $date = Carbon::now()->format('Y-m-d'); // Get today's date in 'YYYY-MM-DD' format
        $filename = $name . '_Report_' . $date . '.pdf';
                 return $this->subject('Your PDF Report')
        ->markdown('mails.user-pdf')
        ->attachData($this->pdf, $filename, [
            'mime' => 'application/pdf',
        ]);
    }

}
