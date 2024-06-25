<?php

namespace App\Services\API\Website;

use App\Models\Company;
use App\Models\Setting;
use App\Models\IndustryType;
use F9Web\ApiResponseHelpers;
use Modules\Location\Entities\Country;
use App\Http\Resources\Company\CompanyShortListResource;

class CompanyListService
{
    use ApiResponseHelpers;

    public function execute($request){
        if (auth('sanctum')->check() && auth('sanctum')->user()->role == 'company') {
            return $this->respondError(__('unauthorized_access'));
        }

        $query = Company::with('user', 'user.contactInfo')->withCount([
            'jobs as activejobs' => function ($q) {
                $q->where('status', 'active');

                $selected_country = request()->header('selected_country');
                if ($selected_country && $selected_country != null && $selected_country != 'all') {
                    $q->where('country', 'LIKE', "%$selected_country%");
                } else {

                    $setting = Setting::first();
                    if ($setting->app_country_type == 'single_base') {
                        if ($setting->app_country) {

                            $country = Country::where('id', $setting->app_country)->first();
                            if ($country) {
                                $q->where('country', 'LIKE', "%$country->name%");
                            }
                        }
                    }
                }
            }
        ])->withCount([
            'bookmarkCandidateCompany as candidatemarked' => function ($q) {
                $q->where('user_id', auth('sanctum')->id());
            }
        ])
            ->withCasts(['candidatemarked' => 'boolean'])->active();

        // Keyword search
        if ($request->has('keyword') && $request->keyword != null) {
            $keyword = $request->keyword;
            $query->whereHas('user', function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }

        // location search
        if ($request->has('lat') && $request->has('long') && $request->lat != null && $request->long != null) {
            $ids = $this->company_location_filter($request->lat, $request->long);
            $query->whereIn('id', $ids)->orWhere('country', $request->location ? $request->location : '');
        }

        // industry_type
        if ($request->has('industry_type') && $request->industry_type !== null) {
            $industry_type_id = IndustryType::where('name', $request->industry_type)->value('id');
            $query->where('industry_type_id', $industry_type_id);
        }

        // organization_type
        if ($request->has('organization_type') && $request->organization_type !== null) {

            $organization_type = $request->organization_type;

            $query->whereHas('organization', function ($q) use ($organization_type) {
                $q->where('name', $organization_type);
            });
        }
        // sortBy search
        if ($request->has('sortBy') && $request->sortBy) {
            if ($request->sortBy == 'latest') {
                $query->latest();
            } else {
                $query->oldest();
            }
        } else {
            $query->latest();
        }

        if ($request->filled('location')) {
            $query->where('country', $request->location);
        }

        $paginate = $request->has('paginate') ? $request->paginate : 12;

        return $query->latest('activejobs')
        ->paginate($paginate)
        ->withQueryString()
        ->through(function($data){
            return [
                'id' => $data->id,
                'name' => $data->user->name,
                'username' => $data->user->username,
                'country' => $data->country,
                'logo_url' => $data->logo_url,
                'activejobs' => $data->activejobs,
            ];
        });
    }


    public function company_location_filter($latitude, $longitude)
    {
        $distance = 50;

        $haversine = "(
                    6371 * acos(
                        cos(radians(" . $latitude . "))
                        * cos(radians(`lat`))
                        * cos(radians(`long`) - radians(" . $longitude . "))
                        + sin(radians(" . $latitude . ")) * sin(radians(`lat`))
                    )
                )";

        $data = Company::select('id')->selectRaw("$haversine AS distance")
            ->having("distance", "<=", $distance)->get();

        $ids = [];

        foreach ($data as $id) {
            array_push($ids, $id->id);
        }

        return $ids;
    }
}
