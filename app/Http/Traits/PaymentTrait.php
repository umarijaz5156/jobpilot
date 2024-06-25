<?php

namespace App\Http\Traits;

use App\Models\Admin;
use App\Models\Earning;
use App\Models\UserPlan;
use App\Notifications\Admin\NewPlanPurchaseNotification;
use App\Services\Website\Job\PayPerJobService;
use Illuminate\Support\Facades\Notification;

trait PaymentTrait
{
    use JobAble;

    public function orderPlacing($redirect = true)
    {
        $plan = session('plan');
        $order_amount = session('order_payment');
        $transaction_id = session('transaction_id') ?? uniqid('tr_');
        $job_payment_type = session('job_payment_type') ?? 'package_job';

        $order = Earning::create([
            'order_id' => rand(1000, 999999999),
            'transaction_id' => $transaction_id,
            'plan_id' => $plan->id ?? null,
            'company_id' => currentCompany()->id,
            'payment_provider' => $order_amount['payment_provider'],
            'amount' => $order_amount['amount'],
            'currency_symbol' => $order_amount['currency_symbol'],
            'usd_amount' => $order_amount['usd_amount'],
            'payment_status' => 'paid',
            'payment_type' => $job_payment_type == 'per_job' ? 'per_job_based' : 'subscription_based',
        ]);

        if ($job_payment_type == 'package_job') {
            info('condition true');

            $user_plan = UserPlan::companyData()->first();
            $company = currentCompany();

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

            if (checkMailConfig()) {
                // make notification to admins for approved
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    Notification::send($admin, new NewPlanPurchaseNotification($admin, $order, $plan, authUser()));
                }
            }

            storePlanInformation();

            info('every thing is ok');

        } else {
            info('condition false');

            return $this->storeJobData();
        }

        $this->forgetSessions();

        if ($redirect) {
            session()->flash('success', __('plan_purchased_successfully'));

            return redirect()->route('company.plan')->send();
        }

        info('redirecting to success');

        return true;
    }

    private function forgetSessions()
    {
        session()->forget('plan');
        session()->forget('order_payment');
        session()->forget('transaction_id');
        session()->forget('stripe_amount');
        session()->forget('razor_amount');
        session()->forget('job_payment_type');
    }

    private function storeJobData()
    {
        $jobCreated = (new PayPerJobService())->execute();

        $this->forgetSessions();

        $message = $jobCreated->status == 'active' ? __('job_has_been_created_and_published') : __('job_has_been_created_and_waiting_for_admin_approval');

        session()->flash('success', $message);

        return redirect()->route('website.job.details', $jobCreated->slug)->send();
    }
}
