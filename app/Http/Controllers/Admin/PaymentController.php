<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Modules\SetupGuide\Entities\SetupGuide;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only(['update', 'manualPaymentUpdate', 'manualPaymentDelete', 'manualPaymentStatus']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function autoPayment()
    {
        abort_if(! userCan('setting.view'), 403);

        return view('backend.settings.pages.payment');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            switch ($request->type) {
                case 'paypal':
                    $this->paypalUpdate($request);
                    break;
                case 'stripe':
                    $this->stripeUpdate($request);
                    break;
                case 'razorpay':
                    $this->razorpayUpdate($request);
                    break;
                case 'ssl_commerz':
                    $this->sslcommerzUpdate($request);
                    break;
                case 'paystack':
                    $this->paystackUpdate($request);
                    break;
                case 'flutterwave':
                    $this->flutterwaveUpdate($request);
                    break;
                case 'midtrans':
                    $this->midtransUpdate($request);
                    break;
                case 'mollie':
                    $this->mollieUpdate($request);
                    break;
                case 'instamojo':
                    $this->instamojoUpdate($request);
                    break;
                case 'iyzipay':
                    $this->iyzipayUpdate($request);
                    break;
            }

            SetupGuide::where('task_name', 'payment_setting')->update(['status' => 1]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the paypal configuration.
     */
    public function paypalUpdate(Request $request)
    {
        $request->validate([
            'paypal_client_id' => 'required',
            'paypal_client_secret' => 'required',
        ]);

        try {
            if ($request->paypal_live_mode) {
                checkSetConfig('templatecookie.paypal_live_client_id', $request->paypal_client_id);
                checkSetConfig('templatecookie.paypal_live_secret', $request->paypal_client_secret);
            } else {
                checkSetConfig('templatecookie.paypal_sandbox_client_id', $request->paypal_client_id);
                checkSetConfig('templatecookie.paypal_sandbox_secret', $request->paypal_client_secret);
            }

            setConfig('templatecookie.paypal_mode', $request->paypal_live_mode ? 'live' : 'sandbox');
            checkSetConfig('templatecookie.paypal_active', $request->paypal ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('paypal_setting_updated_successfully'));

            return redirect()->route('settings.payment')->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the stripe configuration.
     */
    public function stripeUpdate(Request $request)
    {
        $request->validate([
            'stripe_key' => 'required',
            'stripe_secret' => 'required',
        ]);

        try {
            checkSetConfig('templatecookie.stripe_key', $request->stripe_key);
            checkSetConfig('templatecookie.stripe_secret', $request->stripe_secret);
            checkSetConfig('templatecookie.stripe_active', $request->stripe ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('stripe_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the razorpay configuration.
     */
    public function razorpayUpdate(Request $request)
    {
        try {
            $request->validate([
                'razorpay_key' => 'required',
                'razorpay_secret' => 'required',
            ]);

            checkSetConfig('templatecookie.razorpay_key', $request->razorpay_key);
            checkSetConfig('templatecookie.razorpay_secret', $request->razorpay_secret);
            checkSetConfig('templatecookie.razorpay_active', $request->razorpay ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('razorpay_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the sslcommerz configuration.
     */
    public function sslcommerzUpdate(Request $request)
    {
        $request->validate([
            'store_id' => 'required',
            'store_password' => 'required',
        ]);

        try {
            checkSetConfig('sslcommerz.store.id', $request->store_id);
            checkSetConfig('sslcommerz.store.password', $request->store_password);
            checkSetConfig('sslcommerz.active', $request->ssl_commerz ? true : false);
            checkSetConfig('sslcommerz.sandbox', $request->ssl_live_mode ? false : true);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('ssl_commerz_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the paystack configuration.
     */
    public function paystackUpdate(Request $request)
    {
        $request->validate([
            'paystack_public_key' => 'required',
            'paystack_secret_key' => 'required',
            'merchant_email' => 'required',
        ]);

        try {
            checkSetConfig('templatecookie.paystack_key', $request->paystack_public_key);
            checkSetConfig('templatecookie.paystack_secret', $request->paystack_secret_key);
            checkSetConfig('templatecookie.paystack_merchant', $request->merchant_email);
            checkSetConfig('templatecookie.paystack_active', $request->paystack ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('paystack_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the flutterwave configuration.
     */
    public function flutterwaveUpdate(Request $request)
    {
        $request->validate([
            'flw_public_key' => 'required',
            'flw_secret_key' => 'required',
            'flw_secret_hash' => 'required',
        ]);

        try {
            checkSetConfig('templatecookie.flw_public_key', $request->flw_public_key);
            checkSetConfig('templatecookie.flw_secret', $request->flw_secret_key);
            checkSetConfig('templatecookie.flw_secret_hash', $request->flw_secret_hash);
            checkSetConfig('templatecookie.flw_active', $request->flutterwave ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('flutter_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the midtrans configuration.
     */
    public function midtransUpdate(Request $request)
    {
        $request->validate([
            'midtrans_merchat_id' => 'required',
            'midtrans_client_key' => 'required',
            'midtrans_server_key' => 'required',
        ]);

        try {
            checkSetConfig('templatecookie.midtrans_merchat_id', $request->midtrans_merchat_id);
            checkSetConfig('templatecookie.midtrans_client_key', $request->midtrans_client_key);
            checkSetConfig('templatecookie.midtrans_server_key', $request->midtrans_server_key);
            checkSetConfig('templatecookie.midtrans_active', $request->midtrans ? true : false);
            checkSetConfig('templatecookie.midtrans_live_mode', $request->midtrans_live_mode ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('midtrans_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the mollie configuration.
     */
    public function mollieUpdate(Request $request)
    {
        $request->validate([
            'mollie_key' => 'required',
        ]);

        try {
            checkSetConfig('templatecookie.mollie_key', $request->mollie_key);
            checkSetConfig('templatecookie.mollie_active', $request->mollie ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('mollie_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the instamojo configuration.
     */
    public function instamojoUpdate(Request $request)
    {
        $request->validate(
            [
                'im_key' => 'required',
                'im_secret' => 'required',
            ],
            [
                'im_key.required' => 'Instamojo Key is required',
                'im_secret.required' => 'Instamojo Secret is required',
            ],
        );

        try {
            checkSetConfig('templatecookie.im_key', $request->im_key);
            checkSetConfig('templatecookie.im_secret', $request->im_secret);
            checkSetConfig('templatecookie.im_active', $request->instamojo ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('instamojo_setting_updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
    public function iyzipayUpdate(Request $request)
    {
        $request->validate(
            [
                'Iyzipay_api_key' => 'required',
                'Iyzipay_api_secret' => 'required',
            ],
            [
                'Iyzipay_api_key.required' => 'Instamojo Secret is required',
                'Iyzipay_api_secret.required' => 'Instamojo Key is required',
            ],
        );

        try {
            checkSetConfig('templatecookie.Iyzipay_api_key', $request->Iyzipay_api_key);
            checkSetConfig('templatecookie.Iyzipay_api_secret', $request->Iyzipay_api_secret);
            checkSetConfig('templatecookie.Iyzipay_active', $request->Iyzipay_active ? true : false);

            sleep(3);
            Artisan::call('cache:clear');

            flashSuccess(__('updated_successfully'));

            return redirect()
                ->route('settings.payment')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function manualPayment()
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            $manual_payment_gateways = ManualPayment::all();

            return view('backend.settings.pages.payment-manual', compact('manual_payment_gateways'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function manualPaymentStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);

        try {
            ManualPayment::create([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
            ]);

            flashSuccess(__('manual_payment_created_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function manualPaymentEdit(ManualPayment $manual_payment)
    {
        try {
            $manual_payment_gateways = ManualPayment::all();

            return view('backend.settings.pages.payment-manual', compact('manual_payment_gateways', 'manual_payment'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function manualPaymentUpdate(Request $request, ManualPayment $manual_payment)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);

        try {
            $manual_payment->update([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
            ]);

            flashSuccess(__('manual_payment_updated_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function manualPaymentDelete(ManualPayment $manual_payment)
    {
        try {
            $manual_payment->delete();

            flashSuccess(__('manual_payment_deleted_successfully'));

            return redirect()->route('settings.payment.manual');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the manual payment status.
     */
    public function manualPaymentStatus(Request $request)
    {
        try {
            $manual_payment = ManualPayment::findOrFail($request->id);
            $manual_payment->update(['status' => $request->status]);

            return response()->json(['message' => __('payment_status_updated_successfully')]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
