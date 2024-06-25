<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCandidateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $candidate_name;

    public $subject;

    public $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($candidate_name, $subject, $body)
    {
        $this->candidate_name = $candidate_name;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Assuming getFormattedTextByType is a function that returns the formatted subject and message
        $data = $this->getFormattedTextByType('new_candidate', $this->candidate_name);
        $subject = $this->subject;
        $message = $this->body;

        return $this->subject($this->subject)
            ->markdown('mails.candidate-email')
            ->with([
                'body' => $this->body,
                'candidate_name' => $this->candidate_name,
            ]);
    }

    /**
     * Example method to get formatted text by type
     */
    protected function getFormattedTextByType($type, $name)
    {
        // Mockup of the getFormattedTextByType function
        return [
            'subject' => "New Candidate: $name",
            'message' => "Hello $name, welcome to our platform.",
        ];
    }
}
