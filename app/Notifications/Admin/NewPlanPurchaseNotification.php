<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPlanPurchaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $admin;

    public $order;

    public $plan;

    public $user;

    public function __construct($admin, $order, $plan, $user)
    {
        $this->admin = $admin;
        $this->order = $order;
        $this->plan = $plan;
        $this->user = $user;
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
        $type = 'new_plan_purchase';
        $formatted_mail_data = getFormattedTextByType($type, [
            'admin_name' => $this->admin->name,
            'user_name' => $this->user->name,
            'plan_label' => $this->plan->label,
        ]);
        $subject = $formatted_mail_data['subject'];
        $message = $formatted_mail_data['message'];

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
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
            'title' => ucfirst($this->user->name).' has purchased the '.ucfirst($this->plan->label).' plan!',
            'url' => route('order.show', $this->order->id),
        ];
    }
}
