<?php

namespace App\Notifications\Website\Company;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanPurchaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    public $plan_type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $plan_type)
    {
        $this->user = $user;
        $this->plan_type = $plan_type;
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
        $type = 'plan_purchase';
        $formatted_mail_data = getFormattedTextByType($type, [
            'user_name' => $this->user->name,
            'plan_type' => $this->plan_type,
        ]);
        $subject = $formatted_mail_data['subject'];
        $message = $formatted_mail_data['message'];

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->action('View Plan', url('dashboard/plans-billing'))
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
        return [
            'title' => $this->plan_type.' plan purchased',
        ];
    }
}
