<?php

namespace App\Notifications\Website\Candidate;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplyJobNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $user;

    public $company;

    public $job;

    public function __construct($user, $company, $job)
    {
        $this->user = $user;
        $this->company = $company;
        $this->job = $job;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->user->role == 'candidate') {
            return ['database', 'mail'];
        } else {
            return ['database'];
        }
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
            ->greeting('Dear '.$this->user->name)
            ->subject("Applied for {$this->job->title} at {$this->company->name}'")
            ->line("Thank you for applying for the position of {$this->job->title} at {$this->company->name}. We have received your application and it is currently under review.")
            ->line("If your qualifications align with our requirements, we will be in touch regarding the next steps. Thank you for considering {$this->company->name} as your potential employer.")
            ->line('Thank you for choosing <strong>'.config('app.name').'.</strong>');

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
            'title' => ucfirst($this->user->name).' has applied your job',
            'url' => route('company.myjob'),
            'title2' => 'You have applied the job of '.$this->company->name,
            'url2' => route('company.myjob'),
        ];
    }
}
