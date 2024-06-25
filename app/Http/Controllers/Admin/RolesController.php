<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Role\CreateRole;
use App\Actions\Role\UpdateRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleFormRequest;
use App\Models\Admin;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'destroy',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            abort_if(! userCan('role.view'), 403);

            $roles = Role::SimplePaginate(10);

            return view('backend.roles.index', compact('roles'));
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
            abort_if(! userCan('role.create'), 403);

            $permissions = Permission::all();
            $permission_groups = Admin::getPermissionGroup();

            return view('backend.roles.create', compact('permissions', 'permission_groups'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RoleFormRequest $request)
    {
        abort_if(! userCan('role.create'), 403);

        try {
            CreateRole::create($request);

            flashSuccess(__('role_created_successfully'));

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        try {
            abort_if(! userCan('role.edit'), 403);

            $permissions = Permission::all();
            $permission_groups = Admin::getPermissionGroup();

            return view('backend.roles.edit', compact('permissions', 'permission_groups', 'role'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(RoleFormRequest $request, Role $role)
    {
        abort_if(! userCan('role.edit'), 403);

        try {
            UpdateRole::update($request, $role);

            flashSuccess(__('role_updated_successfully'));

            return back();
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
    public function destroy(Role $role)
    {
        abort_if(! userCan('role.delete'), 403);

        try {
            if (! is_null($role)) {
                $role->delete();
            }

            flashSuccess(__('role_deleted_successfully'));

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }
}
