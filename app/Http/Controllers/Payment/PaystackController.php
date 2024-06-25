<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Traits\PaymentTrait;
use Illuminate\Http\Request;

class PaystackController extends Controller
{
    use PaymentTrait;

    /**
     * Redirect the User to Paystack Payment Page
     *
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {
        // Getting payment info from session
        $job_payment_type = session('job_payment_type') ?? 'package_job';

        if ($job_payment_type == 'per_job') {
            $price = session('job_total_amount') ?? '100';
        } else {
            $plan = session('plan');
            $price = $plan->price;
        }

        // Amount conversion
        $converted_amount = usdAmount($price);
        $amount = currencyConversion($price, 'ZAR');

        // Storing payment info in session
        session(['order_payment' => [
            'payment_provider' => 'paystack',
            'amount' => $amount,
            'currency_symbol' => '₦',
            'usd_amount' => $converted_amount,
        ]]);

        // Paystack payment process
        $secret_key = config('templatecookie.paystack_key');
        $curl = curl_init();
        $callback_url = route('paystack.success'); // url to go to after payment

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.paystack.co/transaction/initialize',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'amount' => $amount * 100,
                'email' => authUser()->email,
                'callback_url' => $callback_url,
            ]),
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer '.$secret_key, //replace this with your own test key
                'content-type: application/json',
                'cache-control: no-cache',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        if ($err) {
            return redirect()->back()->with('error', $err);
        }

        $tranx = json_decode($response, true);
        session(['paystack_request' => $request->all()]);
        if (! $tranx['status']) {
            session(['transaction_id' => $request['reference'] ?? null]);

            return redirect()->back()->with('error', $tranx['message']);
        }

        return redirect($tranx['data']['authorization_url']);
    }

    /**
     * Obtain Paystack payment information
     *
     * @return void
     */
    public function successPaystack(Request $request)
    {
        if ($request['trxref'] === $request['reference']) {
            $this->orderPlacing();
        }

        session()->flash('error', __('payment_was_failed'));

        return back();
    }
}
