<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CandidateLanguagePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //  Create roles
        $roleSuperAdmin = Role::first();

        //  permission List as array
        $permissions = [
            [
                'group_name' => 'candidate-language',
                'permissions' => [
                    'candidate-language.create',
                    'candidate-language.view',
                    'candidate-language.update',
                    'candidate-language.delete',
                ],
            ],
        ];

        // Assign Permission
        for ($i = 0; $i < count($permissions); $i++) {
            $permissionGroup = $permissions[$i]['group_name'];

            for ($j = 0; $j < count($permissions[$i]['permissions']); $j++) {
                // Create Permission
                $permission = Permission::create([
                    'name' => $permissions[$i]['permissions'][$j],
                    'group_name' => $permissionGroup,
                    'guard_name' => 'admin',
                ]);

                $roleSuperAdmin->givePermissionTo($permission);
            }
        }
    }
}
