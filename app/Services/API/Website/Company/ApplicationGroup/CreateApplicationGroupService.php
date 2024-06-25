<?php

namespace App\Services\API\Website\Company\ApplicationGroup;

use F9Web\ApiResponseHelpers;
use App\Models\ApplicationGroup;
use Illuminate\Support\Facades\Validator;

class CreateApplicationGroupService
{
    use ApiResponseHelpers;

    public function execute($request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()], 422
            );
        }

        $group = ApplicationGroup::create([
            'company_id' => auth('sanctum')->user()->company->id,
            'name' => $request->name,
        ]);

        return $this->respondWithSuccess([
            'data' => [
                'group' => $group,
                'message' => __('group_created_successfully'),
            ],
        ]);
    }
}
