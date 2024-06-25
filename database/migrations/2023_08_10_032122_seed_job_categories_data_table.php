<?php

use Database\Seeders\JobCategorySlugSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->callJobCategorySeeder();
    }

    public function callJobCategorySeeder()
    {
        Artisan::call('db:seed', [
            '--class' => JobCategorySlugSeeder::class,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
