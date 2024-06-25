<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DBSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbseed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run DB Seed Command for Development Version';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Dropping Database');
        \Artisan::call('db:wipe');

        $this->info('Importing Database');
        $sql = storage_path('app.sql');

        $db = [
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'host' => env('DB_HOST'),
            'database' => env('DB_DATABASE'),
        ];

        exec("mysql --user={$db['username']} --password={$db['password']} --host={$db['host']} --database {$db['database']} < $sql");

        $this->info('Completed Importing Database');

        return Command::SUCCESS;
    }
}
