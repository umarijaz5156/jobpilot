<?php

use Database\Seeders\MenuSettingsSeeder;
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
        $this->callMenuSettingsSeeder();
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

    public function callMenuSettingsSeeder()
    {
        Artisan::call('db:seed', [
            '--class' => MenuSettingsSeeder::class,
        ]);
    }
};
