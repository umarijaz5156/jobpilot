<?php

namespace App\Actions\User;

class UpdateUser
{
    /*
    * Update a user
    * @param object $request
    * @param object $user
    * @return bool
    */
    public static function update(object $request, object $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        if ($image = $request->image) {
            $url = uploadImage($image, 'user');
            $user->image = $url;
        }

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $user->roles()->detach();
        if ($request->roles) {
            $user->assignRole($request->roles);
        }

        return true;
    }
}
