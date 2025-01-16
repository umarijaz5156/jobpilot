<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobScrapedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $detailedJobs;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($detailedJobs, $user)
    {
        $this->detailedJobs = $detailedJobs;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->subject('New Jobs Added to Our System')
                    ->view('mails.job_scraped_notification');
    }
}
