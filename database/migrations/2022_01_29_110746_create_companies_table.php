<?php

use App\Models\IndustryType;
use App\Models\OrganizationType;
use App\Models\TeamSize;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(IndustryType::class, 'industry_type_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(OrganizationType::class, 'organization_type_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(TeamSize::class, 'team_size_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('nationality_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->date('establishment_date')->nullable();
            $table->string('website')->nullable();
            $table->boolean('visibility')->default(1);
            $table->boolean('profile_completion')->default(0);
            $table->text('bio')->nullable();
            $table->text('vision')->nullable();
            $table->unsignedBigInteger('total_views')->default(0);
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
        Schema::dropIfExists('companies');
    }
}
