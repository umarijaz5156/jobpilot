<?php

use App\Models\OrganizationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrganizationType::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('locale');
            $table->timestamps();
        });

        \Artisan::call('db:seed --class=OrganizationTypeTranslationSeeder --force');

        Schema::table('organization_types', function (Blueprint $table) {
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
        Schema::dropIfExists('organization_type_translations');
        Schema::table('organization_types', function (Blueprint $table) {
            $table->string('name');
            $table->string('slug');
        });
    }
}
