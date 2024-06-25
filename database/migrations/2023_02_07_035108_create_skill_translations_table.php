<?php

use App\Models\Skill;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Skill::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('locale');
            $table->timestamps();
        });

        \Artisan::call('db:seed --class=SkillTranslationSeeder --force');

        Schema::table('skills', function (Blueprint $table) {
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
        Schema::dropIfExists('skill_translations');
        Schema::table('skills', function (Blueprint $table) {
            $table->string('name');
            $table->string('slug');
        });
    }
}
