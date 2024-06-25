<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Traits\PaymentTrait;
use Mollie\Laravel\Facades\Mollie;

class MollieController extends Controller
{
    use PaymentTrait;

    /**
     * Redirect the user to the Payment Gateway.
     *
     * @return Response
     */
    public function preparePayment()
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
        $amount = currencyConversion($price, 'EUR', 1);

        session(['order_payment' => [
            'payment_provider' => 'mollie',
            'amount' => $amount,
            'currency_symbol' => '€',
            'usd_amount' => $converted_amount,
        ]]);

        // Storing payment info in session
        $amount = $price;
        $decimal_amount = currencyConversion(round($price, 2));

        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => 'EUR', // Type of currency you want to send
                'value' => $decimal_amount, // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'description' => 'Payment By '.authUser()->name,
            'redirectUrl' => route('mollie.success'), // after the payment completion where you to redirect
        ]);

        $payment = Mollie::api()->payments()->get($payment->id);

        session(['transaction_id' => $payment->id ?? null]);

        // redirect customer to Mollie checkout page
        return redirect($payment->getCheckoutUrl(), 303);
    }

    /**
     * Page redirection after the successfull payment
     *
     * @return Response
     */
    public function paymentSuccess()
    {
        $this->orderPlacing();
    }
}
