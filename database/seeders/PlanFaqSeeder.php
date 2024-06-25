<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Faq\Entities\FaqCategory;

class PlanFaqSeeder extends Seeder
{
    public function run()
    {
        FaqCategory::create([
            'name' => 'Plan',
            'slug' => 'plan',
            'icon' => 'far fa-money-bill-alt',
        ]);
    }
}
