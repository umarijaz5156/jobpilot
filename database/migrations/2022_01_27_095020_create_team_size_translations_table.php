<?php

use App\Models\TeamSize;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamSizeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_size_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TeamSize::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('locale');
            $table->timestamps();
        });

        \Artisan::call('db:seed --class=TeamSizeTranslationSeeder --force');

        Schema::table('team_sizes', function (Blueprint $table) {
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
        Schema::dropIfExists('team_size_translations');
        Schema::table('team_sizes', function (Blueprint $table) {
            $table->string('name');
            $table->string('slug');
        });
    }
}
