<?php

use Database\Seeders\AdvertisementSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('page_slug');
            $table->longText('ad_code')->nullable();
            $table->string('place_example_image')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
        Artisan::call('db:seed', [
            '--class' => AdvertisementSeeder::class,
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertisements');
    }
}
