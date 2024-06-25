<?php

namespace App\Http\Controllers;

use App\Models\SearchCountry;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index()
    {
        $countries = SearchCountry::all();
        $countryId = request('country');
        $keyword = request('keyword');

        $citiesQuery = State::with('country');
        // Layer 1: Search by State ID
        if ($countryId) {
            $citiesQuery->where('country_id', $countryId);
        }

        // Layer 2: Search by Keyword
        if ($keyword) {
            $citiesQuery->where('name', 'like', '%'.$keyword.'%');
        }

        // Layer 3: Normal Search (no specific filters)
        if (! $countryId && ! $keyword) {
            $citiesQuery->where('id', '>', 0); // A condition that always evaluates to true.
        }
        $states = $citiesQuery->paginate(10);

        return view('backend.settings.pages.location.state.index', ['countries' => $countries, 'states' => $states]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = SearchCountry::all();

        return view('backend.settings.pages.location.state.create', ['countries' => $countries]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'state_name' => 'required|string|max:255',
            'country_id' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);
        $state = new State();
        $state->name = $request->input('state_name');
        $state->country_id = $request->input('country_id');
        $state->lat = $request->input('lat');
        $state->lat = $request->input('lat');
        $state->save();

        return back()->with('success', 'State created successfully.');
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
        $state = State::findOrFail($id);
        $countries = SearchCountry::all();

        return view('backend.settings.pages.location.state.edit', compact('state', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $state = State::findOrFail($id);
        $request->validate([
            'state_name' => 'required|string|max:255',
            'country_id' => 'required',
            'lat' => 'required',
            'lat' => 'required',
        ]);

        $state->name = $request->input('state_name');
        $state->country_id = $request->input('country_id');
        $state->lat = $request->input('lat');
        $state->lat = $request->input('lat');
        $state->save();

        return back()->with('success', 'State update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $state = State::findOrFail($id);

            // Check if there are associated states
            if ($state->cities()->count() > 0) {
                return back()->with('error', 'Cannot delete country with associated cities.');
            }

            // Now you can delete the state
            $state->delete();

            return back()->with('success', 'State deleted successfully.');
        } catch (\Exception $e) {
            // Handle any exceptions, log them, or display an error message
            return back()->with('error', 'Error deleting state: '.$e->getMessage());
        }
    }
}
