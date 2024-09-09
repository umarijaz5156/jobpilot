<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {

            $table->string('facebook_app_id', 255);
            $table->string('facebook_app_secret', 255);
            $table->string('facebook_page_id', 255);
            $table->text('facebook_access_token');

            $table->string('facebook_app_id_wl', 255)->after('facebook_access_token');
            $table->string('facebook_app_secret_wl', 255)->after('facebook_app_id_wl');
            $table->string('facebook_page_id_wl', 255)->after('facebook_app_secret_wl');
            $table->text('facebook_access_token_wl')->after('facebook_page_id_wl');

            $table->string('facebook_app_id_eh', 255)->after('facebook_access_token_wl');
            $table->string('facebook_app_secret_eh', 255)->after('facebook_app_id_eh');
            $table->string('facebook_page_id_eh', 255)->after('facebook_app_secret_eh');
            $table->text('facebook_access_token_eh')->after('facebook_page_id_eh');


            $table->string('facebook_app_id_pj', 255)->after('facebook_access_token_eh');
            $table->string('facebook_app_secret_pj', 255)->after('facebook_app_id_pj');
            $table->string('facebook_page_id_pj', 255)->after('facebook_app_secret_pj');
            $table->text('facebook_access_token_pj')->after('facebook_page_id_pj');

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
