<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Traits\PaymentTrait;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    use PaymentTrait;

    /**
     * process transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function processTransaction()
    {
        // Getting payment info from session
        $job_payment_type = session('job_payment_type') ?? 'package_job';

        if ($job_payment_type == 'per_job') {
            $price = session('job_total_amount') ?? '100';
        } else {
            $plan = session('plan');
            $price = $plan->price ?? '100';
        }

        // Amount conversion
        $converted_amount = currencyConversion($price);

        // Storing payment info in session
        session(['order_payment' => [
            'payment_provider' => 'paypal',
            'amount' => $converted_amount,
            'currency_symbol' => '$',
            'usd_amount' => $converted_amount,
        ]]);
        // PayPal payment process
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('paypal.successTransaction'),
                'cancel_url' => route('paypal.cancelTransaction'),
            ],
            'purchase_units' => [
                0 => [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $converted_amount,
                    ],
                ],
            ],
        ]);

        if (isset($response['id']) && $response['id'] != null) {

            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            session()->flash('error', 'Unable to find the approval link. Please try again later.');

            return back();
        } else {
            session()->flash('error', 'Failed to initiate the PayPal transaction. Please try again.');

            return back();
        }
    }

    /**
     * success transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function successTransaction(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            session(['transaction_id' => $response['id'] ?? null]);

            $this->orderPlacing();
        } else {
            session()->flash('error', __('payment_was_failed'));

            return back();
        }
    }

    /**
     * cancel transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelTransaction(Request $request)
    {
        session()->flash('error', __('payment_was_failed'));

        return back();
    }
}
