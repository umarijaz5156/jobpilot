<?php

namespace App\Services\API\Website\Company\Bookmark;

use F9Web\ApiResponseHelpers;

class FetchCandidateBookmarkService
{
    use ApiResponseHelpers;

    public function execute($request){
        $query = auth('sanctum')->user()->company->bookmarkCandidates();

        if ($request->category != 'all' && $request->has('category') && $request->category != null) {
            $query->wherePivot('category_id', $request->category);
        }

        $bookmarks = $query->with('profession')->paginate(12)->withQueryString();

        return $this->respondWithSuccess([
            'data' => $bookmarks
        ]);
    }
}
