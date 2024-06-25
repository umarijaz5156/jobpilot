@extends('frontend.auth.layouts.auth')

@section('meta')
    @php
        $data = metaData('register');
    @endphp
@endsection

@section('description')
    {{ $data->description }}
@endsection

@section('title')
    {{ __('register') }}
@endsection

@section('og:image')
    {{ asset($data->image) }}
@endsection

@section('content')
    <div class="row">
        <div class="auth-page2 order-1 order-lg-0">
            <div class="rt-spacer-100  rt-spacer-lg-50 rt-spacer-xs-50"></div>
            <div class="rt-spacer-100 rt-spacer-lg-50 rt-spacer-xs-0"></div>
            <div class="rt-spacer-50 rt-spacer-lg-0 rt-spacer-xs-0"></div>
            <div class="container">
                <div class="row ">
                    <div class="col-xl-5 col-lg-6 col-md-12 tw-bg-white tw-relative tw-z-50">
                        <div class="auth-box2">
                            <form id="formId" action="{{ route('register') }}" method="POST" class="rt-form">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="rt-mb-20">{{ __('create_account') }}</h4>
                                        <span class="d-block body-font-3 text-gray-600 rt-mb-32">
                                            {{ __('already_have_account') }}
                                            <span>
                                                <a href="{{ route('login') }}">{{ __('log_in') }}</a>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-12">
                                        <div class="tw-bg-[#F1F2F4] tw-rounded-lg tw-mb-6 tw-p-3">
                                            <p class="tw-text-[#767F8C] tw-text-xs tw-font-medium tw-text-center tw-mb-2">
                                                {{ __('create_account_as_a') }}
                                            </p>
                                            <div
                                                class="switcher-container tw-px-0 tw-w-full tw-border-2 tw-border-red-600 tw-flex">
                                                <input id="switcher-toggle-on"
                                                    class="switcher-toggle switcher-toggle-left tw-w-full" name="role"
                                                    value="candidate" type="radio" checked="">
                                                <label for="switcher-toggle-on"
                                                    class="switcher-button tw-w-full tw-rounded-tl-md  tw-rounded-bl-md"
                                                    id="web-btn">
                                                    <span><x-svg.candidate-profile-icon /></span>
                                                    <span>{{ __('candidate') }}</span>
                                                </label>
                                                <input id="switcher-toggle-off"
                                                    class="switcher-toggle switcher-toggle-right tw-w-full" name="role"
                                                    value="company" type="radio">
                                                <label for="switcher-toggle-off"
                                                    class="switcher-button tw-w-full  tw-rounded-tr-md tw-rounded-br-md"
                                                    id="wp-btn">
                                                    <span><x-svg.employer-profile-icon /></span>
                                                    <span>{{ __('employer') }}</span>
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="fromGroup rt-mb-15">
                                            <input name="name" id="name" value="{{ old('name') }}"
                                                class="field form-control @error('name') is-invalid @enderror"
                                                type="text" placeholder="{{ __('full_name') }}">
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="fromGroup rt-mb-15">
                                            <input type="email" id="email" value="{{ old('email') }}" name="email"
                                                class="field form-control @error('email') is-invalid @enderror"
                                                placeholder="{{ __('email_address') }}">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="rt-mb-15">
                                    <div class="d-flex fromGroup">
                                        <input name="password" id="password"
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
                                <div class="rt-mb-15">
                                    <div class="d-flex fromGroup">
                                        <input name="password_confirmation" id="password_confirmation"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            type="password" placeholder="{{ __('confirm_password') }}">
                                        <div onclick="passToText('password_confirmation','eyeIcon2')" id="eyeIcon2"
                                            class="has-badge">
                                            <i class="ph-eye @error('password_confirmation') m-3 @enderror"></i>
                                        </div>
                                    </div>
                                    @error('password_confirmation')
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
                                <div class="rt-mb-30">
                                    <div class="form-check from-chekbox-custom align-items-center">
                                        <input id="term" class="form-check-input" type="checkbox" value="1"
                                            required>
                                        <label class="form-check-label pointer text-gray-700 f-size-14" for="term">
                                            {{ __('i_have_read_and_agree_with') }}
                                        </label>
                                        <a href="{{ url('terms-condition') }}" target="_blank"
                                            class="body-font-4 text-primary-500">
                                            {{ __('terms_of_service') }}
                                        </a>
                                    </div>

                                </div>
                                <button id="submitButton" type="submit" class="btn btn-primary d-block rt-mb-15">
                                    <span class="button-content-wrapper ">
                                        <span class="button-icon align-icon-right">
                                            <x-svg.rightarrow-icon />
                                        </span>
                                        <span class="button-text">
                                            {{ __('create_account') }}
                                        </span>
                                    </span>
                                </button>

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
                                            <div class="d-flex justify-content-center col-12 rt-mb-15">
                                                <button onclick="LoginService('linkedin-openid')" type="button"
                                                    class="w-100 btn btn-outline-plain custom-padding ">
                                                    <span class="button-content-wrapper ctr">
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rt-spacer-100 rt-spacer-md-50"></div>
        </div>
        <div class="auth-right-sidebar reg-sidebar order-1 order-lg-0">
            <div class="sidebar-bg" style="background-image: url({{ asset($cms_setting->register_page_image) }})">
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
                                        <div class="iconbox-content">
                                            <div class="f-size-20 ft-wt-5"><span
                                                    class="counter">{{ livejob() }}</span>
                                            </div>
                                            <span class=" f-size-14">{{ __('live_job') }}</span>
                                        </div>
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
                                            <div class="f-size-20 ft-wt-5"><span
                                                    class="counter">{{ companies() }}</span>
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
                                            <span class=" f-size-14">{{ __('candidates') }}</span>
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
                        <form id="LoginFormHit" class="d-inline justify-content-center" method="GET">
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
                                <button onclick="CloseModoal()" type="button" class="close btn btn-danger">
                                    <div class="button-content-wrapper ">
                                        <span class="button-text">
                                            {{ __('cancel') }}
                                        </span>
                                    </div>
                                </button>
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
        function LoginService(value) {
            $("#ModalBtn").css("display", "block");
            var action = "auth/" + value + "/redirect";
            $("#LoginFormHit").attr("action", action);
        }

        function CloseModoal() {
            $("#ModalBtn").css("display", "none");
        }
    </script>
    <script>
        $(document).ready(function() {
            validate();
            $('#name, #email, #password, #password_confirmation, #term').keyup(validate);
        });

        function validate() {
            if (
                $('#name').val().length > 0 &&
                $('#email').val().length > 0 &&
                $('#password').val().length > 0 &&
                $('#password_confirmation').val().length > 0 &&
                $('#term').val().length > 0) {
                $('#submitButton').attr('disabled', false);
            } else {
                $('#submitButton').attr('disabled', true);
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
    </script>
@endsection
@section('style')
    <style>
        .font-size-25 {
            font-size: 25px !important;
        }
    </style>
@endsection
