<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Plan\Entities\Plan;

class CreatePlanDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Plan::class)->constrained()->onDelete('cascade');
            $table->string('locale', 4);
            $table->text('description');
            $table->timestamps();
        });

        \Artisan::call('db:seed --class=PlanDescriptionSeeder --force');
        // \Artisan::call('db:seed --class=Modules\Plan\Database\Seeders\PlanDescriptionSeederTableSeeder --force');

        info('Dropping description column from plans table');
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_descriptions');
        Schema::table('plans', function (Blueprint $table) {
            $table->text('description');
        });
    }
}
