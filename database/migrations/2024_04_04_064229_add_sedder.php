<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Artisan::call('db:seed --class=JobTypePermissionSeeder --force');
        \Artisan::call('db:seed --class=TeamSizePermissionSeeder --force');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
