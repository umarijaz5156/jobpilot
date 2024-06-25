<?php

namespace App\Services\API\Website\Company\ApplicationGroup;

use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Validator;

class UpdateApplicationGroupService
{
    use ApiResponseHelpers;

    public function execute($request,$group){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()], 422
            );
        }
        $group->update([
            'name' => $request->name,
        ]);

        return $this->respondWithSuccess([
            'data' => [
                'group' => $group,
                'message' => __('group_updated_successfully'),
            ],
        ]);
    }
}
