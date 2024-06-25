<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Support\Str;
use App\Models\LanguageData;
use Illuminate\Database\Seeder;
use Modules\Plan\Entities\Plan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Modules\Faq\Entities\FaqCategory;
use Modules\Currency\Entities\Currency;
use Modules\Language\Entities\Language;
use Modules\Plan\Entities\PlanDescription;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Plan Create
        $plan = Plan::create([
            'label' => 'Trial',
            'price' => '0',
            'job_limit' => '1',
            'featured_job_limit' => '1',
            'highlight_job_limit' => '1',
            'candidate_cv_view_limit' => 5,
            'recommended' => false,
            'frontend_show' => false,
        ]);

        // Plan descriptions creating
        $languages = DB::table('languages')->get();

        if ($languages && count($languages) && count($languages) != 0) {
            foreach ($languages as $language) {
                PlanDescription::create([
                    'plan_id' => $plan->id,
                    'locale' => $language->code,
                    'description' => $plan->description ?? "{$language->code} description",
                ]);
            }
        }

        // Admin Create
        $role = Role::first();
        $admin = new Admin();
        $admin->name = 'Admin';
        $admin->email = 'admin@mail.com';
        $admin->image = 'backend/image/default.png';
        $admin->password = bcrypt('password');
        $admin->email_verified_at = Carbon::now();
        $admin->remember_token = Str::random(10);
        $admin->save();
        $admin->assignRole($role);

        // Faq Category Create
        FaqCategory::create([
            'name' => 'Job',
            'slug' => 'job',
            'icon' => 'fas fa-briefcase',
        ]);

        // Currency Create
        Currency::create([
            'name' => 'United State Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'symbol_position' => 'left',
        ]);

        // Language Create
        if (! Language::exists()) {
            Language::create([
                'name' => 'English',
                'code' => 'en',
                'icon' => 'flag-icon-gb',
                'direction' => 'ltr',
            ]);
        }

        $count = LanguageData::count();

        if ($count == 0) {
            // Call the seeder directly using dependency injection
            $seeder = new LanguageDataSeeder();
            $seeder->run();
        }
    }
}
