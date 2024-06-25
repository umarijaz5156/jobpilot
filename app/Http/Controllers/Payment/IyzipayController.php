<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Traits\PaymentTrait;
use App\Models\Iyzipay;

class IyzipayController extends Controller
{

    public function pay(){

        try {

            $job_payment_type = session('job_payment_type') ?? 'package_job';

            if ($job_payment_type == 'per_job') {
                $price = session('job_total_amount') ?? '100';
            } else {
                $plan = session('plan');
                $price = $plan->price ?? '100';
            }

            $converted_amount = currencyConversion($price, 'TL'); // Convert to TL

            session(['order_payment' => [
                'payment_provider' => 'iyzipay',
                'amount' => $converted_amount,
                'currency_symbol' => '$',
                'usd_amount' => $converted_amount, // This is still in TL
            ]]);

            $auth = auth()->user();
            $options = Iyzipay::options();
            $request = new \Iyzipay\Request\CreatePayWithIyzicoInitializeRequest();
            $request->setLocale(\Iyzipay\Model\Locale::TR);
            $request->setConversationId("123456789");
            $request->setPrice("1");
            $request->setPaidPrice("$converted_amount");
            $request->setCurrency (\Iyzipay\Model\Currency::TL);
            $request->setBasketId("$plan->id");
            $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup:: PRODUCT);
            $request->setCallbackUrl("https://www.merchant.com/callback");
            $request->setEnabledInstallments (array(2, 3, 6, 9));

            $buyer = new \Iyzipay\Model\Buyer();
            $buyer->setId("$auth->id");
            $buyer->setName("$auth->name");
            $buyer->setSurname("$auth->username");
            $buyer->setGsmNumber("+905350000000");
            $buyer->setEmail("$auth->email");
            $buyer->setIdentityNumber("74300864791");
            $buyer->setLastLoginDate("");
            $buyer->setRegistrationDate("");
            $buyer->setRegistrationAddress ("$auth->company->address");
            $buyer->setIp("");
            $buyer->setCity("$auth->company->district");
            $buyer->setCountry ("$auth->company->country");
            $buyer->setZipCode("");

            $request->setBuyer ($buyer);
            $shippingAddress = new \Iyzipay\Model\Address(); $shippingAddress->setContactName("Jane Doe");
            $shippingAddress->setCity("$auth->company->district");
            $shippingAddress->setCountry("$auth->company->country");
            $shippingAddress->setAddress("$auth->company->address");
            $shippingAddress->setZipCode("");

            $request->setShippingAddress($shippingAddress);
            $billingAddress = new \Iyzipay\Model\Address(); 
            $billingAddress->setContactName("$auth->name");
            $billingAddress->setCity("$auth->company->district"); 
            $billingAddress->setCountry("$auth->company->country");
            $billingAddress->setAddress ("$auth->company->address");
            $billingAddress->setZipCode ("");
            $request->setBillingAddress ($billingAddress);

            $basketItems = array();
            $firstBasketItem = new \Iyzipay\Model\BasketItem();
            $firstBasketItem->setId("$plan->id");
            $firstBasketItem->setName("$plan->label");
            $firstBasketItem->setCategory1 ("$plan->label");
            $firstBasketItem->setCategory2 ("$plan->label");
            $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType:: PHYSICAL);
            $firstBasketItem->setPrice("0.3");

            $basketItems[0] = $firstBasketItem;
            $secondBasketItem = new \Iyzipay\Model\BasketItem();
            $secondBasketItem = new \Iyzipay\Model\BasketItem();
            $secondBasketItem->setId("BI102");
            $secondBasketItem->setName("Game code");
            $secondBasketItem->setCategory1("Game");
            $secondBasketItem->setCategory2("Online Game Items");
            $secondBasketItem->setItemType (\Iyzipay\Model\BasketItemType:: VIRTUAL);
            $secondBasketItem->setPrice("0.5");

            $basketItems [1] = $secondBasketItem;
            $thirdBasketItem = new \Iyzipay\Model\BasketItem();
            $thirdBasketItem->setId("BI103"); $thirdBasketItem->setName("Usb");
            $thirdBasketItem->setCategory1 ("Electronics");
            $thirdBasketItem->setCategory2 ("Usb / Cable");
            $thirdBasketItem->setItemType(\Iyzipay\Model\BasketItemType:: PHYSICAL);
            $thirdBasketItem->setPrice("0.2");
            $basketItems [2] = $thirdBasketItem;
            $request->setBasketItems ($basketItems);

            #make request
            $payWithIyzicoInitialize = \Iyzipay\Model\PayWithIyzicoInitialize::create($request, $options);
            return redirect()->away($payWithIyzicoInitialize->getPayWithIyzicoPageUrl());

        } catch (\Throwable $th) {

            session()->flash('error', __('payment_was_failed'));

            return back();
        }

        
    }
}
