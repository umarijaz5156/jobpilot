<?php

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
        Schema::table('cookies', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'approve_button_text', 'decline_button_text', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cookies', function (Blueprint $table) {
            $table->string('language')->default('en');
            $table->string('title');
            $table->text('description');
            $table->string('approve_button_text');
            $table->string('decline_button_text');
        });
    }
};
