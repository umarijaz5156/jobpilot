<?php

namespace App\Services\API\Website\Post;

use F9Web\ApiResponseHelpers;
use Modules\Blog\Entities\Post;

class FetchPostDetailsService
{
    use ApiResponseHelpers;

    public function execute($slug){
        $post = Post::published()->whereSlug($slug)
            ->with([
                'author:id,name',
                'comments.replies.user:id,name,image',
                'comments.user:id,name,image'
            ])
            ->first();

        if(!$post){
            return $this->respondNotFound(__('post_not_found'));
        }

        return $this->respondWithSuccess([
            'data' => $post
        ]);
    }
}
