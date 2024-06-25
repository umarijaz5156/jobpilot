<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\TagsImport;
use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Language\Entities\Language;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            abort_if(! userCan('tags.view'), 403);

            $tags = Tag::latest('id')->paginate(15);
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.tag.index', compact('tags', 'app_language'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Change status a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function statusChange(Request $request, Tag $tag)
    {
        try {
            $tag->update([
                'show_popular_list' => $request->status ?? false,
            ]);

            return ['message' => __('tag_status_updated_successfully')];
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
            abort_if(! userCan('tags.create'), 403);

            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $tag_exists = TagTranslation::where('name', $request->input('name_'.$language->code))
                    ->where('locale', $language->code)
                    ->first();
                if ($tag_exists) {
                    flashError(__('tag_already_exists_in').' '.getLanguageByCode($language->code).' '.__('language'));

                    return back();
                }

                $validate_array['name_'.$language->code] = 'required|string|max:255|unique:tag_translations,name';
            }
            $this->validate($request, $validate_array);

            // saving the data
            $tag = new Tag();
            $tag->save();

            foreach ($request->except('_token') as $key => $value) {
                $tag->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $tag->save();
            }

            flashSuccess(__('tag').' '.__('created').' '.__('successfully'));

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
    public function edit(Tag $tag)
    {
        try {
            abort_if(! userCan('tags.update'), 403);

            $tags = Tag::latest('id')->paginate(15);
            $app_language = Language::latest()->get(['code', 'name']);

            return view('backend.tag.index', compact('tags', 'tag', 'app_language'));
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
    public function update(Request $request, Tag $tag)
    {
        try {
            abort_if(! userCan('tags.update'), 403);

            // validation
            $app_language = Language::latest()->get(['code', 'name']);
            $validate_array = [];
            foreach ($app_language as $language) {
                $tag_exists = TagTranslation::where('name', $request->input('name_'.$language->code))
                    ->where('locale', $language->code)
                    ->where('tag_id', '!=', $tag->id)
                    ->first();
                if ($tag_exists) {
                    flashError(__('tag_already_exists_in').' '.getLanguageByCode($language->code).' '.__('language'));

                    return back();
                }

                $validate_array['name_'.$language->code] = 'required|string|max:255';
            }

            // saving the data
            foreach ($request->except(['_token', '_method']) as $key => $value) {
                $tag->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $tag->save();
            }

            $this->validate($request, $validate_array);

            foreach ($request->except(['_token', '_method']) as $key => $value) {
                $tag->translateOrNew(str_replace('name_', '', $key))->name = $value;
                $tag->save();
            }

            flashSuccess(__('tag').' '.__('updated').' '.__('successfully'));

            return redirect()->route('tags.index');
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
    public function destroy(Tag $tag)
    {
        try {
            abort_if(! userCan('tags.delete'), 403);

            // check if tag has jobs
            if ($tag && $tag->jobs->count()) {
                flashError(__('tag_has_jobs'));

                return back();
            }

            $success = $tag->delete();

            $success ? flashSuccess(__('tag').' '.__('deleted').' '.__('successfully').'!') : flashSuccess(__('something_went_wrong'));

            return redirect()->route('tags.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Bulk data Import.
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:csv,xlsx,xls',
        ]);

        try {
            Excel::import(new TagsImport(), $request->import_file);

            flashSuccess(__('tag_imported_successfully'));

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }
}
