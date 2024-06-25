<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Traits\PaymentTrait;
use Illuminate\Http\Request;

class FlutterwaveController extends Controller
{
    use PaymentTrait;

    public function success(Request $request)
    {
        if ($request->status == 'successful') {
            // Getting payment info from session
            $job_payment_type = session('job_payment_type') ?? 'package_job';
            if ($job_payment_type == 'per_job') {
                $price = session('job_total_amount') ?? '100';
            } else {
                $plan = session('plan');
                $price = $plan->price;
            }

            // Amount conversion
            $amount = currencyConversion($price, 'NGN', 1);
            $converted_amount = usdAmount($price);

            // Storing payment info in session
            session(['order_payment' => [
                'payment_provider' => 'flutterwave',
                'amount' => $amount,
                'currency_symbol' => '₦',
                'usd_amount' => $converted_amount,
            ]]);

            session(['transaction_id' => $request->transaction_id ?? null]);
            $this->orderPlacing();
        } else {
            session()->flash('error', 'Payment was failed');

            return back();
        }
    }
}
