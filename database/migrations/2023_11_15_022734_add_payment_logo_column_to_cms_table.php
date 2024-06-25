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
        Schema::table('cms', function (Blueprint $table) {
            $table->string('payment_logo1')->nullable();
            $table->string('payment_logo2')->nullable();
            $table->string('payment_logo3')->nullable();
            $table->string('payment_logo4')->nullable();
            $table->string('payment_logo5')->nullable();
            $table->string('payment_logo6')->nullable();
        });
        \Artisan::call('db:seed --class=PlanFaqSeeder --force');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms', function (Blueprint $table) {
            //
        });
    }
};
