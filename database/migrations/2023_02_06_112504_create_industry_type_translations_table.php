<?php

use App\Models\IndustryType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndustryTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industry_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(IndustryType::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('locale');
            $table->timestamps();
        });

        \Artisan::call('db:seed --class=IndustryTypeTranslationSeeder --force');

        // Industry type table field droppings
        Schema::table('industry_types', function (Blueprint $table) {
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
        Schema::dropIfExists('industry_type_translations');
        Schema::table('industry_types', function (Blueprint $table) {
            $table->string('name');
            $table->string('slug');
        });
    }
}
