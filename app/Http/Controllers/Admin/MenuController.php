<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuSettings;
use App\Services\Admin\Settings\MenuSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Language\Entities\Language;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'statusChange',
            'store',
            'update',
            'sortAble',
            'destroy',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            abort_if(! userCan('menu-setting.index'), 403);

            $data = (new MenuSettingService())->index($request);

            return view('backend.settings.pages.menu_settings', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update existing resource status.
     *
     * @return \Illuminate\Http\Response
     */
    public function statusChange(Request $request, MenuSettings $menuSetting)
    {
        abort_if(! userCan('menu-setting.update'), 403);

        try {
            $menuSetting->update([
                'status' => $request->status ? true : false,
            ]);

            // menu list cache clear
            Cache::forget('menu_lists');

            flashSuccess(__('menu_updated_successfully'));

            return redirect()->route('menu-settings.index');
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(! userCan('menu-setting.create'), 403);

        // validation
        $request->validate([
            'url' => 'required',
            'eligibility' => 'required',
        ]);

        // get all language
        $app_language = Language::latest()->get(['code', 'name']);
        $validate_array = [];
        foreach ($app_language as $language) {
            $validate_array['title_'.$language->code] = 'required|string|max:255';
        }
        $this->validate($request, $validate_array);

        // saving the data
        try {
            (new MenuSettingService())->store($request);

            // menu list cache clear
            Cache::forget('menu_lists');

            flashSuccess(__('menu_created_successfully'));

            return redirect()->back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(MenuSettings $menuSetting, Request $request)
    {
        try {
            abort_if(! userCan('menu-setting.update'), 403);

            $data = (new MenuSettingService())->index($request);
            $data['menu_setting'] = $menuSetting;

            return view('backend.settings.pages.menu_settings', $data);
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
    public function update(Request $request, MenuSettings $menuSetting)
    {
        abort_if(! userCan('menu-setting.update'), 403);

        // validation
        $app_language = Language::latest()->get(['code', 'name']);
        $validate_array = [];
        foreach ($app_language as $language) {
            $validate_array['title_'.$language->code] = 'required|string|max:255';
        }
        $this->validate($request, $validate_array);

        $request->validate([
            'url' => $menuSetting->default ? '' : 'required',
            'eligibility' => $menuSetting->default ? '' : 'required',
        ]);

        // saving the data
        try {
            (new MenuSettingService())->update($request, $menuSetting);

            // menu list cache clear
            Cache::forget('menu_lists');

            flashSuccess(__('menu_updated_successfully'));

            return redirect()->route('menu-settings.index');
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Sort the specified resource from database.
     *
     * @param  MenuSettings  $menuSetting
     * @return \Illuminate\Http\Response
     */
    public function sortAble(Request $request)
    {
        try {
            $menus = MenuSettings::all();

            foreach ($menus as $menu) {
                $menu->timestamps = false;
                $id = $menu->id;

                foreach ($request->order as $order) {
                    if ($order['id'] == $id) {
                        $menu->update(['order' => $order['position']]);
                    }
                }
            }

            // menu list cache clear
            Cache::forget('menu_lists');

            return response()->json(['message' => 'menu_updated_successfully']);
        } catch (\Throwable $th) {
            info($th->getMessage());
            flashError($th->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MenuSettings $menuSetting)
    {
        try {
            abort_if(! userCan('menu-setting.delete'), 403);

            if ($menuSetting->default) {
                flashWarning(__('dont_have_permission'));

                return redirect()->route('menu-settings.index');
            }

            $menuSetting->delete();

            // menu list cache clear
            Cache::forget('menu_lists');

            flashSuccess(__('menu_deleted_successfully'));

            return redirect()->route('menu-settings.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
