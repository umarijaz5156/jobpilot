<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Blog\Entities\PostCategory;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        try {
            abort_if(! userCan('post.view'), 403);

            $categories = PostCategory::withCount('posts')->get();

            return view('blog::postcategory.index', compact('categories'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function create()
    {
        try {
            abort_if(! userCan('post.create'), 403);

            return view('blog::postcategory.create');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:post_categories',
        ]);

        try {
            abort_if(! userCan('post.create'), 403);

            if ($request->file('image')) {
                $path = 'uploads/images/post-category';
                $image = uploadImage($request->image, $path, [50, 50]);
            }

            PostCategory::create([
                'name' => $request->name,
                'image' => $request->file('image') ? $image : 'backend/image/default.png',
                'slug' => Str::slug($request->name),
            ]);

            flashSuccess(__('category_created_successfully'));

            return redirect()->route('module.category.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show(PostCategory $category)
    {
        return view('blog::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit(PostCategory $category)
    {
        try {
            abort_if(! userCan('post.update'), 403);

            return view('blog::postcategory.edit', compact('category'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Renderable
     */

    // public function update(Request $request, PostCategory $category)
    // {
    //     abort_if(! userCan('post.update'), 403);

    //     if ($request->file('image')) {
    //         $path = 'category';
    //         $image = uploadImage($request->image, $path);
    //     }

    //     $category->update([
    //         'name' => $request->title,
    //         'image' => $request->file('image') ? $image : $category->image,
    //         'slug' => $request->slug,
    //     ]);

    //     flashSuccess(__('category_created_successfully'));

    //     return redirect()->route('module.category.index');
    // }
    public function update(Request $request, PostCategory $category)
    {
        try {
            abort_if(! userCan('post.update'), 403);

            if ($request->file('image')) {
                $path = 'uploads/images/post-category';
                $image = uploadImage($request->image, $path, [50, 50]);
            }

            $category->update([
                'name' => $request->title,
                'image' => $request->file('image') ? $image : $category->image,
                'slug' => $request->slug,
            ]);

            flashSuccess(__('category_created_successfully'));

            return redirect()->route('module.category.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy(PostCategory $category)
    {
        try {
            abort_if(! userCan('post.delete'), 403);

            deleteImage($category->image);
            $category->delete();

            return redirect()->back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
