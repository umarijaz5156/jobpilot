<?php

use Database\Seeders\LanguageDataSeeder;
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
        Schema::create('language_data', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->json('data');
            $table->timestamps();
        });

        // \Artisan::call('db:seed --class=LanguageDataSeeder --force');

        // Call the seeder directly using dependency injection
        $seeder = new LanguageDataSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('language_data');
    }
};
