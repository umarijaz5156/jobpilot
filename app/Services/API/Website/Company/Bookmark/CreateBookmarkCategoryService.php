<?php

namespace App\Services\API\Website\Company\Bookmark;

use F9Web\ApiResponseHelpers;
use App\Models\CompanyBookmarkCategory;
use Illuminate\Support\Facades\Validator;

class CreateBookmarkCategoryService
{
    use ApiResponseHelpers;

    public function execute($request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        $category = CompanyBookmarkCategory::create([
            'company_id' => auth('sanctum')->user()->company->id,
            'name' => $request->name
        ]);

        return $this->respondWithSuccess([
            'data' => [
                'category' => $category,
                'message' => __('category_created_successfully'),
            ]
        ]);
    }
}
