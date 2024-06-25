<?php

use App\Models\Benefit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBenefitTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('benefit_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Benefit::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('locale');
            $table->timestamps();
        });

        \Artisan::call('db:seed --class=BenefitTranslationSeeder --force');

        Schema::table('benefits', function (Blueprint $table) {
            $table->dropColumn(['name', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('benefit_translations');
        Schema::table('benefits', function (Blueprint $table) {
            $table->string('name');
            $table->string('slug');
        });
    }
}
