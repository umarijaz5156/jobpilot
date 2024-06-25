@extends('backend.layouts.app')
@section('title')
    {{ __('user_create') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ __('order_create') }}</h3>
                        <a href="{{ route('order.index') }}"
                            class="btn bg-primary float-right d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-left mr-1"></i>
                            {{ __('back') }}
                        </a>
                    </div>
                    <div class="row pt-3 pb-4">
                        <div class="col-md-6 offset-md-3">
                            <form class="form-horizontal" action="{{ route('order.store') }}" method="POST">
                                @csrf

                                <div class="form-group row">
                                    <x-forms.label name="{{ __('comapanies_name') }}" class="col-sm-3" />
                                    <div class="col-sm-9">
                                        <select name="company_id"
                                            class="w-100-p select2bs4 @error('name') is-invalid @enderror"
                                            data-placeholder="{{ __('select_one') }}">
                                            @foreach ($companies as $user)
                                                <option value="{{ $user->company->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('name')
                                            <span class="invalid-feedback"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <x-forms.label name="{{ __('plans') }}" class="col-sm-3" />
                                    <div class="col-sm-9">
                                        <select name="plan_id"
                                            class="w-100-p select2bs4 @error('plan_id') is-invalid @enderror"
                                            data-placeholder="{{ __('select_one') }}">
                                            @foreach ($plans as $plan)
                                                <option value="{{ $plan->id }}">{{ $plan->label }}</option>
                                            @endforeach
                                        </select>
                                        @error('plan_id')
                                            <span class="invalid-feedback"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <x-forms.label name="{{ __('status') }}" class="col-sm-3" />
                                    <div class="col-sm-9">
                                        <select name="status"
                                            class="w-100-p select2bs4 @error('status') is-invalid @enderror"
                                            data-placeholder="{{ __('select_one') }}">
                                                <option value="paid">Paid</option>
                                                <option value="unpaid">Unpaid</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <x-forms.label name="{{ __('payment_provider') }}" class="col-sm-3" />
                                    <div class="col-sm-9">
                                        <select name="payment_provider" id="payment_provider"
                                            class="w-100-p select2bs4 @error('plan') is-invalid @enderror"
                                            data-placeholder="{{ __('select_one') }}">
                                            <option value="flutterwave">Flutterwave</option>
                                            <option value="mollie">Mollie</option>
                                            <option value="midtrans">Midtrans</option>
                                            <option value="paypal">Paypal</option>
                                            <option value="paystack">Paystack</option>
                                            <option value="razorpay">Razorpay</option>
                                            <option value="sslcommerz">Sslcommerz</option>
                                            <option value="stripe">Stripe</option>
                                            <option value="instamojo">Instamojo</option>
                                            <option value="offline">Offline</option>
                                        </select>
                                        @error('manual_payment_id')
                                            <span class="invalid-feedback"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row" id="payment_type">
                                    <x-forms.label name="{{ __('payment_type') }}" class="col-sm-3" />
                                    <div class="col-sm-9">
                                        <select name="manual_payment_id"
                                            class="w-100-p select2bs4 @error('plan') is-invalid @enderror"
                                            data-placeholder="{{ __('select_one') }}">
                                            @foreach ($manuals_payments as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('manual_payment_id')
                                            <span class="invalid-feedback"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="offset-sm-3 col-sm-9">
                                        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i>
                                            {{ __('save') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .select2-results__option[aria-selected=true] {
            display: none;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            color: #fff;
            border: 1px solid #fff;
            background: #007bff;
            border-radius: 30px;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
        }
    </style>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#payment_type').hide();

            $('#payment_provider').change(function() {
                if ($(this).val() === 'offline') {
                    $('#payment_type').show();
                } else {
                    $('#payment_type').hide();
                }
            });
        });
    </script>
@endsection
