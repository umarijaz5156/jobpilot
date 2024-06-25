<?php

use App\Models\MenuSettings;
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
        Schema::create('menu_settings_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MenuSettings::class)->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('locale');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_settings_translations');
    }
};
