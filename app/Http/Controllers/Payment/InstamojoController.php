<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Traits\PaymentTrait;
use Illuminate\Http\Request;

class InstamojoController extends Controller
{
    use PaymentTrait;

    public function pay()
    {
        try {
            // Getting payment info from session
            $job_payment_type = session('job_payment_type') ?? 'package_job';
            if ($job_payment_type == 'per_job') {
                $price = session('job_total_amount') ?? '100';
            } else {
                $plan = session('plan');
                $price = $plan->price;
            }

            // Amount conversion
            $amount = currencyConversion($price, 'INR', 1);
            $converted_amount = usdAmount($price);

            // Storing payment info in session
            session(['order_payment' => [
                'payment_provider' => 'instamojo',
                'amount' => $amount,
                'currency_symbol' => '₹',
                'usd_amount' => $converted_amount,
            ]]);

            // Sending payment request to instamojo
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payment-requests/');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'X-Api-Key:'.config('templatecookie.im_key'),
                    'X-Auth-Token:'.config('templatecookie.im_secret'),
                ]
            );
            $payload = [
                'purpose' => 'Payment for the job plan you bought',
                'amount' => $amount,
                'phone' => '9888888888',
                'buyer_name' => authUser()->name,
                'redirect_url' => route('instamojo.success'),
                'send_email' => true,
                'webhook' => 'http://www.example.com/webhook/',
                'send_sms' => true,
                'email' => authUser()->email,
                'allow_repeated_payments' => false,
            ];
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response);

            return redirect($response->payment_request->longurl);
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());

            return back();
        }
    }

    public function success(Request $request)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payments/'.$request->get('payment_id'));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'X-Api-Key:'.config('templatecookie.im_key'),
                'X-Auth-Token:'.config('templatecookie.im_secret'),
            ]
        );

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return back()->with('error', __('payment_was_failed'));
        } else {
            $data = json_decode($response);
        }

        if ($data->success == true) {
            if ($data->payment->status == 'Credit') {
                session(['transaction_id' => $request->get('payment_id') ?? null]);

                // Here Your Database Insert Login
                $this->orderPlacing();
            } else {
                session()->flash('error', __('payment_was_failed'));

                return redirect()->route('payment');
            }
        }
    }
}
