@extends('backend.layouts.app')

@section('title')
    {{ __('orders') }}
@endsection

@section('content')
    <div class="">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title line-height-36">{{ __('orders') }}</h3>
                            <a href="{{ route('order.create') }}" class="btn bg-primary float-right d-flex align-items-center justify-content-center">
                                <i class="fas fa-plus"></i>
                                {{ __('create') }}
                            </a>
                            @if (request('company') || request('provider') || request('plan') || request('sort_by'))
                                <div>
                                    <a href="{{ route('order.index') }}" class="btn bg-danger"><i class="fas fa-times"></i>
                                        &nbsp;{{ __('clear') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <form id="filterForm" action="{{ route('order.index') }}" method="GET">
                        <div class="card-body border-bottom row">
                            <div class="col-xl-3 col-md-6 col-12">
                                <label>{{ __('companies') }}</label>
                                <select name="company" class="form-control select2bs4">
                                    <option {{ request('company') ? '' : 'selected' }} value="" selected>
                                        {{ __('all') }}
                                    </option>
                                    @foreach ($companies as $company)
                                        <option {{ request('company') == $company->id ? 'selected' : '' }}
                                            value="{{ $company->id }}">{{ $company->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 col-md-6 col-12">
                                <label>{{ __('payment_provider') }}</label>
                                <select name="provider" id="filter" class="form-control select2bs4">
                                    <option {{ request('provider') ? '' : 'selected' }} value="" selected>
                                        {{ __('all') }}
                                    </option>
                                    <option {{ request('provider') == 'paypal' ? 'selected' : '' }} value="paypal">
                                        {{ __('paypal') }}
                                    </option>
                                    <option {{ request('provider') == 'stripe' ? 'selected' : '' }} value="stripe">
                                        {{ __('stripe') }}
                                    </option>
                                    <option {{ request('provider') == 'razorpay' ? 'selected' : '' }} value="razorpay">
                                        {{ __('razorpay') }}
                                    </option>
                                    <option {{ request('provider') == 'paystack' ? 'selected' : '' }} value="paystack">
                                        {{ __('paystack') }}
                                    </option>
                                    <option {{ request('provider') == 'sslcommerz' ? 'selected' : '' }} value="sslcommerz">
                                        {{ __('sslcommerz') }}
                                    </option>
                                    <option {{ request('provider') == 'instamojo' ? 'selected' : '' }} value="instamojo">
                                        {{ __('instamojo') }}
                                    </option>
                                    <option {{ request('provider') == 'flutterwave' ? 'selected' : '' }}
                                        value="flutterwave">
                                        {{ __('flutterwave') }}
                                    </option>
                                    <option {{ request('provider') == 'mollie' ? 'selected' : '' }} value="mollie">
                                        {{ __('mollie') }}
                                    </option>
                                    <option {{ request('provider') == 'midtrans' ? 'selected' : '' }} value="midtrans">
                                        {{ __('midtrans') }}
                                    </option>
                                    <option {{ request('provider') == 'offline' ? 'selected' : '' }} value="offline">
                                        {{ __('offline') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-xl-3 col-md-6 col-12">
                                <label>{{ __('plan') }}</label>
                                <select name="plan" class="form-control select2bs4">
                                    <option {{ request('plan') ? '' : 'selected' }} value="" selected>
                                        {{ __('all') }}
                                    </option>
                                    @foreach ($plans as $plan)
                                        @if ($plan->frontend_show)
                                            <option {{ request('plan') == $plan->id ? 'selected' : '' }}
                                                value="{{ $plan->id }}">{{ $plan->label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 col-md-6 col-12">
                                <label>{{ __('sort_by') }}</label>
                                <select name="sort_by" class="form-control select2bs4">
                                    <option {{ !request('sort_by') || request('sort_by') == 'latest' ? 'selected' : '' }}
                                        value="latest" selected>
                                        {{ __('latest') }}
                                    </option>
                                    <option {{ request('sort_by') == 'oldest' ? 'selected' : '' }} value="oldest">
                                        {{ __('oldest') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="card-body text-center table-responsive p-0">
                        <table class="ll-table table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>{{ __('order_and_transaction') }}</th>
                                    <th>{{ __('company') }}</th>
                                    <th>{{ __('plan') }}</th>
                                    <th>{{ __('amount') }}</th>
                                    <th>{{ __('payment_method') }}</th>
                                    <th>{{ __('created_time') }}</th>
                                    <th>{{ __('payment_status') }}</th>
                                    @if (userCan('order.download'))
                                        <th width="10%">{{ __('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                @if(isset($order->company->id))
                                    <tr>
                                        <td>
                                            <p>#{{ $order->order_id }}</p>
                                            <p>{{ __('transaction') }}: <strong>{{ $order->transaction_id }}</strong></p>
                                        </td>
                                        <td>
                                            <a href="{{ route('company.show', $order->company->id) }}" class="company">
                                                <img src="{{ $order->company->logo_url }}" alt="logo">
                                                <div>
                                                    <h4>{{ $order->company->user->name }}</h4>
                                                    <p>{{ $order->company->user->email }}</p>
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            @if ($order->payment_type == 'per_job_based')
                                                <span>{{ ucfirst(Str::replace('_', ' ', $order->payment_type)) }}</span>
                                            @else
                                                <span>{{ $order->plan->label }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ currencyPosition(
                                                currencyConversion($order->usd_amount, config('templatecookie.currency', 'USD'))
                                                ,false, $current_currency
                                                ) }}
                                        </td>
                                        <td>
                                            @if ($order->payment_provider == 'offline')
                                                {{ __('offline') }}
                                                @if (isset($order->manualPayment) && isset($order->manualPayment->name))
                                                    (<b>{{ $order->manualPayment->name }}</b>)
                                                @endif
                                            @else
                                                {{ ucfirst($order->payment_provider) }}
                                            @endif
                                        </td>

                                        <td class="text-muted">
                                            {{ formatTime($order->created_at, 'M d, Y') }}
                                        </td>
                                        <td>
                                            @if ($order->payment_status == 'paid')
                                                <span class="ll-badge ll-bg-success">{{ __('paid') }}</span>
                                            @else
                                                <span class="ll-badge ll-bg-warning">{{ __('unpaid') }}</span> <br><br>
                                                <div>
                                                    <a onclick="return confirm('{{ __('are_you_sure') }}')"
                                                        href="{{ route('manual.payment.mark.paid', $order->id) }}">
                                                        {{ __('mark_as_paid') }}
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="d-flex align-items-center">
                                            <a href="{{ route('order.show', $order->id) }}" class="btn ll-btn ll-border-none">
                                                {{ __('view_details') }}
                                                <x-svg.table-btn-arrow />
                                            </a>
                                            @if (userCan('order.download'))
                                                <form
                                                    action="{{ route('admin.transaction.invoice.download', $order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn ll-btn-info">
                                                        <x-svg.table-download />
                                                    </button>
                                                </form>
                                            @endif
                                            @if (userCan('order.download'))
                                                <form action="{{ route('admin.transaction.invoice.view', $order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn ll-btn-info ml-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                            height="20" fill="#767F8C" viewBox="0 0 256 256">
                                                            <path
                                                                d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            <div class="empty py-5">
                                                <x-not-found message="{{ __('no_data_found') }}" />
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($orders->total() > $orders->count())
                        <div class="mt-3 d-flex justify-content-center">{{ $orders->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

