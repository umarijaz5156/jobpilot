<?php

namespace App\Console\Commands;

use App\Models\Skill;
use App\Models\SkillTranslation;
use Illuminate\Console\Command;

class SyncAdditionalSkills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:skills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import additional skills';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $skills = collect(require storage_path('skills.php'));
        $languages = loadLanguage();

        if ($languages && count($languages) && $skills && count($skills)) {
            foreach ($skills as $skill) {
                $existing = SkillTranslation::query()
                    ->where('locale', 'en')
                    ->where('name', $skill)->exists();

                if ($existing) {
                    continue;
                }

                $skillModel = new Skill();
                $skillModel->save();
                foreach ($languages as $language) {
                    $skillModel->translateOrNew($language->code)->name = $skill;
                }

                $skillModel->save();
            }
        }

        return Command::SUCCESS;
    }
}
