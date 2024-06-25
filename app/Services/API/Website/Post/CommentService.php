<?php

namespace App\Services\API\Website\Post;

use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Validator;
use Modules\Blog\Entities\PostComment;

class CommentService
{
    use ApiResponseHelpers;

    public function store($post, $request){
        $validator = Validator::make($request->all(), [
            'body' => 'required|max:2500|min:2'
        ]);
        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()],
                422
            );
        }

        $comment = new PostComment();
        $comment->author_id = auth('sanctum')->user()->id;
        $comment->post_id = $post->id;
        if ($request->has('parent_id')) {
            $comment->parent_id = $request->parent_id;
        }
        $comment->body = $request->body;
        $comment->save();

        return $this->respondWithSuccess([
            'data' => [
                'comment' => $comment->load(['user', 'replies']),
                'message' => __('comment_created_successfully'),
            ]
        ]);
    }
}
