<?php

namespace App\Providers;

use App\Events\JobDeleted;
use App\Events\JobSaved;
use App\Listeners\JobDeleteListener;
use App\Listeners\JobSaveListener;
use App\Listeners\SendEmailVerificationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        JobSaved::class => [
            JobSaveListener::class,
        ],
        JobDeleted::class => [
            JobDeleteListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
