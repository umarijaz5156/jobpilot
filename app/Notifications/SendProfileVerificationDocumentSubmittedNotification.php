<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendProfileVerificationDocumentSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject('New Document Verification Submission')
            ->line('Hello Admin,')
            ->line('A new document verification submission has been received on '.env('APP_NAME').'. by '.$this->company->user->name.'. Here are the details:')
            ->action('View Document', route('admin.company.documents', $this->company))
            ->line('Please log in to the admin dashboard to review and process the submission.')
            ->line('Thank you for maintaining the security and credibility of our platform.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => __('A new profile verification document is  available for approval'),
            'url' => route('admin.company.documents', $this->company),
        ];
    }
}
