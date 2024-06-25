<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Traits\UploadAble;
use App\Models\Admin;

class ProfileController extends Controller
{
    use UploadAble;

    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'profile_update',
        ]);
    }

    /**
     * Profile View.
     *
     * @return void
     */
    public function profile()
    {
        try {
            $user = auth()->user();

            return view('backend.profile.index', compact('user'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Profile Setting.
     *
     * @return void
     */
    public function setting()
    {
        try {
            $user = Admin::find(auth()->id());

            return view('backend.profile.setting', compact('user'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Profile Update.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile_update(ProfileRequest $request)
    {
        try {
            $data = $request->only(['name', 'email']);
            $user = Admin::find(auth()->id());

            if ($request->hasFile('image')) {
                $data['image'] = uploadImage($request->image, 'user');

                deleteFile($user->image);
            }
            if ($request->isPasswordChange == 1) {
                $data['password'] = bcrypt($request->password);
            }

            $user->update($data);

            return back()->with('success', __('profile_update_successfully'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
