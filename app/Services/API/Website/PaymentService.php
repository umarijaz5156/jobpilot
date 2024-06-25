<?php

namespace App\Services\API\Website;

use App\Models\Admin;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Plan\Entities\Plan;

class PaymentService
{
    use ApiResponseHelpers;

    public function execute($label, $token)
    {
        $plan = Plan::where('label', $label)->firstOrFail();
        $token = PersonalAccessToken::findToken($token);
        $user = $token->tokenable;
        if (! auth('sanctum')->check()) {
            Auth::login($user);
        }
        return redirect(route('website.plan.details', $plan->label));
    }
}
