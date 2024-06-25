<?php

namespace App\Services\API\Website\Post;

use F9Web\ApiResponseHelpers;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostCategory;

class FetchPostService
{
    use ApiResponseHelpers;

    public function execute($request){
        $key = $request->search;
        $posts = Post::query()->published()->withCount('comments');

        if ($key) {
            $posts->where('title', 'Like', '%' . $key . '%');
        }

        if ($request->category) {
            // $category_ids = PostCategory::whereIn('id', $request->category)->get()->pluck('id');
            $posts = $posts->whereIn('category_id', explode(',', $request->category))->latest()->paginate(8)->withQueryString();
        } else {
            $posts = $posts->latest()->paginate(8)->withQueryString();
        }

        $recent_posts = Post::published()->withCount('comments')->latest()->take(5)->get();
        $categories = PostCategory::latest()->get(['id','name']);

        return $this->respondWithSuccess([
            'data' => [
                'posts' => $posts,
                'recent_posts' => $recent_posts,
                'categories' => $categories
            ]
        ]);

    }
}
