<?php

use App\Models\JobCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobCategoryTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JobCategory::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('locale');
            $table->timestamps();
        });

        \Artisan::call('db:seed --class=JobCategoryTranslationSeeder --force');

        Schema::table('job_categories', function (Blueprint $table) {
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
        Schema::dropIfExists('job_category_translations');
        Schema::table('job_categories', function (Blueprint $table) {
            $table->string('name');
            $table->string('slug');
        });
    }
}
