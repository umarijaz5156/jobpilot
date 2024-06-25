<?php

namespace App\Services\API\Website\Company\Bookmark;

use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Validator;

class UpdateBookmarkCategoryService
{
    use ApiResponseHelpers;

    public function execute($request, $category){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        $category->update(['name' => $request->name]);

        return $this->respondWithSuccess([
            'data' => [
                'category' => $category,
                'message' => __('category_updated_successfully'),
            ]
        ]);
    }
}
