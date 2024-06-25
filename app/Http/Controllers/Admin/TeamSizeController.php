<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamSize;
use Illuminate\Http\Request;
use Modules\Language\Entities\Language;

class TeamSizeController extends Controller
{
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

            $team_sizes = TeamSize::with('translations')->paginate(10);

            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.team_size.index', compact('team_sizes', 'app_language'));
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
    public function store(Request $request)
    {
        try {
            abort_if(! userCan('team_sizes.create'), 403);

            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $this->validate($request, $validate_array);

            // saving the data
            $team_size = new TeamSize();
            $team_size->save();

            foreach ($request->except('_token') as $key => $value) {
                $team_size->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $team_size->save();
            }

            flashSuccess(__('team_size_created_successfully'));

            return back();
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
    public function edit(TeamSize $teamSize)
    {
        try {
            abort_if(! userCan('team_sizes.update'), 403);
            $team_size = $teamSize;
            $team_sizes = TeamSize::with('translations')->paginate(10);
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.team_size.index', compact('team_size', 'team_sizes', 'app_language'));
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
    public function update(Request $request, TeamSize $teamSize)
    {
        try {
            abort_if(! userCan('team_sizes.update'), 403);

            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }
            $this->validate($request, $validate_array);

            // saving the data
            foreach ($request->except(['_token', '_method']) as $key => $value) {
                $teamSize->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $teamSize->save();
            }

            flashSuccess(__('team_size_updated_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeamSize $teamSize)
    {
        try {
            abort_if(! userCan('team_sizes.delete'), 403);

            // check if the team_size has candidates
            if ($teamSize && $teamSize->companies->count()) {
                flashError(__('team_size_has_candidates'));

                return back();
            }

            $teamSize->delete();

            flashSuccess(__('team_size_deleted_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
