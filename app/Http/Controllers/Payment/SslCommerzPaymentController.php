<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Traits\PaymentTrait;
use DGvai\SSLCommerz\SSLCommerz;
use Illuminate\Http\Request;

class SslCommerzPaymentController extends Controller
{
    use PaymentTrait;

    public function payment(Request $request)
    {
        $job_payment_type = session('job_payment_type') ?? 'package_job';
        if ($job_payment_type == 'per_job') {
            $price = session('job_total_amount') ?? '100';
        } else {
            $plan = session('plan');
            $price = $plan->price;
        }

        $converted_amount = usdAmount($price);
        $amount = currencyConversion($price, 'BDT', 1);

        session(['order_payment' => [
            'payment_provider' => 'sslcommerz',
            'amount' => $amount,
            'currency_symbol' => '৳',
            'usd_amount' => $converted_amount,
        ]]);

        $user = auth()->user();
        $trxID = uniqid();
        $sslc = new SSLCommerz();
        $sslc->amount($request->amount)
            ->trxid($trxID)
            ->product("{$plan->label} Plan Purchase: {$request->amount}")
            ->customer($user->name, $user->email);

        return $sslc->make_payment();
    }

    public function success(Request $request)
    {
        $validate = SSLCommerz::validate_payment($request);
        if ($validate) {
            $bankID = $request->bank_tran_id;
            $tran_id = $request->input('tran_id');

            session(['transaction_id' => $tran_id ?? null]);
            $this->orderPlacing();

            session()->flash('success', 'Payment Successfully');

            return redirect()->route('company.plan');
        }
    }

    public function failure()
    {
        session()->flash('error', __('payment_was_failed'));

        return back();
    }
}
