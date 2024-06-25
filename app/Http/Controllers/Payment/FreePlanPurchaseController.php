<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\UserPlan;
use App\Services\Website\Job\PayPerJobService;
use Illuminate\Http\Request;
use Modules\Currency\Entities\Currency;
use Modules\Plan\Entities\Plan;

class FreePlanPurchaseController extends Controller
{
    /**
     * check user is authenticated
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * check user is authenticated
     *
     * @return void
     */
    public function purchaseFreePlan(Request $request)
    {
        $plan = Plan::findOrFail($request->plan);
        if ($plan->price == 0) {

            $user = auth()->user();
            $company = $user->company;

            // check free plan already buy
            $already_purchase = $this->checkAlreadyPurchase($plan, $company);
            if ($already_purchase) {
                flashWarning(__('plan_already_purchased'));

                return redirect()->back();
            }

            $this->createPlan($plan, $company);
            $this->makeTransaction($plan);

            flashSuccess(__('plan_successfully_purchased'));

            return redirect()->route('company.plan');
        } else {
            flashWarning(__('its_not_a_free_plan'));

            return back();
        }
    }

    public function purchaseZeroPricing($payperjob_code)
    {
        $code = session('payperjob_code');

        if ($code != $payperjob_code) {
            abort(404);
        }

        // Create transaction
        $jobCreated = (new PayPerJobService())->execute();
        $jobCreated ? $this->makeTransaction(null, 0, 'per_job_based') : null;

        $message = $jobCreated->status == 'active' ? __('job_has_been_created_and_published') : __('job_has_been_created_and_waiting_for_admin_approval');

        flashSuccess($message);

        return redirect()->route('website.job.details', $jobCreated->slug);
    }

    public function createPlan($plan, $company)
    {
        $user_plan = UserPlan::where('company_id', $company->id)->first();

        if ($user_plan) {

            $user_plan->update([
                'plan_id' => $plan->id,
                'job_limit' => $user_plan->job_limit + $plan->job_limit,
                'featured_job_limit' => $user_plan->featured_job_limit + $plan->featured_job_limit,
                'highlight_job_limit' => $user_plan->highlight_job_limit + $plan->highlight_job_limit,
                'candidate_cv_view_limit' => $user_plan->candidate_cv_view_limit + $plan->candidate_cv_view_limit,
                'candidate_cv_view_limitation' => $plan->candidate_cv_view_limitation,
            ]);
        } else {
            $company->userPlan()->create([
                'plan_id' => $plan->id,
                'job_limit' => $plan->job_limit,
                'featured_job_limit' => $plan->featured_job_limit,
                'highlight_job_limit' => $plan->highlight_job_limit,
                'candidate_cv_view_limit' => $plan->candidate_cv_view_limit,
                'candidate_cv_view_limitation' => $plan->candidate_cv_view_limitation,
            ]);
        }
    }

    public function makeTransaction($plan = null, $amount = 0, $payment_type = 'subscription_based')
    {
        if (isset($plan) && isset($plan->price)) {
            $amount = $plan->price;
        }

        $fromRate = Currency::whereCode(config('templatecookie.currency'))->first()->rate;
        $toRate = Currency::whereCode('USD')->first()->rate;
        $rate = $fromRate / $toRate;

        return Earning::create([
            'order_id' => uniqid(),
            'transaction_id' => uniqid('tr_'),
            'payment_provider' => 'offline',
            'plan_id' => $plan?->id ?? null,
            'company_id' => currentCompany()->id,
            'amount' => $amount,
            // 'currency_symbol' => config('jobpilot.currency_symbol'),
            'currency_symbol' => config('templatecookie.currency_symbol'),
            'usd_amount' => $amount * $rate,
            'payment_type' => $payment_type,
            'payment_status' => 'paid',
        ]);
    }

    public function checkAlreadyPurchase(object $plan, object $company): bool
    {
        $order = Earning::where('company_id', $company->id)->where('plan_id', $plan->id)->where('amount', 0)->first();
        if ($order) {
            return true;
        } else {
            return false;
        }
    }
}
