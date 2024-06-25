<?php

use Database\Seeders\CandidateLanguagePermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCandidateLanguagePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('candidate_language_permission', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        // Counting super admin role table rows
        $role_count = DB::table('roles')->count();
        if ($role_count == 0) {
            $this->callPermissionSeeder();
        }

        $this->createCandidateLanguagePermission();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_language_permission');
    }

    public function callPermissionSeeder()
    {
        Artisan::call('db:seed', [
            '--class' => RolePermissionSeeder::class,
        ]);
    }

    public function createCandidateLanguagePermission()
    {
        Artisan::call('db:seed', [
            '--class' => CandidateLanguagePermissionSeeder::class,
        ]);
    }
}
