@extends('backend.layouts.app')

@section('title')
    {{ __('update_user_plan_benefits') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-12 col-md-8 mb-3 mx-auto">
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5>
                        <i class="icon fas fa-info"></i>
                        {{ __('do_you_want_to_update_the_plan_data_of_the_user_under_this_order') }} ?
                    </h5>
                </div>
                <div class="card card-widget widget-user shadow">
                    <div class="widget-user-header bg-info">
                        <h3 class="widget-user-username">
                            {{ $plan->label }}
                        </h3>
                        <h5 class="widget-user-desc">
                            {{ config('templatecookie.currency_symbol') }} {{ $plan->price }}
                        </h5>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-2 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        {{ __('job_limit') }}
                                    </h5>
                                    <span class="description-text text-capilatize">
                                        {{ $plan->job_limit }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-2 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        {{ __('featured_job_limits') }}
                                    </h5>
                                    <span class="description-text text-capilatize">
                                        {{ $plan->featured_job_limit }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-2 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        {{ __('highlight_job_limit') }}
                                    </h5>
                                    <span class="description-text text-capilatize">
                                        {{ $plan->highlight_job_limit }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        {{ __('candidate_cv_view_limit') }}
                                    </h5>
                                    <span class="description-text text-capilatize">
                                        {{ $plan->candidate_cv_view_limit }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        {{ __('candidate_cv_view_limitation') }}
                                    </h5>
                                    <span class="description-text text-capilatize">
                                        {{ Str::ucfirst($plan->candidate_cv_view_limitation) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title" style="line-height: 36px;">
                            {{ __('update_user_plan_benefits') }}
                        </h3>
                        <a href="{{ route('order.index') }}"
                            class="btn bg-primary float-right d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-left"></i>
                            <span class="ml-2">
                                {{ __('back') }}
                            </span>
                        </a>
                    </div>
                    <div class="row pt-3 pb-4">
                        <div class="col-12 px-5">
                            <form class="form-horizontal" action="{{ route('user.plan.update', $user->id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')

                                {{-- <input type="text" value="{{ $earning }}" name="earning_id"> --}}

                                <!-- user plan details  -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <x-forms.label name="user_plan" required="true" />
                                                <select name="user_plan"
                                                    class="form-control select2bs4 @error('user_plan') is-invalid @enderror">
                                                    @foreach ($plans as $item)
                                                        <option {{ $plan->id == $item->id ? 'selected' : '' }}
                                                            value="{{ $item->id }}">
                                                            {{ $item->label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('user_plan')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <x-forms.label name="job_limit" required="true" />
                                                <input type="number" name="job_limit"
                                                    class="form-control @error('job_limit') is-invalid @enderror"
                                                    value="{{ $user->userPlan->job_limit }}"
                                                    placeholder="{{ __('job_limit') }}">
                                                @error('job_limit')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <x-forms.label name="featured_job_limit" required="true" />
                                                <input type="number" name="featured_job_limit"
                                                    class="form-control @error('job_limit') is-invalid @enderror"
                                                    value="{{ $user->userPlan->featured_job_limit }}"
                                                    placeholder="{{ __('featured_job_limit') }}">
                                                @error('featured_job_limit')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <x-forms.label name="highlight_job_limit" required="true" />

                                                <input type="number" name="highlight_job_limit"
                                                    class="form-control @error('highlight_job_limit') is-invalid @enderror"
                                                    value="{{ $user->userPlan->highlight_job_limit }}"
                                                    placeholder="{{ __('highlight_job_limit') }}">

                                                @error('highlight_job_limit')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <x-forms.label name="candidate_cv_view_limit" required="true" />

                                                <input type="number" name="candidate_cv_view_limit"
                                                    class="form-control @error('candidate_cv_view_limit') is-invalid @enderror"
                                                    value="{{ $user->userPlan->candidate_cv_view_limit }}"
                                                    placeholder="{{ __('candidate_cv_view_limit') }}">

                                                @error('candidate_cv_view_limit')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- user plan details end -->
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-plus"></i>
                                            &nbsp;{{ __('update_user_plan') }}
                                        </button>
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
        .card-footer {
            padding-top: 0px !important;
        }

        @media screen and (max-width: 768px) {
            .border-right {
                border-right: 0px !important;
            }
        }

        .widget-user .widget-user-header {
            height: 93px !important;
        }

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

        .description-block>.description-text {
            text-transform: none !important;
        }
    </style>
@endsection
