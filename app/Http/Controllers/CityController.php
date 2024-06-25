<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = State::all();
        $stateId = request('state');
        $keyword = request('keyword');

        $citiesQuery = City::with('state');

        // Layer 1: Search by State ID
        if ($stateId) {
            $citiesQuery->where('state_id', $stateId);
        }

        // Layer 2: Search by Keyword
        if ($keyword) {
            $citiesQuery->where('name', 'like', '%'.$keyword.'%');
        }

        // Layer 3: Normal Search (no specific filters)
        if (! $stateId && ! $keyword) {
            $citiesQuery->where('id', '>', 0); // A condition that always evaluates to true.
        }
        $posts = $citiesQuery->paginate(10);

        return view('backend.settings.pages.location.city.index', ['categories' => $categories, 'posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = State::all();

        return view('backend.settings.pages.location.city.create', ['states' => $states]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'city_name' => 'required|string|max:255',
            'state_id' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);
        $city = new City();
        $city->name = $request->input('city_name');
        $city->state_id = $request->input('state_id');
        $city->lat = $request->input('lat');
        $city->long = $request->input('long');
        $city->save();

        return back()->with('success', 'City created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $city = City::findOrFail($id);
        $states = State::all();

        return view('backend.settings.pages.location.city.edit', compact('city', 'states'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);
        $request->validate([
            'city_name' => 'required|string|max:255',
            'state_id' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);

        $city->name = $request->input('city_name');
        $city->state_id = $request->input('state_id');
        $city->lat = $request->input('lat');
        $city->long = $request->input('long');
        $city->save();

        return back()->with('success', 'City update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return back()->with('success', 'City delete successfully.');
    }
}
