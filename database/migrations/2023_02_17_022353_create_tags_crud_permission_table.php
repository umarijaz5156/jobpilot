<?php

use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TagPermissionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTagsCrudPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('tags_crud_permission', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        // Counting super admin role table rows
        $role_count = DB::table('roles')->count();
        if ($role_count == 0) {
            $this->callPermissionSeeder();
        }

        $this->callTagPermissionSeeder();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags_crud_permission');
    }

    public function callPermissionSeeder()
    {
        Artisan::call('db:seed', [
            '--class' => RolePermissionSeeder::class,
        ]);
    }

    public function callTagPermissionSeeder()
    {
        Artisan::call('db:seed', [
            '--class' => TagPermissionSeeder::class,
        ]);
    }
}
