<?php

use App\Http\Controllers\Payment\FlutterwaveController;
use App\Http\Controllers\Payment\FreePlanPurchaseController;
use App\Http\Controllers\Payment\InstamojoController;
use App\Http\Controllers\Payment\IyzipayController;
use App\Http\Controllers\Payment\ManualPaymentController;
use App\Http\Controllers\Payment\MidtransController;
use App\Http\Controllers\Payment\MollieController;
use App\Http\Controllers\Payment\PayPalController;
use App\Http\Controllers\Payment\PaystackController;
use App\Http\Controllers\Payment\RazorpayController;
use App\Http\Controllers\Payment\SslCommerzPaymentController;
use App\Http\Controllers\Payment\StripeController;
use Illuminate\Support\Facades\Route;

//Paypal
Route::controller(PayPalController::class)->group(function () {
    Route::post('paypal/payment', 'processTransaction')->name('paypal.post');
    Route::get('success-transaction', 'successTransaction')->name('paypal.successTransaction');
    Route::get('cancel-transaction', 'cancelTransaction')->name('paypal.cancelTransaction');
});

// Paystack
Route::controller(PaystackController::class)->group(function () {
    Route::post('paystack/payment', 'redirectToGateway')->name('paystack.post');
    Route::get('/paystack/success', 'successPaystack')->name('paystack.success');
});

// SSLCOMMERZ
Route::controller(SslCommerzPaymentController::class)->name('sslc.')->prefix('payment')->group(function () {
    Route::post('sslcommerz/payment', 'payment')->name('payment');
    Route::post('sslcommerz/success', 'success')->name('success');
    Route::post('sslcommerz/failure', 'failure')->name('failure');
    Route::post('sslcommerz/cancel', 'cancel')->name('cancel');
    Route::post('sslcommerz/ipn', 'ipn')->name('ipn');
});

// Flutterwave
Route::controller(FlutterwaveController::class)->group(function () {
    Route::get('/flutterwave/success', 'success')->name('flutterwave.success');
});

// Stripe
Route::post('stripe', [StripeController::class, 'stripePost'])->name('stripe.post');

// Razorpay
Route::post('payment', [RazorpayController::class, 'payment'])->name('razorpay.post');

// Paystack
Route::post('paystack/payment', [PaystackController::class, 'redirectToGateway'])->name('paystack.post');
Route::get('/paystack/success', [PaystackController::class, 'successPaystack'])->name('paystack.success');

// Instamojo
Route::controller(InstamojoController::class)->group(function () {
    Route::post('/instamojo/pay', 'pay')->name('instamojo.pay');
    Route::get('/instamojo/success', 'success')->name('instamojo.success');
});

// Mollie
Route::post('mollie-paymnet', [MollieController::class, 'preparePayment'])->name('mollie.payment');
Route::get('mollie-success', [MollieController::class, 'paymentSuccess'])->name('mollie.success');

// Midtrans
Route::post('/midtrans/success', [MidtransController::class, 'success'])->name('midtrans.success');

// Manual Payment
Route::controller(ManualPaymentController::class)->group(function () {
    Route::post('/manual/payment', 'paymentPlace')->name('manual.payment');
    Route::get('/manual/payment/{order}/mark-paid', 'markPaid')->name('manual.payment.mark.paid');
});

// Free Plan Purchase
Route::controller(FreePlanPurchaseController::class)->group(function () {
    Route::post('/free/plan/purchase', 'purchaseFreePlan')->name('purchase.free.plan');
    Route::get('/zero/pricing/job/{code}', 'purchaseZeroPricing')->name('purchase.zero.pricing.job');
});

//Iyzipay

Route::post('iyzipay/payment', [IyzipayController::class, 'pay'])->name('iyzipay.post');
