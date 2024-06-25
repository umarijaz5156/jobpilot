<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Traits\PaymentTrait;
use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Stripe;

class StripeController extends Controller
{
    use PaymentTrait;

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
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
        $converted_amount = currencyConversion($price);

        // Storing payment info in session
        session(['order_payment' => [
            'payment_provider' => 'stripe',
            'amount' => $converted_amount,
            'currency_symbol' => '$',
            'usd_amount' => $converted_amount,
        ]]);

        // Stripe payment process
        try {
            Stripe::setApiKey(config('templatecookie.stripe_secret'));

            if ($job_payment_type == 'per_job') {
                $description = 'Payment for job post in '.config('app.name');
            } else {
                $description = 'Payment for '.$plan->name.' plan purchase'.' in '.config('app.name');
            }

            $charge = Charge::create([
                'amount' => session('stripe_amount'),
                'currency' => 'USD',
                'source' => $request->stripeToken,
                'description' => $description,
            ]);

            session(['transaction_id' => $charge->id ?? null]);
            $this->orderPlacing();
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
