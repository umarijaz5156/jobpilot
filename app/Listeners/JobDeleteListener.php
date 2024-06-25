<?php

namespace App\Listeners;

use App\Events\JobDeleted;
use App\Services\Jobs\GoogleIndexingService;

class JobDeleteListener
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
     * @return void
     */
    public function handle(JobDeleted $event)
    {
        $job = $event->job;
        $notified = GoogleIndexingService::deleteJobIndexing($job);
    }
}
