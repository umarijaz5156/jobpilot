@extends('frontend.auth.layouts.auth')

@section('meta')
    @php
        $data = metaData('login');
    @endphp
@endsection

@section('description')
    {{ $data->description }}
@endsection

@section('title')
    {{ __('login') }}
@endsection

@section('og:image')
    {{ asset($data->image) }}
@endsection

@section('content')
    <div class="row mt-0 mt-lg-5">
        <div class="full-height col-12 order-1 order-lg-0">
            <div class="container">
                <div class="row full-height align-items-center">
                    <div class="col-xl-5 col-lg-6 col-md-12">
                        <div class="auth-box2">
                            <form action="{{ route('login') }}" method="POST" class="rt-form" id="login_form">
                                @csrf
                                <h4 class="rt-mb-20">{{ __('log_in') }}</h4>
                                <span class="d-block body-font-3 text-gray-600 rt-mb-32">
                                    {{ __('dont_have_account') }}
                                    <span>
                                        <a href="{{ route('register') }}">{{ __('create_account') }}
                                        </a>
                                    </span>
                                </span>
                                <div class="fromGroup rt-mb-15">
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="{{ __('email_address') }}">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                    @enderror
                                </div>
                                <div class="rt-mb-15">
                                    <div class="d-flex fromGroup">
                                        <input name="password" id="password" value=""
                                            class="form-control @error('password') is-invalid @enderror" type="password"
                                            placeholder="{{ __('password') }}">
                                        <div onclick="passToText('password','eyeIcon')" id="eyeIcon" class="has-badge">
                                            <i class="ph-eye @error('password') m-3 @enderror"></i>
                                        </div>
                                    </div>
                                    @error('password')
                                        <span class="text-danger" role="alert">{{ __($message) }}</span>
                                    @enderror
                                </div>
                                @if (config('captcha.active'))
                                    <div class="rt-mb-15">
                                        <div class="g-custom-css">
                                            {!! NoCaptcha::display() !!}
                                        </div>
                                        @if ($errors->has('g-recaptcha-response'))
                                            <span class="text-danger text-sm">
                                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                <div class="d-flex flex-wrap rt-mb-30">
                                    <div class="flex-grow-1">
                                        <div class="form-check from-chekbox-custom">
                                            <input name="remember" id="remember" class="form-check-input" type="checkbox"
                                                value="1">
                                            <label class="form-check-label pointer text-gray-700 f-size-14" for="remember">
                                                {{ __('keep_me_logged') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex-grow-0">
                                        <span class="body-font-4">
                                            <a href="{{ route('password.request') }}" class="text-primary-500">
                                                {{ __('forget_password') }}
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <button id="submitButton" type="submit" class="btn btn-primary d-block rt-mb-15">
                                    <span class="button-content-wrapper ">
                                        <span class="button-icon align-icon-right">
                                            <x-svg.rightarrow-icon />
                                        </span>
                                        <span class="button-text">
                                            {{ __('log_in') }}
                                        </span>
                                    </span>
                                </button>
                            </form>
                            @php
                                $google = config('services.google.active') && config('services.google.client_id') && config('services.google.client_secret');
                                $facebook = config('services.facebook.active') && config('services.facebook.client_id') && config('services.facebook.client_secret');
                                $twitter = config('services.twitter.active') && config('services.twitter.client_id') && config('services.twitter.client_secret');
                                $linkedin = config('services.linkedin-openid.active') && config('services.linkedin-openid.client_id') && config('services.linkedin-openid.client_secret');
                                $github = config('services.github.active') && config('services.github.client_id') && config('services.github.client_secret');
                            @endphp
                            <div>
                                @if ($google || $facebook || $twitter || $linkedin || $github)
                                    <p class="or text-center">{{ __('or') }}</p>
                                @endif
                                <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-3 tw-gap-6">
                                    @if ($google)
                                        <div>
                                            <a href="{{ route('social.login', 'google') }}"
                                                class="btn btn-outline-plain d-block custom-padding me-3 rt-mb-xs-10 ">
                                                <span class="button-content-wrapper">
                                                    <span class="button-icon align-icon-left">
                                                        <x-svg.google-icon />
                                                    </span>
                                                    <span class="button-text">
                                                        {{ __('google') }}
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                    @endif
                                    @if ($facebook)
                                        <div>
                                            <a href="{{ route('social.login', 'facebook') }}"
                                                class="btn btn-outline-plain d-block custom-padding me-3 rt-mb-xs-10 ">
                                                <span class="button-content-wrapper ">
                                                    <span class="button-icon align-icon-left">
                                                        <x-svg.facebook-icon />
                                                    </span>
                                                    <span class="button-text">
                                                        {{ __('facebook') }}
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                    @endif
                                    @if ($twitter)
                                        <div>
                                            <a href="{{ route('social.login', 'twitter') }}"
                                                class="btn btn-outline-plain d-block custom-padding me-3 rt-mb-xs-10 ">
                                                <span class="button-content-wrapper ">
                                                    <span class="button-icon align-icon-left">
                                                        <x-svg.twitter-icon fill="#007ad9" />
                                                    </span>
                                                    <span class="button-text">
                                                        {{ __('twitter') }}
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                    @endif
                                    @if ($linkedin)
                                        <div>
                                            <a href="{{ route('social.login', 'linkedin-openid') }}"
                                                class="btn btn-outline-plain d-block custom-padding me-3 rt-mb-xs-10 ">
                                                <span class="button-content-wrapper ">
                                                    <span class="button-icon align-icon-left">
                                                        <x-svg.linkedin-icon />
                                                    </span>
                                                    <span class="button-text">
                                                        {{ __('linkedin') }}
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                    @endif
                                    @if ($github)
                                        <div>
                                            <a href="{{ route('social.login', 'github') }}"
                                                class="btn btn-outline-plain d-block custom-padding me-3 rt-mb-xs-10 ">
                                                <span class="button-content-wrapper ">
                                                    <span class="button-icon align-icon-left">
                                                        <x-svg.github-icon />
                                                    </span>
                                                    <span class="button-text">
                                                        {{ __('github') }}
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="auth-right-sidebar r-z order-1 order-lg-0">
            <div class="sidebar-bg" style="background-image: url({{ asset($cms_setting->login_page_image) }})">
                <div class="sidebar-content">
                    <h4 class="text-gray-10 rt-mb-50">{{ openJobs() }} {{ __('open_jobs_waiting_for_you') }}</h4>
                    <div class="d-flex">
                        <div class="flex-grow-1 rt-mb-24">
                            <div class="card jobcardStyle1 counterbox4">
                                <div class="card-body">
                                    <div class="rt-single-icon-box icon-center2">
                                        <div class="icon-thumb">
                                            <div class="icon-64">
                                                <x-svg.livejob-icon />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="iconbox-content">
                                        <div class="f-size-20 ft-wt-5"><span class="counter">{{ livejob() }}</span>
                                        </div>
                                        <span class=" f-size-14">{{ __('live_job') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1  rt-mb-24">
                            <div class="card jobcardStyle1 counterbox4">
                                <div class="card-body">
                                    <div class="rt-single-icon-box icon-center2">
                                        <div class="icon-thumb">
                                            <div class="icon-64">
                                                <x-svg.thumb-icon />
                                            </div>
                                        </div>
                                        <div class="iconbox-content">
                                            <div class="f-size-20 ft-wt-5"><span class="counter">{{ companies() }}</span>
                                            </div>
                                            <span class=" f-size-14">{{ __('companies') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1 rt-mb-24">
                            <div class="card jobcardStyle1 counterbox4">
                                <div class="card-body">
                                    <div class="rt-single-icon-box icon-center2">
                                        <div class="icon-thumb">
                                            <div class="icon-64">
                                                <x-svg.newjobs-icon />
                                            </div>
                                        </div>
                                        <div class="iconbox-content">
                                            <div class="f-size-20 ft-wt-5"><span
                                                class="counter">{{ $candidates }}</span>
                                            </div>
                                            <span class="f-size-14">
                                                <span>{{ __('candidates') }}</span>
                                                {{-- <span>{{ __('total_new_jobs') }}</span> --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- The Modal -->
    <div id="ModalBtn" class="modal">
        <div class="row justify-content-center m-2 mt-5 pt-5">
            <div class="col-sm-12 col-lg-4">
                <div class="rt-rounded-12">
                    <div class="card border border-gray-500">
                        <div class="card-header bg-primary text-white font-size-25">
                            {{ __('select_one') }}
                        </div>
                        <form class="d-inline justify-content-center" method="POST"
                            action="{{ route('social.register') }}">
                            @csrf
                            @if (session()->has('warning'))
                                <div class="alert alert-warning m-3" role="alert">
                                    {{ __('we_couldnot_find_any_accounts_please_continue_to_register_as_a_candidate_or_employer') }}
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"
                                            for="experience">{{ __('employer_or_candidate') }}</label>
                                        <select name="user" class="form-controll rounded" id="">
                                            <option value="candidate">{{ __('candidate') }}</option>
                                            <option value="company">{{ __('employer') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <a href="{{ route('login') }}" class="close btn btn-danger">
                                    <div class="button-content-wrapper ">
                                        <span class="button-text">
                                            {{ __('cancel') }}
                                        </span>
                                    </div>
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <div class="button-content-wrapper ">
                                        <span class="button-text">
                                            {{ __('register_now') }}
                                        </span>
                                    </div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        $(document).ready(function() {
            Validate();
            $('#email, #password').keyup(Validate);
        });

        function Validate() {
            if (
                $('#email').val().length > 0 &&
                $('#password').val().length > 0) {
                $('#submitButton').prop('disabled', false);
            } else {
                $('#submitButton').prop('disabled', true);
            }
        }

        function passToText(id, icon) {
            var input = $('#' + id);
            var eyeIcon = $('#' + icon);
            if (input.is('input[type="password"]')) {
                eyeIcon.html('<i class="ph-eye-slash @error('password') m-3 @enderror"></i>');
                input.attr('type', 'text');
            } else {
                eyeIcon.html('<i class="ph-eye @error('password') m-3 @enderror"></i>');
                input.attr('type', 'password');
            }
        }

        function loginCredentials(type) {
            if (type == 'candidate') {
                $('#email').val('candidate@mail.com')
                $('#password').val('password')
            } else {
                $('#email').val('company@mail.com')
                $('#password').val('password')
            }

            $('#login_form').submit();
        }

        if ({{ request('social_register') ?? 0 }} == 1) {
            LoginService();
        }

        function LoginService() {
            $("#ModalBtn").css("display", "block");
        }
    </script>
@endsection
