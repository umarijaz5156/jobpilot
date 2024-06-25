<?php

namespace App\Http\Middleware\Api;

use App\Models\Earning;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasPlanApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // check previous url


        if (auth('sanctum')->check() && auth('sanctum')->user() && auth('sanctum')->user()->role == 'company') {

            $user  =  Auth::user();
            $company = $user->company;
            $plan = $company->userPlan;
            if (!$plan) {

                // check company have any pending order
                $check_pending_plan = $this->checkPendingPlan($company);
                if ($check_pending_plan) {
                    abort(401, __('your_purchased_plan_order_has_pending._please_wait_until_the_order_is_approved'));
                }else {
                    abort(401, __('you_dont_have_a_chosen_plan_please_choose_a_plan_to_continue'));
                }

            } else {
                return $next($request);
            }
        }
        return $next($request);
    }

    public function checkPendingPlan(Object $company): bool
    {
        $earnings = Earning::where('company_id', $company->id)->where('payment_status', 'unpaid')->first();
        if ($earnings) {
            return true;
        } else {
            return false;
        }
    }
}
