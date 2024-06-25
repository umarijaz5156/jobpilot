<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::all();

        return view('backend.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('backend.pages.create');
    }

    public function store(Request $request)
    {
        $slug = Str::slug($request->slug);
        $slug_exists = Page::where('slug', $slug)->exists();

        if ($slug_exists) {
            flashError('Slug has been used already');

            return back();
        }

        $request->validate([
            'title' => 'required|unique:pages,title|max:255',
            'content' => 'required',
            'footer_column_position' => 'required|numeric|between:1,4',
        ]);

        $page = Page::create([
            'title' => $request->title,
            'slug' => $slug,
            'footer_column_position' => $request->footer_column_position,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        if ($request->hasFile('meta_image')) {
            $url = uploadFileToPublic($request->meta_image, 'images/pages');
            $page->update(['meta_image' => $url]);
        }

        flashSuccess('Page created successfully');

        return back();
    }

    public function edit(Page $page)
    {
        return view('backend.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $slug = Str::slug($request->slug);
        $slug_exists = Page::where('id', '!=', $page->id)->where('slug', $slug)->exists();

        if ($slug_exists) {
            flashError('Slug has been used already');

            return back();
        }

        $request->validate([
            'title' => "required|unique:pages,title,$page->id|max:255",
            'content' => 'required',
            'footer_column_position' => 'required|numeric|between:1,4',
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => $slug,
            'footer_column_position' => $request->footer_column_position,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        if ($request->hasFile('meta_image')) {
            $url = uploadFileToPublic($request->meta_image, 'images/pages');
            $page->update(['meta_image' => $url]);
        }

        flashSuccess('Page updated successfully');

        return back();
    }

    public function delete(Page $page)
    {
        $page->delete();

        flashSuccess('Page deleted successfully');

        return back();
    }

    public function showCustomPage($slug)
    {

        Log::info('Debug: Inside showCustomPage method');
        $page = Page::where('slug', $slug)->first();

        if ($page != null) {
            return view('frontend.pages.custom_page', compact('page'));
        }

        abort(404);
    }

    public function changeShowInheader(Request $request)
    {
        $page = Page::findOrFail($request->id);
        $page->show_header = $request->status ?? 0;
        $page->save();

        return responseSuccess('Status updated successfully');
    }

    public function changeShowInFooter(Request $request)
    {
        $page = Page::findOrFail($request->id);
        $page->show_footer = $request->status ?? 0;
        $page->save();

        return responseSuccess('Status updated successfully');
    }
}
