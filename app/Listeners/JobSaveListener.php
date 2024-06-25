<?php

namespace App\Listeners;

use App\Events\JobSaved;
use App\Services\Jobs\GoogleIndexingService;

class JobSaveListener
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
    public function handle(JobSaved $event)
    {
        $job = $event->job;
        if ($job->status === 'active') {
            $notified = GoogleIndexingService::updateJobIndexing($job);
        }
    }
}
