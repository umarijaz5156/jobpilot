<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Traits\PaymentTrait;

class MidtransController extends Controller
{
    use PaymentTrait;

    public function success()
    {
        // plan data store
        $this->orderPlacing(false);

        // forget midtrans session
        session()->forget('payment_details');
        session()->flash('success', 'Payment Successfully');

        // redirect url pass
        return response()->json([
            'redirect_url' => route('company.plan'),
        ]);
    }
}
