<?php

namespace App\Services\Admin\Settings;

use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdListingService
{
    public function index()
    {
        $adlist = Advertisement::all();

        return $adlist;
    }

    public function update(Request $request)
    {

        $ad_codes = $request['ad_code'];

        foreach ($request->page_slug as $key => $value) {
            $get_ad = Advertisement::where('page_slug', $value)->first();

            if ($get_ad) {
                $get_ad->update([
                    'ad_code' => $ad_codes[$key],
                ]);
            }
        }
    }

    public function update_ad_status($request)
    {
        $advertisement = Advertisement::find($request['id']);
        $advertisement->status = $request['status'];
        $advertisement->save();
    }
}
