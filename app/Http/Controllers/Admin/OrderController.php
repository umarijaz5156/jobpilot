<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Earning;
use App\Models\ManualPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Plan\Entities\Plan;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            abort_if(! userCan('order.view'), 403);

            $companies = Company::with('user')
                ->select('id', 'user_id')
                ->get()
                ->makeHidden(['fullteam_size', 'banner_url', 'logo_url']);
            $plans = Plan::all();

            $order_query = Earning::query();

            if (request()->has('company') && request('company') != null) {
                $order_query->where('company_id', request('company'));
            }

            if (request()->has('plan') && request('plan') != null) {
                $order_query->where('plan_id', request('plan'));
            }

            if (request()->has('provider') && request('provider') != null) {
                $order_query->where('payment_provider', request('provider'));
            }

            if (request()->has('sort_by') && request('sort_by') != null) {
                if (request('sort_by') == 'latest') {
                    $order_query->latest();
                } else {
                    $order_query->oldest();
                }
            } else {
                $order_query->latest();
            }

            $orders = $order_query->with(['company.user', 'plan', 'manualPayment:id,name'])->paginate(10)->withQueryString();
            $current_currency = currentCurrency();

            return view('backend.order.index', compact('orders', 'companies', 'plans', 'current_currency'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function create()
    {
        try {
            $companies = User::where('role', 'company')->get();
            $plans = Plan::get(['id', 'label']);
            $manuals_payments = ManualPayment::get();

            return view('backend.order.create', compact('companies', 'plans', 'manuals_payments'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function store(Request $request)
    {
        try {
            $plan = Plan::find($request->plan_id);

            $earning = new Earning();
            $earning->order_id = uniqid();
            $earning->transaction_id = uniqid('tr_');
            $earning->payment_provider = $request->payment_provider;
            $earning->plan_id = $request->plan_id;
            $earning->amount = $plan->price;
            $earning->company_id = $request->company_id;
            $earning->manual_payment_id = $request->payment_provider == 'offline' ? $request->manual_payment_id : null;
            $earning->payment_status = $request->status;
            $earning->payment_type = 'subscription_based';
            $earning->currency_symbol = env('APP_CURRENCY_SYMBOL');
            $earning->usd_amount = usdAmount($plan->price);
            $earning->save();

            // Create benefit under this order
            $company = User::where('id', $request->company_id)->first();

            $company->userPlan()->create([
                'plan_id' => $plan->id,
                'job_limit' => $plan->job_limit,
                'featured_job_limit' => $plan->featured_job_limit,
                'highlight_job_limit' => $plan->highlight_job_limit,
                'candidate_cv_view_limit' => $plan->candidate_cv_view_limit,
                'candidate_cv_view_limitation' => $plan->candidate_cv_view_limitation,
            ]);
            // Create benefit under this order

            // return redirect()->route('order.index');

            flashSuccess('Order Added!');

            return redirect()->route('order.user.plan.update', $earning->id);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function show($id)
    {
        try {
            abort_if(! userCan('order.view'), 403);
            $order = Earning::with('plan', 'company', 'manualPayment:id,name')->findOrFail($id);

            return view('backend.order.show', compact('order'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * update the specified user plan data.
     *
     * @return Response
     */
    public function updateUserPlan(Earning $earning)
    {
        try {

            $earning->plans = Plan::latest()->get();
            $earning->user = User::where('id', $earning->company_id)->with('userPlan')->first();
            $earning->plan = Plan::where('id', $earning->plan_id)->first();

            return view('backend.order.user-plan', $earning);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * update the specified user plan data.
     *
     * @return RedirectResponse
     */
    public function UserPlanUpdate(Request $request, User $user)
    {
        try {

            $user->userPlan->update([
                'job_limit' => $request->job_limit,
                'plan_id' => $request->user_plan,
                'featured_job_limit' => $request->featured_job_limit,
                'highlight_job_limit' => $request->highlight_job_limit,
                'candidate_cv_view_limit' => $request->candidate_cv_view_limit,
            ]);

            $plan = Plan::where('id', $user->userPlan->plan_id)->first();

            $earning = Earning::where('company_id', $user->company->id)->latest()->first();
            if ($earning && $earning->plan_id !== $request->user_plan) {
                $earning->update([
                    'plan_id' => $request->user_plan,
                    'amount' => $plan->price,
                    'usd_amount' => usdAmount($plan->price),
                ]);
            }

            flashSuccess('User Plan Data Updated!');

            return redirect()->route('order.index', $plan);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
