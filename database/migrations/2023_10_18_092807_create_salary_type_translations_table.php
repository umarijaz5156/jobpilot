<?php

use App\Models\SalaryType;
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
        Schema::create('salary_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SalaryType::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('locale');
            $table->timestamps();
        });

        \Artisan::call('db:seed --class=SalaryTypeTranslationSeeder --force');

        Schema::table('salary_types', function (Blueprint $table) {
            $table->dropColumn(['name']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_type_translations');
        Schema::table('salary_types', function (Blueprint $table) {
            $table->string('name');
        });
    }
};
