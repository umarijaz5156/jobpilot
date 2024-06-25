<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class JobTypePermissionSeeder extends Seeder
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
        //  $roleSuperAdmin = Role::create(['name' => 'superadmin', 'guard_name' => 'admin']);

        //  permission List as array
        $permissions = [
            [
                'group_name' => 'attributes',
                'permissions' => [
                    'job_types.create',
                    'job_types.view',
                    'job_types.update',
                    'job_types.delete',
                ],
            ],
        ];

        // Assaign Permission
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
