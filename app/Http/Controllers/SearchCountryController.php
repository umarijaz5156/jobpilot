<?php

namespace App\Http\Controllers;

use App\Models\SearchCountry;
use Illuminate\Http\Request;

class SearchCountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keyword = request('keyword');

        $countryQuery = SearchCountry::query();

        // Layer 2: Search by Keyword
        if ($keyword) {
            $countryQuery->where('name', 'like', '%'.$keyword.'%');
        }

        // Layer 3: Normal Search (no specific filters)
        if ($keyword) {
            $countryQuery->where('id', '>', 0); // A condition that always evaluates to true.
        }
        $posts = $countryQuery->paginate(10);

        return view('backend.settings.pages.location.country.index', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $country = SearchCountry::all();

        return view('backend.settings.pages.location.country.create', ['country' => $country]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lat' => 'required',
            'long' => 'required',
            'short_name' => 'required',
        ]);
        $country = new SearchCountry();
        $country->name = $request->input('name');
        $country->short_name = $request->input('short_name');
        $country->lat = $request->input('lat');
        $country->long = $request->input('long');
        $country->save();

        return back()->with('success', 'Country created successfully.');
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
        $country = SearchCountry::findOrFail($id);

        return view('backend.settings.pages.location.country.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $country = SearchCountry::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'lat' => 'required',
            'long' => 'required',
            'short_name' => 'required',
        ]);

        $country->name = $request->input('name');
        $country->short_name = $request->input('short_name');
        $country->lat = $request->input('lat');
        $country->long = $request->input('long');
        $country->save();

        return back()->with('success', 'Country update successfully.');
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
            $country = SearchCountry::findOrFail($id);

            // Check if there are associated states
            if ($country->states()->count() > 0) {
                return back()->with('error', 'Cannot delete country with associated states.');
            }

            $country->delete();

            return back()->with('success', 'Country deleted successfully.');
        } catch (\Exception $e) {
            // Handle any exceptions, log them, or display an error message
            return back()->with('error', 'Error deleting country: '.$e->getMessage());
        }
    }
}
