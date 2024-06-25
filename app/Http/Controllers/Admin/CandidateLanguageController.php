<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateLanguage;
use Illuminate\Http\Request;

class CandidateLanguageController extends Controller
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
    public function index(Request $request)
    {
        try {
            abort_if(! userCan('candidate-language.view'), 403);

            $candidate_languages = CandidateLanguage::latest('id')
                ->when($request->has('keyword') && $request->keyword != null, function ($q) use ($request) {
                    $q->where('name', 'LIKE', "%$request->keyword%");
                })
                ->paginate(20)
                ->withQueryString();

            return view('backend.candidate.language.index', compact('candidate_languages'));
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
            abort_if(! userCan('candidate-language.create'), 403);

            $request->validate([
                'name' => 'required',
            ]);

            CandidateLanguage::create(['name' => $request->name]);

            flashSuccess(__('language_created_successfully'));

            return redirect()->route('admin.candidate.language.index');
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
    public function edit($id)
    {
        try {
            abort_if(! userCan('candidate-language.update'), 403);

            $item = CandidateLanguage::FindOrFail($id);
            $candidate_languages = CandidateLanguage::latest('id')->paginate(20);

            return view('backend.candidate.language.index', compact('candidate_languages', 'item'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            abort_if(! userCan('candidate-language.update'), 403);

            $request->validate(['name' => 'required']);

            $item = CandidateLanguage::FindOrFail($id);
            $item->update(['name' => $request->name]);

            flashSuccess(__('language_updated_successfully'));

            return redirect()->route('admin.candidate.language.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
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
            abort_if(! userCan('candidate-language.delete'), 403);

            $item = CandidateLanguage::FindOrFail($id);
            $item->delete();

            flashSuccess(__('language_deleted_successfully'));

            return redirect()->route('admin.candidate.language.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
