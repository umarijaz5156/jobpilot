<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostComment;

class PostCommentController extends Controller
{
    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show($slug)
    {
        try {
            $post = Post::where('slug', $slug)
                ->with('comments', 'comments.user')
                ->withCount('comments')
                ->first();

            return view('blog::comments.show', compact('post'));

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
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'body' => 'required',
            ]);
            $comment = PostComment::findOrFail($id);
            $comment->body = $request->body;
            $comment->save();
            flashSuccess(__('comment_edited_successfully'));

            return back();
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
    public function destroy($id)
    {
        try {
            $comment = PostComment::findOrFail($id);
            $comment->delete();
            flashSuccess(__('comment_deleted_successfully'));

            return back();
        } catch (\Exception $e) {

            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }
}
