<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;

class UpdateJobStatus extends Command
{
    protected $signature = 'jobs:updatestatus';

    protected $description = 'Update job statuses based on deadlines';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $currentDateTime = now();
        $jobs = Job::where('status', '<>', 'expired')->get();

        foreach ($jobs as $job) {
            $deadline = $job->deadline;

            if ($deadline <= $currentDateTime) {
                $job->update([
                    'status' => 'expired',
                ]);
            }
        }
        $this->info('Job statuses updated successfully.');
    }
}
