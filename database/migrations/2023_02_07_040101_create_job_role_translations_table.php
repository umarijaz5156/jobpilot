<?php

use App\Models\JobRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobRoleTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_role_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JobRole::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('locale');
            $table->timestamps();
        });

        \Artisan::call('db:seed --class=JobRoleTranslationSeeder --force');

        Schema::table('job_roles', function (Blueprint $table) {
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
        Schema::dropIfExists('job_role_translations');
        Schema::table('job_roles', function (Blueprint $table) {
            $table->string('name');
            $table->string('slug');
        });
    }
}
