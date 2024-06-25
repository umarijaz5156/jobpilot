@extends('backend.settings.setting-layout')

@section('title')
    {{ __('payment_gateway_setting') }}
@endsection

@section('breadcrumbs')
    <div class="row mb-2 mt-4">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('settings') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('home') }}</a></li>
                <li class="breadcrumb-item">{{ __('settings') }}</li>
                <li class="breadcrumb-item active">{{ __('payment_gateway_setting') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('website-settings')
<div class="alert alert-warning mb-3">
    <h5>{{ __('want_to_receive_payments_setup_at_least_1_payment_gateway') }}</h5>
    <hr class="my-2">
    <p class="mb-0">{{ __('setup_smtp_to_send_all_applications_emails_such_as') }} <strong>{{ __('forget_password') }}</strong>, <strong>{{ __('user_verification') }}</strong>, <strong>{{ __('invoice') }}</strong>  & {{ __('many_more') }}. <a href="https://www.gmass.co/smtp-test" target="_blank"> {{ __('test_your_smtp_credentials_here') }} </a></p>
</div>
<div class="row">
    <div class="col-sm-6">
        {{-- paypal settings --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title line-height-36">
                        {{ __('paypal_settings') }}
                        <a target="_blank" href="https://developer.paypal.com/developer/accounts/"><small>({{ __('get_help') }})</small></a>
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('settings.payment.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" value="paypal" name="type">
                    <div class="form-group row">
                        <x-forms.label name="live_mode" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('templatecookie.paypal_mode') == 'live' ? 'checked' : '' }}
                                type="checkbox" name="paypal_live_mode" data-bootstrap-switch value="1">
                        </div>
                    </div>
                    @if (config('templatecookie.paypal_mode') == 'sandbox')
                        <div class="form-group row">
                            <x-forms.label name="client_id" class="col-sm-3" />
                            <div class="col-sm-9">
                                <input
                                    value="{{ config('templatecookie.paypal_sandbox_client_id') }}" name="paypal_client_id"
                                    type="text" class="form-control @error('paypal_client_id') is-invalid @enderror"
                                    autocomplete="off">
                                @error('paypal_client_id')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="client_secret" class="col-sm-3" />
                            <div class="col-sm-9">
                                <input
                                    value="{{ config('templatecookie.paypal_sandbox_secret') }}"
                                    name="paypal_client_secret" type="text"
                                    class="form-control @error('paypal_client_secret') is-invalid @enderror"
                                    autocomplete="off">
                                @error('paypal_client_secret')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="form-group row">
                            <x-forms.label name="client_id" class="col-sm-3" />
                            <div class="col-sm-9">
                                <input
                                    value="{{ config('templatecookie.paypal_live_client_id') }}" name="paypal_client_id"
                                    type="text" class="form-control @error('paypal_client_id') is-invalid @enderror"
                                    autocomplete="off">
                                @error('paypal_client_id')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="client_secret" class="col-sm-3" />
                            <div class="col-sm-9">
                                <input
                                    value="{{ config('templatecookie.paypal_live_secret') }}"
                                    name="paypal_client_secret" type="text"
                                    class="form-control @error('paypal_client_secret') is-invalid @enderror"
                                    autocomplete="off">
                                @error('paypal_client_secret')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                    @endif
                    <div class="form-group row">
                        <x-forms.label name="status" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('templatecookie.paypal_active') ? 'checked' : '' }} type="checkbox" name="paypal"
                            data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                            data-off-text="{{ __('off') }}">
                        </div>
                    </div>
                    @if (userCan('setting.update'))
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><i
                                        class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- SSL Commerz Setting --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title line-height-36">
                        {{ __('ssl_commerz_settings') }}
                        <a target="_blank" href="https://developer.sslcommerz.com/"><small>({{ __('get_help') }})</small></a>
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('settings.payment.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" value="ssl_commerz" name="type">
                    <div class="form-group row">
                        <x-forms.label name="live_mode" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('sslcommerz.sandbox') ? '' : 'checked'}} type="checkbox"
                                name="ssl_live_mode" data-bootstrap-switch value="1">
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="store_id" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('sslcommerz.store.id') }}" name="store_id" type="text"
                                class="form-control @error('store_id') is-invalid @enderror" autocomplete="off">
                            @error('store_id')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="store_password" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input
                                value="{{ config('sslcommerz.store.password') }}" name="store_password" type="text"
                                class="form-control @error('store_password') is-invalid @enderror"
                                autocomplete="off">
                            @error('store_password')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="status" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('sslcommerz.active') ? 'checked' : '' }} type="checkbox"
                            name="ssl_commerz" data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                            data-off-text="{{ __('off') }}">
                        </div>
                    </div>
                    @if (userCan('setting.update'))
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><i
                                        class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Flutterwave Setting --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title line-height-36">
                        {{ __('flutterwave_settings') }}
                        <a target="_blank" href="https://developer.flutterwave.com/docs/quickstart"><small>({{ __('get_help') }})</small></a>
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('settings.payment.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" value="flutterwave" name="type">

                    <div class="form-group row">
                        <x-forms.label name="public_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input
                                value="{{ config('templatecookie.flw_public_key') }}" name="flw_public_key"
                                type="text" class="form-control @error('flw_public_key') is-invalid @enderror"
                                autocomplete="off">
                            @error('flw_public_key')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="secret_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.flw_secret') }}" name="flw_secret_key" type="text"
                                class="form-control @error('flw_secret_key') is-invalid @enderror"
                                autocomplete="off">
                            @error('flw_secret_key')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="Secret Hash" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input  value="{{ config('templatecookie.flw_secret_hash') }}" name="flw_secret_hash"
                                type="text" class="form-control @error('flw_secret_hash') is-invalid @enderror"
                                autocomplete="off">
                            @error('flw_secret_hash')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="status" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('templatecookie.flw_active') ? 'checked' : '' }} type="checkbox"
                            name="flutterwave" data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                            data-off-text="{{ __('off') }}">
                        </div>
                    </div>
                    @if (userCan('setting.update'))
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><i
                                        class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Mollie Setting --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title line-height-36">
                        {{ __('mollie_setting') }}
                        <a target="_blank" href="https://docs.mollie.com"><small>({{ __('get_help') }})</small></a>
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('settings.payment.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" value="mollie" name="type">

                    <div class="form-group row">
                        <x-forms.label name="mollie_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.mollie_key') }}" name="mollie_key" type="text"
                                class="form-control @error('mollie_key') is-invalid @enderror" autocomplete="off">
                            @error('mollie_key')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="status" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('templatecookie.mollie_active') ? 'checked' : '' }} type="checkbox" name="mollie"
                            data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                            data-off-text="{{ __('off') }}">
                        </div>
                    </div>
                    @if (userCan('setting.update'))
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><i
                                        class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

         {{-- iyzipay Setting --}}
         <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title line-height-36">
                        {{ __('iyzipay_setting') }}
                        <a target="_blank" href="https://docs.iyzico.com"><small>({{ __('get_help') }})</small></a>
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('settings.payment.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" value="iyzipay" name="type">

                    <div class="form-group row">
                        <x-forms.label name="Iyzipay_api_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.Iyzipay_api_key') }}" name="Iyzipay_api_key" type="text"
                                class="form-control @error('Iyzipay_api_key') is-invalid @enderror" autocomplete="off">
                            @error('Iyzipay_api_key')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="Iyzipay_api_secret" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.Iyzipay_api_secret') }}" name="Iyzipay_api_secret" type="text"
                                class="form-control @error('Iyzipay_api_secret') is-invalid @enderror" autocomplete="off">
                            @error('Iyzipay_api_secret')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="status" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('templatecookie.Iyzipay_active') ? 'checked' : '' }} type="checkbox" name="Iyzipay_active"
                            data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                            data-off-text="{{ __('off') }}">
                        </div>
                    </div>
                    @if (userCan('setting.update'))
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><i
                                        class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        {{-- Stripe Setting --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title line-height-36">
                        {{ __('stripe_settings') }}
                        <a target="_blank" href="https://stripe.com/docs/development/dashboard/manage-api-keys"><small>({{ __('get_help') }})</small></a>
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('settings.payment.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" value="stripe" name="type">
                    <div class="form-group row">
                        <x-forms.label name="secret_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.stripe_secret') }}" name="stripe_secret" type="text"
                                   class="form-control @error('stripe_secret') is-invalid @enderror"
                                   autocomplete="off">
                            @error('stripe_secret')
                            <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>

                    </div>
                    <div class="form-group row">
                        <x-forms.label name="publisher_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.stripe_key') }}" name="stripe_key" type="text"
                                   class="form-control @error('stripe_key') is-invalid @enderror" autocomplete="off">
                            @error('stripe_key')
                            <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>

                    </div>
                    <div class="form-group row">
                        <x-forms.label name="status" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('templatecookie.stripe_active') ? 'checked' : '' }} type="checkbox" name="stripe"
                            data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                            data-off-text="{{ __('off') }}">
                        </div>
                    </div>
                    @if (userCan('setting.update'))
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><i
                                        class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Razorpay Setting --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title line-height-36">
                        {{ __('razorpay_settings') }}
                        <a target="_blank" href="https://razorpay.com/docs/api/"><small>({{ __('get_help') }})</small></a>
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('settings.payment.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" value="razorpay" name="type">
                    <div class="form-group row">
                        <x-forms.label name="secret_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.razorpay_key') }}" name="razorpay_key" type="text"
                                class="form-control @error('razorpay_key') is-invalid @enderror"
                                autocomplete="off">
                            @error('razorpay_key')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="publisher_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.razorpay_secret') }}" name="razorpay_secret"
                                type="text" class="form-control @error('razorpay_secret') is-invalid @enderror"
                                autocomplete="off">
                            @error('razorpay_secret')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="status" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('templatecookie.razorpay_active') ? 'checked' : '' }} type="checkbox"
                            name="razorpay" data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                            data-off-text="{{ __('off') }}">
                        </div>
                    </div>
                    @if (userCan('setting.update'))
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><i
                                        class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Paystack Setting --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title line-height-36">
                        {{ __('paystack_settings') }}
                        <a target="_blank" href="https://paystack.com/docs/api"><small>({{ __('get_help') }})</small></a>
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('settings.payment.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" value="paystack" name="type">
                    <div class="form-group row">
                        <x-forms.label name="client_id" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.paystack_key') }}" name="paystack_public_key"
                                type="text"
                                class="form-control @error('paystack_public_key') is-invalid @enderror"
                                autocomplete="off">
                            @error('paystack_public_key')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="client_secret" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.paystack_secret') }}" name="paystack_secret_key"
                                type="text"
                                class="form-control @error('paystack_secret_key') is-invalid @enderror"
                                autocomplete="off">
                            @error('paystack_secret_key')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="merchant_email" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.paystack_merchant') }}" name="merchant_email"
                                type="text" class="form-control @error('merchant_email') is-invalid @enderror"
                                autocomplete="off">
                            @error('merchant_email')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="status" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('templatecookie.paystack_active') ? 'checked' : '' }} type="checkbox"
                            name="paystack" data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                            data-off-text="{{ __('off') }}">
                        </div>
                    </div>
                    @if (userCan('setting.update'))
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><i
                                        class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Midtrans Setting --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title line-height-36">
                        {{ __('midtrans_setting') }}
                        <a target="_blank" href="https://api-docs.midtrans.com/"><small>({{ __('get_help') }})</small></a>
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('settings.payment.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" value="midtrans" name="type">
                    <div class="form-group row">
                        <x-forms.label name="live_mode" class="col-sm-3" />
                        <div class="col-sm-9">
                            {{-- <input {{ config('templatecookie.midtrans_live_mode') ? 'checked' : '' }}
                                type="checkbox" name="midtrans_live_mode" data-bootstrap-switch value="1"> --}}
                            <input {{ config('templatecookie.midtrans_live_mode') ? 'checked' : '' }}
                                type="checkbox" name="midtrans_live_mode" data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                                data-off-text="{{ __('off') }}" data-on-text="{{ __('on') }}"
                                data-off-text="{{ __('off') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="merchant_id" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.midtrans_merchat_id') }}" name="midtrans_merchat_id"
                                type="text"
                                class="form-control @error('midtrans_merchat_id') is-invalid @enderror"
                                autocomplete="off">
                            @error('midtrans_merchat_id')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="client_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.midtrans_client_key') }}" name="midtrans_client_key"
                                type="text"
                                class="form-control @error('midtrans_client_key') is-invalid @enderror"
                                autocomplete="off">
                            @error('midtrans_client_key')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="server_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.midtrans_server_key') }}" name="midtrans_server_key"
                                type="text"
                                class="form-control @error('midtrans_server_key') is-invalid @enderror"
                                autocomplete="off">
                            @error('midtrans_server_key')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="status" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('templatecookie.midtrans_active') ? 'checked' : '' }} type="checkbox"
                            name="midtrans" data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                            data-off-text="{{ __('off') }}" data-on-text="{{ __('on') }}"
                            data-off-text="{{ __('off') }}">
                        </div>
                    </div>
                    @if (userCan('setting.update'))
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><i
                                        class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Instamojo Setting --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title" style="line-height: 36px;">
                        {{ __('instamojo_setting') }}
                        <a target="_blank" href="https://docs.instamojo.com"><small>({{ __('get_help') }})</small></a>
                    </h3>
                </div>
            </div>

            <div class="card-body">
                <form class="form-horizontal" action="{{ route('settings.payment.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" value="instamojo" name="type">

                    <div class="form-group row">
                        <x-forms.label name="instamojo_key" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.im_key') }}" name="im_key" type="text"
                                class="form-control @error('im_key') is-invalid @enderror" autocomplete="off">
                            @error('im_key')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="instamojo_secret" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input value="{{ config('templatecookie.im_secret') }}" name="im_secret" type="text"
                                class="form-control @error('im_secret') is-invalid @enderror" autocomplete="off">
                            @error('im_secret')
                                <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-forms.label name="status" class="col-sm-3" />
                        <div class="col-sm-9">
                            <input {{ config('templatecookie.im_active') ? 'checked' : '' }} type="checkbox"
                            name="instamojo" data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                            data-off-color="default" data-off-text="{{ __('off') }}"
                            class="payment-status">
                        </div>
                    </div>
                    @if (userCan('setting.update'))
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><i
                                        class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        })
    </script>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
@endsection
