<?php

namespace App\Http\Traits;

use App\Models\MenuSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

trait GetMenuTrait
{
    protected function hasMenuTable()
    {
        return Cache::rememberForever('has_menu_table', function () {
            return Schema::hasTable('menu_settings');
        });
    }

    protected function fetchMenuData()
    {
        if ($this->hasMenuTable()) {

            return MenuSettings::enabled()
                ->with(['translations'])
                ->orderBy('order', 'asc')
                ->get(['id', 'url', 'for'])
                ->map(function ($data) {
                    $translations = $data->translations;
                    $get_en_item = $translations->where('locale', 'en')->first();
                    $data->en_title = $get_en_item ? $get_en_item->title : '';

                    return $data;
                })
                ->toArray();
        }

        return [];
    }

    protected function filterMenuByValue($value)
    {
        $menuData = $this->fetchMenuData();

        return array_filter($menuData, function ($item) use ($value) {
            $for = json_decode($item['for'], true);

            return in_array($value, $for);
        });
    }

    public function getMenu(): array
    {
        return $this->fetchMenuData();
    }

    public function getSpecificMenu(string $value): array
    {
        return $this->filterMenuByValue($value);
    }

    public function publicMenu()
    {
        return $this->getSpecificMenu('public');
    }

    public function companyMenu()
    {
        return $this->getSpecificMenu('employee');
    }

    public function candidateMenu()
    {
        return $this->getSpecificMenu('candidate');
    }
}
