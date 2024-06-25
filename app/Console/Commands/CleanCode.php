<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanCode extends Command
{
    protected $signature = 'app:clean-code';

    protected $description = 'Remove unnecessary code before shipping to client';

    public function handle()
    {
        $files = glob(resource_path('views/**/**/*.blade.php'));

        foreach ($files as $file) {
            $content = file_get_contents($file);

            // Define the code block to be removed
            $codeBlockToRemove = '/@if\s*\(\s*config\(\'app\.demo_mode\'\)\)\s*.*?@endif/s';

            // Remove the entire @if and @endif blocks using regular expression
            $content = preg_replace($codeBlockToRemove, '', $content);

            file_put_contents($file, $content);
        }

        $this->info('Code blocks removed successfully.');
    }
}
