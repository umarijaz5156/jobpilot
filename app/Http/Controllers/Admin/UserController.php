<?php

namespace App\Http\Controllers\Admin;

use App\Actions\User\CreateUser;
use App\Actions\User\UpdateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Models\Admin;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'update',
            'destroy',
        ]);
    }

    public function dashboard()
    {
        try {
            session(['layout_mode' => 'left_nav']);

            return view('backend.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            abort_if(! userCan('admin.view'), 403);

            $users = Admin::where('id', '!=', 1)->SimplePaginate(10);

            return view('backend.users.index', compact('users'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            abort_if(! userCan('admin.create'), 403);

            $roles = Role::all();

            return view('backend.users.create', compact('roles'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\UserFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserFormRequest $request)
    {
        try {
            abort_if(! userCan('admin.create'), 403);

            try {
                CreateUser::create($request);

                flashSuccess(__('user_created_successfully'));

                return redirect()->route('user.index');
            } catch (\Throwable $th) {
                flashError($th->getMessage());

                return back();
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $user)
    {
        try {
            abort_if(! userCan('admin.edit'), 403);

            $roles = Role::all();

            return view('backend.users.edit', compact('roles', 'user'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserFormRequest $request, Admin $user)
    {
        abort_if(! userCan('admin.edit'), 403);

        try {
            UpdateUser::update($request, $user);

            flashSuccess(__('user_updated_successfully'));

            return redirect()->route('user.index');
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $user)
    {
        abort_if(! userCan('admin.delete'), 403);

        try {
            if (! is_null($user)) {
                $user->delete();
            }

            flashSuccess(__('user_deleted_successfully'));

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }
}
