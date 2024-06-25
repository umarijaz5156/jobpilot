<?php

namespace App\Services\Admin\Settings;

use App\Models\MenuSettings;
use Illuminate\Support\Str;
use Modules\Language\Entities\Language;

class MenuSettingService
{
    /**
     * Get menu settings
     *
     * @return void
     */
    public function index(object $request): array
    {
        $query = MenuSettings::query();

        if ($request->has('for') && $request->for != null) {
            $query->whereJsonContains('for', $request->for);
        } else {
            $query->whereJsonContains('for', 'public');
        }

        $data['menus'] = $query->orderBy('order', 'asc')->paginate(20);

        $data['app_languages'] = Language::latest()->get(['code', 'name']);

        return $data;
    }

    /**
     * Store menu settings
     */
    public function store(object $request): void
    {
        // url validate
        $userInput = $request->url;
        if (Str::startsWith($userInput, ['http://', 'https://'])) {
            // This is an external link, so we don't need to convert it to a slug
            $validSlug = $userInput;
        } else {
            // This is a regular string, so we can convert it to a slug
            $validSlug = '/'.Str::slug($userInput);
        }
        $order_first = MenuSettings::orderBy('order', 'DESC')->first();
        $menu = new MenuSettings();
        $menu->url = $validSlug;
        $menu->status = $request->has('status') ? true : false;
        $menu->order = $order_first ? $order_first->order + 1 : 0;
        $menu->for = json_encode($request->eligibility);
        $menu->save();

        foreach ($request->except([
            '_token',
            'url',
            'status',
            'eligibility',
        ]) as $key => $value) {
            $code = str_replace('title_', '', $key);
            $menu->translateOrNew($code)->title = $value;
            $menu->save();
        }
    }

    /**
     * Store menu settings
     */
    public function update(object $request, object $menuSetting): void
    {
        // url validate
        $userInput = $request->url;
        if (Str::startsWith($userInput, ['http://', 'https://'])) {
            // This is an external link, so we don't need to convert it to a slug
            $validSlug = $userInput;
        } else {
            // This is a regular string, so we can convert it to a slug
            $validSlug = '/'.Str::slug($userInput);
        }
        $menuSetting->update([
            'url' => $menuSetting->default ? $menuSetting->url : $validSlug,
            'status' => $request->has('status') ? true : false,
            'for' => $menuSetting->default ? $menuSetting->for : json_encode($request->eligibility),
        ]);

        foreach ($request->except([
            '_token',
            '_method',
            'url',
            'status',
            'eligibility',
        ]) as $key => $value) {
            $code = str_replace('title_', '', $key);
            $menuSetting->translateOrNew($code)->title = $value;
            $menuSetting->save();
        }
    }
}
