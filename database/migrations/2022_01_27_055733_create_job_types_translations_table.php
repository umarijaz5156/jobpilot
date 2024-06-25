<?php

use App\Models\JobType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobTypesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JobType::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('locale');
            $table->timestamps();
        });

        \Artisan::call('db:seed --class=JobTypeTranslationSeeder --force');

        Schema::table('job_types', function (Blueprint $table) {
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
        Schema::dropIfExists('job_type_translations');
        Schema::table('job_types', function (Blueprint $table) {
            $table->string('name');
            $table->string('slug');
        });
    }
}
