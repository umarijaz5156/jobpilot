<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateSearchCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_countries', function (Blueprint $table) {
            $table->id();
            $table->string('short_name', 100);
            $table->string('long', 255)->nullable();
            $table->string('lat', 255)->nullable();
            $table->string('name', 150);
            $table->timestamps();
        });

        Artisan::call('db:seed --class=SearchCountrySeeder --force');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_countries');
    }
}
