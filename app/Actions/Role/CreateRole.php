<?php

namespace App\Actions\Role;

use Spatie\Permission\Models\Role;

class CreateRole
{
    /*
    * Create a new role
    * @param object $request
    * @return bool
    */
    public static function create(object $request)
    {
        $role = Role::create(['name' => $request->name]);
        if (! empty($request->permissions)) {
            $role->syncPermissions($request->permissions);
        }

        return true;
    }
}
