<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidateCreateApprovalPendingNotification extends Notification
{
    use Queueable;

    public $user;

    public $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $type = 'new_pending_candidate';
        $formatted_mail_data = getFormattedTextByType($type, [
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'user_password' => $this->password,
        ]);
        $subject = $formatted_mail_data['subject'];
        $message = $formatted_mail_data['message'];

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->action('Login', url('login'))
            ->view('mails.email-template', ['content' => $message]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $url = $this->user->role == 'company' ? route('company.show', [$this->user->company->id]) : route('candidate.show', [$this->user->candidate->id]);

        return [
            'title' => 'A '.ucfirst($this->user->role).' registered recently',
            'url' => $url,
        ];
    }
}
