<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;

class StatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:status-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDateTime = now();
        $jobs = Job::where('status', '<>', 'expired')->get();

        foreach ($jobs as $job) {
            if ($job->deadline <= $currentDateTime) {
                $job->update(['status' => 'expired']);
            }
        }

        $this->info('Job statuses updated successfully.');
    }
}
