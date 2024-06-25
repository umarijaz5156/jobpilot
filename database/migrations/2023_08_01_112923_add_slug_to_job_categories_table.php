<?php

use Database\Seeders\JobCategorySlugSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_categories', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
        });

        // \Artisan::call('db:seed --class=JobCategorySlugSeeder --force');

        // Call the seeder directly using dependency injection
        $seeder = new JobCategorySlugSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_categories', function (Blueprint $table) {
            $table->dropColumn(['slug']);
        });
    }
};
