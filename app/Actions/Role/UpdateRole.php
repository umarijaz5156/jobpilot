<?php

namespace App\Actions\Role;

class UpdateRole
{
    /*
    * Update a role
    * @param object $request
    * @param object $role
    * @return bool
    */
    public static function update(object $request, object $role)
    {
        if (! empty($request->permissions)) {
            $role->name = $request->name;
            $role->save();
            $role->syncPermissions($request->permissions);
        }

        return true;
    }
}
