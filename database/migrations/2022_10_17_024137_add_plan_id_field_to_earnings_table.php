<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlanIdFieldToEarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\DB::getDriverName() !== 'sqlite') {
            Schema::table('earnings', function (Blueprint $table) {
                $table->dropForeign(['plan_id']);
            });
        }

        Schema::table('earnings', function (Blueprint $table) {
            $table->dropColumn('plan_id');
        });
        Schema::table('earnings', function (Blueprint $table) {
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->enum('payment_type', ['subscription_based', 'per_job_based'])->default('subscription_based');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (\DB::getDriverName() !== 'sqlite') {
            Schema::table('earnings', function (Blueprint $table) {
                $table->dropForeign(['plan_id']);
            });
        }

        Schema::table('earnings', function (Blueprint $table) {
            $table->dropColumn(['plan_id', 'payment_type']);
        });
    }
}
