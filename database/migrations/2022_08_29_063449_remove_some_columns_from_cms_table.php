<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSomeColumnsFromCmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cms', function (Blueprint $table) {
            $table->dropColumn([
                'home_page_banner_title',
                'home_page_banner_subtitle',
                'about_title',
                'about_sub_title',
                'about_description',
                'contact_title',
                'contact_subtitle',
                'contact_description',
                'mission_title',
                'mission_sub_title',
                'mission_description',
                'page403_title',
                'page403_type',
                'page403_subtitle',
                'page404_title',
                'page404_type',
                'page404_subtitle',
                'page500_title',
                'page500_type',
                'page500_subtitle',
                'page503_title',
                'page503_type',
                'page503_subtitle',
                'comingsoon_title',
                'comingsoon_subtitle',
                'maintenance_title',
                'maintenance_subtitle',
                'account_setup_title',
                'account_setup_subtitle',
                'employers_description',
                'employers_title',
                'candidate_description',
                'candidate_title',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cms', function (Blueprint $table) {
            //
        });
    }
}
