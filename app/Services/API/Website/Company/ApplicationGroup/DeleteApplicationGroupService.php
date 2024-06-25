<?php

namespace App\Services\API\Website\Company\ApplicationGroup;

use F9Web\ApiResponseHelpers;
use App\Models\ApplicationGroup;

class DeleteApplicationGroupService
{
    use ApiResponseHelpers;

    public function execute($group){
        if ($group->is_deleteable) {
            $new_group = ApplicationGroup::where('company_id', auth('sanctum')->user()->company->id)
                ->where('id', '!=', $group->id)
                ->where('is_deleteable', false)
                ->first();

            if ($new_group) {
                $group->applications()->update([
                    'application_group_id' => $new_group->id
                ]);
            }

            $group->delete();

            return $this->respondOk(__('group_deleted_successfully'));
        }

        return $this->respondError(__('group_is_not_deletable'));
    }
}
