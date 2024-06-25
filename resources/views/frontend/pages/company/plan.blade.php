@extends('frontend.layouts.app')

@section('title')
    {{ __('plan') }}
@endsection

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <x-website.company.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right tw-ps-0 lg:tw-ps-5">
                        <div class="row tw-my-5">
                            <div class="col-lg-5">
                                <div class="plan-card">
                                    <h2 class="title">{{ $userplan->plan->label }}</h2>
                                    <p class="short-desc">
                                        @if (isset($userplan->plan->descriptions) && isset($userplan->plan->descriptions[0]))
                                            {!! $userplan->plan->descriptions[0]->description !!}
                                        @else
                                            <span class="text-danger">{!! __('no_description_has_been_added_to_this_language', ['current' => $current_language_code]) !!}</span>
                                        @endif
                                    </p>
                                    <div class="">
                                        <a href="{{ route('website.plan') }}" class="btn btn-primary">
                                            {{ __('upgrade_plan') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="benefits-card">
                                    <h4 class="title">{{ __('create_plan_benefits') }}</h4>
                                    <ul class="benefit-list">
                                        <li>
                                            <x-svg.double-check-icon />
                                            <span>{{ $userplan->plan->job_limit }} {{ __('active_jobs') }}</span>
                                        </li>
                                        <li>
                                            <x-svg.double-check-icon />
                                            <span>{{ $userplan->plan->highlight_job_limit }}
                                                {{ __('highlight_jobs') }}</span>
                                        </li>
                                        <li>
                                            <x-svg.double-check-icon />
                                            <span>{{ $userplan->plan->featured_job_limit }}
                                                {{ __('featured_jobs') }}</span>
                                        </li>
                                        <li>
                                            <x-svg.double-check-icon />
                                            <span>{{ $userplan->plan->candidate_cv_view_limitation == 'limited' ? $userplan->plan->candidate_cv_view_limit : __('unlimited') }}
                                                {{ __('candidate_profile_view') }}</span>
                                        </li>
                                    </ul>
                                    <div class="remaining">
                                        <h4 class="title">{{ __('remaining') }}</h4>
                                        <ul class="remaining-list">
                                            <li>
                                                @if (!$userplan->job_limit)
                                                    <x-svg.cross-round2-icon />
                                                @else
                                                    <x-svg.double-check-icon />
                                                @endif
                                                <span>{{ $userplan->job_limit }} {{ __('active_jobs') }}</span>
                                            </li>
                                            <li>
                                                @if (!$userplan->highlight_job_limit)
                                                    <x-svg.cross-round2-icon />
                                                @else
                                                    <x-svg.double-check-icon />
                                                @endif
                                                <span>{{ $userplan->highlight_job_limit }}
                                                    {{ __('highlight_jobs') }}</span>
                                            </li>
                                            <li>
                                                @if (!$userplan->featured_job_limit)
                                                    <x-svg.cross-round2-icon />
                                                @else
                                                    <x-svg.double-check-icon />
                                                @endif
                                                <span>{{ $userplan->featured_job_limit }}
                                                    {{ __('featured_jobs') }}</span>
                                            </li>
                                            <li>
                                                @if ($userplan->candidate_cv_view_limitation == 'unlimited')
                                                    <x-svg.double-check-icon />
                                                    <span>
                                                        {{ __('unlimited') }} {{ __('candidate_profile_view') }}
                                                    </span>
                                                @else
                                                    @if (!$userplan->candidate_cv_view_limit)
                                                        <x-svg.cross-round2-icon />
                                                    @else
                                                        <x-svg.double-check-icon />
                                                    @endif
                                                    <span>
                                                        {{ $userplan->candidate_cv_view_limit }}
                                                        {{ __('candidate_profile_view') }}
                                                    </span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoices-table ">
                            <h2 class="title">{{ __('latest_invoice') }}</h2>
                            <div class="table-wrapper pb-1">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('date') }}</th>
                                            <th>{{ __('plan') }}</th>
                                            <th>{{ __('amount') }}</th>
                                            <th>{{ __('payment_provider') }}</th>
                                            <th>{{ __('payment_status') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($transactions->count() > 0)
                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td>#{{ $transaction->order_id }}</td>
                                                    <td>{{ formatTime($transaction->created_at, 'M, d Y') }}</td>
                                                    <td>
                                                        @if ($transaction->payment_type == 'per_job_based')
                                                            <span
                                                                class="badge bg-secondary">{{ ucfirst(Str::replace('_', ' ', $transaction->payment_type)) }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-primary">{{ $transaction->plan->label }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ currencyConversion($transaction->usd_amount, 'USD') }}
                                                        {{ currentCurrencyCode() }}
                                                    </td>
                                                    <td>
                                                        @if ($transaction->payment_provider == 'offline')
                                                            {{ __('offline') }}
                                                            @if (isset($transaction->manualPayment) && isset($transaction->manualPayment->name))
                                                                (<b>{{ $transaction->manualPayment->name }}</b>)
                                                            @endif
                                                        @else
                                                            {{ ucfirst($transaction->payment_provider) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($transaction->payment_status == 'paid')
                                                            <span
                                                                class="badge badge-pill bg-success">{{ __('paid') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-pill bg-warning">{{ __('unpaid') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="tw-inline-flex tw-gap-2 tw-items-center">
                                                            <form
                                                            action="{{ route('company.transaction.invoice.download', $transaction->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn tw-p-0">
                                                                <x-svg.download-icon />
                                                            </button>
                                                        </form>
                                                        <a
                                                            href="{{ route('company.transaction.invoice.view', $transaction->order_id) }}">
                                                            {{ __('view_invoice') }}
                                                        </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <x-website.not-found />
                                        @endif
                                    </tbody>
                                </table>
                                @if (request('perpage') != 'all' && $transactions->total() > $transactions->count())
                                    <div class="rt-pt-30 mb-3">
                                        <nav>
                                            {{ $transactions->onEachSide(0)->links('vendor.pagination.frontend') }}
                                        </nav>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
