<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveNationalityFieldFromCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\DB::getDriverName() !== 'sqlite') {
            Schema::table('candidates', function (Blueprint $table) {
                $table->dropForeign(['nationality_id']);
            });
        }

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['nationality_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->unsignedBigInteger('nationality_id');
        });
    }
}
