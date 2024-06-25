@extends('backend.settings.setting-layout')
@section('title')
    {{ __('website_settings') }}
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
                <li class="breadcrumb-item active">{{ __('website_settings') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('website-settings')
    <div class="row">
        <div class="col-xl-6 col-lg-12">
            <div class="card mb-3">
                <div class="card-header">
                    {{ __('basic_setting') }}
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('settings.general.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row ">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="" for="site_name"> {{ __('app_name') }} </label>
                                    <input value="{{ config('app.name') }}" name="name" type="text"
                                        class="form-control " placeholder="{{ __('app_name') }}" id="site_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="" for="site_email"> {{ __('email') }} </label>
                                    <input value="{{ $setting->email }}" name="email" type="text"
                                        class="form-control " placeholder="{{ __('email') }}" id="site_email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <x-forms.label name="dark_logo">
                                    <x-info-tip message="Recommended: 150x50" />
                                </x-forms.label>
                                <input type="file" class="form-control dropify"
                                    data-default-file="{{ $setting->dark_logo_url }}" name="dark_logo"
                                    data-allowed-file-extensions='["jpg", "jpeg","png","svg"]'
                                    accept="image/png, image/jpg,image/svg image/jpeg" data-max-file-size="3M">
                                @error('dark_logo')
                                    <span class="invalid-feedback d-block" role="alert">{{ __($message) }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-4">
                                <x-forms.label name="light_logo">
                                    <x-info-tip message="{{ __('recommended') }}: 150x50" />
                                </x-forms.label>
                                <input type="file" class="form-control dropify"
                                    data-default-file="{{ $setting->light_logo_url }}" name="light_logo"
                                    data-allowed-file-extensions='["jpg", "jpeg","png","svg"]'
                                    accept="image/png, image/jpg,image/svg image/jpeg" data-max-file-size="3M">
                                @error('light_logo_url')
                                    <span class="invalid-feedback d-block" role="alert">{{ __($message) }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-4">
                                <x-forms.label name="favicon">
                                    <x-info-tip message="{{ __('recommended') }}: 32x32" />
                                </x-forms.label>
                                <input type="file" class="form-control dropify"
                                    data-default-file="{{ $setting->favicon_image_url }}" name="favicon_image"
                                    data-allowed-file-extensions='["jpg", "jpeg","png","svg"]'
                                    accept="image/png, image/jpg,image/svg image/jpeg" data-max-file-size="1M">
                                @error('favicon_image')
                                    <span class="invalid-feedback d-block" role="alert">{{ __($message) }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row mt-3">
                            <div class="col-12">
                                <label>Choose Landing Page </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="image-container">
                                    <input type="radio" name="landing_page" id="1" value="1"  {{ $setting->landing_page == 1 ? 'checked' : '' }}>
                                    <img class="full-image" src="{{ asset('backend/image/landing-page-01.png') }}"
                                        alt="">
                                    <span class="checked-badge"></span>
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="image-container">
                                    <input type="radio" name="landing_page" id="2" value="2" {{ $setting->landing_page == 2 ? 'checked' : '' }}>
                                    <img class="full-image" src="{{ asset('backend/image/landing-page-02.png') }}"
                                        alt="">
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="image-container">
                                    <input type="radio" name="landing_page" id="3" value="3" {{ $setting->landing_page == 3 ? 'checked' : '' }}>
                                    <img class="full-image" src="{{ asset('backend/image/landing-page-03.png') }}"
                                        alt="">
                                </label>
                            </div>
                        </div> --}}
                        <div class="row mt-3 mx-auto">
                            @if (userCan('setting.update'))
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-sync"></i>
                                    {{ __('update') }}
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- PWA Setting Start -->
            <div class="card mb-3">
                <div class="card-header">
                    {{ __('pwa_settings') }}
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('settings.pwa.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-sm-5">
                                <x-forms.label name="app_pwa_icon">
                                    <x-info-tip message="{{ __('recommended') }}: 512x512" />
                                </x-forms.label>
                                <input type="file" class="form-control dropify"
                                    data-default-file="{{ $setting->app_pwa_icon_url }}" name="app_pwa_icon"
                                    data-allowed-file-extensions='["jpg", "jpeg","png"]'
                                    accept="image/png, image/jpg, image/jpeg">

                                <p class="img-size-note text-danger mt-2">{{ __('app_pwa_icon_size_note') }}</p>

                                @error('app_pwa_icon')
                                    <span class="invalid-feedback d-block" role="alert">{{ __($message) }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <x-forms.label name="pwa_enable" />
                                    <div>
                                        <input type="hidden" name="pwa_enable" value="0" />
                                        <input type="checkbox" id="pwa_enable"
                                            {{ $setting->pwa_enable ? 'checked' : '' }} name="pwa_enable"
                                            data-bootstrap-switch data-on-color="success"
                                            data-on-text="{{ __('on') }}" data-off-color="default"
                                            data-off-text="{{ __('off') }}" data-size="small" value="1">
                                        <x-forms.error name="pwa_enable" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 mx-auto text-center">
                            <div class="col-sm-12">
                                @if (userCan('setting.update'))
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-sync"></i>
                                        {{ __('update') }}
                                    </button>
                                @endif
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <!-- PWA Setting End -->

            <!-- Google recaptcha Setting -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title line-height-36">
                            {{ __('recaptcha_configuration') }}
                            (<small><a href="https://support.google.com/recaptcha"
                                    target="_blank">{{ __('get_help') }}</a></small>)
                        </h3>
                    </div>
                </div>
                <form class="form-horizontal" action="{{ route('settings.recaptcha.update') }}" method="POST"
                    id="recaptch_form">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <x-forms.label name="nocaptcha_sitekey" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ old('nocaptcha_key', config('captcha.sitekey')) }}" name="nocaptcha_key"
                                    type="text" class="form-control @error('nocaptcha_key') is-invalid @enderror"
                                    autocomplete="off">
                                @error('nocaptcha_key')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="nocaptcha_secret" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ old('nocaptcha_secret', config('captcha.secret')) }}"
                                    name="nocaptcha_secret" type="text" placeholder=""
                                    class="form-control @error('nocaptcha_secret') is-invalid @enderror"
                                    autocomplete="off">
                                @error('nocaptcha_secret')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="status" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input {{ config('captcha.active') ? 'checked' : '' }} type="checkbox" name="status"
                                    data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                                    data-off-color="default" data-on-color="success"
                                    data-off-text="{{ __('off') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-7 offset-sm-5">
                                <div class="input-group text-center">
                                    {!! NoCaptcha::display() !!}
                                    @if ($errors->has('g-recaptcha-response'))
                                        <span class="text-danger text-sm">
                                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if (userCan('setting.update'))
                            <div class="form-group row">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                                        {{ __('update') }}</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Pusher Setting -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title line-height-36">
                            {{ __('pusher_configuration') }}
                        </h3>
                    </div>
                </div>
                <form class="form-horizontal" action="{{ route('settings.pusher.update') }}" method="POST"
                    id="pusher_form">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <x-forms.label name="pusher_app_id" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ old('pusher_app_id', config('templatecookie.pusher_app_id')) }}"
                                    name="pusher_app_id" type="text"
                                    class="form-control @error('pusher_app_id') is-invalid @enderror" autocomplete="off">
                                @error('pusher_app_id')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="pusher_app_key" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ old('pusher_app_key', config('templatecookie.pusher_app_key')) }}"
                                    name="pusher_app_key" type="text"
                                    class="form-control @error('pusher_app_key') is-invalid @enderror" autocomplete="off">
                                @error('pusher_app_key')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="pusher_app_secret" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ old('pusher_app_secret', config('templatecookie.pusher_app_secret')) }}"
                                    name="pusher_app_secret" type="text"
                                    class="form-control @error('pusher_app_secret') is-invalid @enderror"
                                    autocomplete="off">
                                @error('pusher_app_secret')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="pusher_host" class="col-sm-5" :required="false" />
                            <div class="col-sm-7">
                                <input value="{{ old('pusher_host', config('templatecookie.pusher_host')) }}"
                                    name="pusher_host" type="text"
                                    class="form-control @error('pusher_host') is-invalid @enderror" autocomplete="off">
                                @error('pusher_host')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="pusher_port" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ old('pusher_port', config('templatecookie.pusher_port')) }}"
                                    name="pusher_port" type="text"
                                    class="form-control @error('pusher_port') is-invalid @enderror" autocomplete="off">
                                @error('pusher_port')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="pusher_schema" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ old('pusher_schema', config('templatecookie.pusher_schema')) }}"
                                    name="pusher_schema" type="text"
                                    class="form-control @error('pusher_schema') is-invalid @enderror" autocomplete="off">
                                @error('pusher_schema')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="pusher_app_cluster" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input
                                    value="{{ old('pusher_app_cluster', config('templatecookie.pusher_app_cluster')) }}"
                                    name="pusher_app_cluster" type="text"
                                    class="form-control @error('pusher_app_cluster') is-invalid @enderror"
                                    autocomplete="off">
                                @error('pusher_app_cluster')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        @if (userCan('setting.update'))
                            <div class="form-group row">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                                        {{ __('update') }}</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>


            {{-- PUSHER_APP_ID=1537839
            PUSHER_APP_KEY=5be0e06bbae2726b72ba
            PUSHER_APP_SECRET=45aa7ed9d8ba8f621f1c
            PUSHER_HOST=
            PUSHER_PORT=443
            PUSHER_SCHEME=https
            PUSHER_APP_CLUSTER=ap2 --}}

            {{-- 'pusher_app_id' => env('PUSHER_APP_ID'),
            'pusher_app_key' => env('PUSHER_APP_KEY'),
            'pusher_app_secret' => env('PUSHER_APP_SECRET'),
            'pusher_host' => env('PUSHER_HOST'),
            'pusher_port' => env('PUSHER_PORT'),
            'pusher_schema' => env('PUSHER_SCHEME', 'https'),
            'pusher_app_cluster' => env('PUSHER_APP_CLUSTER'), --}}

            <!-- Google analytics Setting -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title line-height-36">
                            {{ __('Google Analytics Configuration') }}
                            (<small><a href="https://analytics.google.com/analytics/web/provision/#/provision"
                                    target="_blank">{{ __('get_help') }}</a></small>)
                        </h3>
                    </div>
                </div>
                <form class="form-horizontal" action="{{ route('settings.analytics.update') }}" method="POST"
                    id="analytics_form">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <x-forms.label name="Google Analytics ID" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ old('analytics_id') ?? config('templatecookie.google_analytics') }}"
                                    name="analytics_id" type="text"
                                    class="form-control @error('analytics_id') is-invalid @enderror" autocomplete="off">
                                @error('analytics_id')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="Google Analytics Status" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input
                                    {{ old('is_analytics_active') ?? config('templatecookie.google_analytics_status') ? 'checked' : '' }}
                                    type="checkbox" name="is_analytics_active" data-bootstrap-switch value="1"
                                    data-on-text="{{ __('on') }}" data-off-color="default" data-on-color="success"
                                    data-off-text="{{ __('off') }}">
                            </div>
                        </div>
                        @if (userCan('setting.update'))
                            <div class="form-group row">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                                        {{ __('update') }}</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ __('app_configuration') }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.system.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-forms.label name="time_zone" />
                                    <select name="timezone"
                                        class="@error('timezone') is-invalid @enderror select2bs4 form-control">
                                        @foreach ($timezones as $timezone)
                                            <option {{ env('APP_TIMEZONE') == $timezone->value ? 'selected' : '' }}
                                                value="{{ $timezone->value }}">
                                                {{ $timezone->value }}
                                            </option>
                                        @endforeach
                                        @error('timezone')
                                            <span class="invalid-feedback"
                                                role="alert"><span>{{ $message }}</span></span>
                                        @enderror
                                    </select>
                                </div>
                                <div class="form-group">
                                    <x-forms.label name="set_default_language" />
                                    <select class="form-control select2bs4 @error('code') is-invalid @enderror"
                                        name="code" id="default_language">
                                        @foreach ($languages as $language)
                                            <option
                                                {{ $language->code == config('templatecookie.default_language') ? 'selected' : '' }}
                                                value="{{ $language->code }}">
                                                {{ $language->name }}({{ $language->code }})
                                            </option>
                                        @endforeach
                                        @error('code')
                                            <span class="invalid-feedback"
                                                role="alert"><span>{{ $message }}</span></span>
                                        @enderror
                                    </select>
                                </div>
                                <div class="form-group">
                                    <x-forms.label name="set_default_currency" for="inlineFormCustomSelect" />
                                    <select name="currency" class="custom-select select2bs4 mr-sm-2"
                                        id="inlineFormCustomSelect">
                                        <option value="" disabled selected>{{ __('currency') }}
                                        </option>
                                        @foreach ($currencies as $key => $currency)
                                            <option
                                                {{ config('templatecookie.currency') == $currency->code ? 'selected' : '' }}
                                                value="{{ $currency->id }}">
                                                {{ $currency->name }} ( {{ $currency->code }} )
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <x-forms.label name="email_verification" :required="true" />
                                    <div>
                                        <input type="hidden" name="email_verification" value="0" />
                                        <input type="checkbox" id="email_verification"
                                            {{ $setting->email_verification ? 'checked' : '' }} name="email_verification"
                                            data-on-color="success" data-bootstrap-switch
                                            data-on-text="{{ __('on') }}" data-off-color="default"
                                            data-off-text="{{ __('off') }}" data-size="small" value="1">
                                        <x-forms.error name="email_verification" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <x-forms.label name="job_approval" :required="true" />
                                    <div>
                                        <input type="hidden" name="job_approval" value="0" />
                                        <input type="checkbox" id="job_approval"
                                            {{ $setting->job_auto_approved ? 'checked' : '' }} name="job_approval"
                                            data-on-color="success" data-bootstrap-switch
                                            data-on-text="{{ __('auto') }}" data-off-color="default"
                                            data-off-text="{{ __('manual') }}" data-size="small" value="1">
                                        <x-forms.error name="job_approval" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <x-forms.label name="app_debug" />
                                    <div>
                                        <input type="hidden" name="app_debug" value="0" />
                                        <input type="checkbox" id="app_debug" {{ config('app.debug') ? 'checked' : '' }}
                                            name="app_debug" data-bootstrap-switch data-on-color="success"
                                            data-on-text="{{ __('on') }}" data-off-color="default"
                                            data-off-text="{{ __('off') }}" data-size="small" value="1">
                                        <x-forms.error name="app_debug" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <x-forms.label class="mt-1" name="website_language_switcher" :required="true" />
                                    <div>
                                        <input type="hidden" name="language_changing" value="0" />
                                        <input type="checkbox" id="language_changing"
                                            {{ $setting->language_changing ? 'checked' : '' }} name="language_changing"
                                            data-on-color="success" data-bootstrap-switch
                                            data-on-text="{{ __('on') }}" data-off-color="default"
                                            data-off-text="{{ __('off') }}" data-size="small" value="1">
                                        <x-forms.error name="language_changing" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <x-forms.label class="mt-2" name="frontend_currency_switcher" :required="true" />
                                    <div>
                                        <input type="hidden" name="currency_switcher" value="0" />
                                        <input type="checkbox" id="currency_switcher"
                                            {{ $setting->currency_switcher ? 'checked' : '' }} name="currency_switcher"
                                            data-on-color="success" data-bootstrap-switch
                                            data-on-text="{{ __('on') }}" data-off-color="default"
                                            data-off-text="{{ __('off') }}" data-size="small" value="1">
                                        <x-forms.error name="currency_switcher" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <x-forms.label class="mt-2" name="employer_account_auto_activation"
                                        :required="true" />
                                    <div>
                                        <input type="hidden" name="employer_auto_activation" value="0" />
                                        <input type="checkbox" id="employer_auto_activation"
                                            {{ $setting->employer_auto_activation ? 'checked' : '' }}
                                            name="employer_auto_activation" data-on-color="success" data-bootstrap-switch
                                            data-on-text="{{ __('on') }}" data-off-color="default"
                                            data-off-text="{{ __('off') }}" data-size="small" value="1">
                                        <x-forms.error name="employer_auto_activation" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <x-forms.label class="mt-1" name="candidate_account_auto_activation"
                                        :required="true" />
                                    <div>
                                        <input type="hidden" name="candidate_account_auto_activation" value="0" />
                                        <input type="checkbox" id="candidate_account_auto_activation"
                                            {{ $setting->candidate_account_auto_activation ? 'checked' : '' }}
                                            name="candidate_account_auto_activation" data-on-color="success"
                                            data-bootstrap-switch data-on-text="{{ __('on') }}"
                                            data-off-color="default" data-off-text="{{ __('off') }}"
                                            data-size="small" value="1">
                                        <x-forms.error name="candidate_account_auto_activation" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <x-forms.label class="mt-1" name="edited_job_approval" :required="true" />
                                    <div>
                                        <input type="hidden" name="edited_job_approval" value="0" />
                                        <input type="checkbox" id="edited_job_approval"
                                            {{ $setting->edited_job_auto_approved ? 'checked' : '' }}
                                            name="edited_job_approval" data-on-color="success" data-bootstrap-switch
                                            data-on-text="{{ __('auto') }}" data-off-color="default"
                                            data-off-text="{{ __('manual') }}" data-size="small" value="1">
                                        <x-forms.error name="edited_job_approval" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full mt-4 mb-2 ml-2 d-flex justify-content-center items-center">
                            <button type="submit" class="btn btn-success" id="setting_button">
                                <span><i class="fas fa-sync"></i> {{ __('update') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <form id="" class="form-horizontal" action="{{ route('module.map.update') }}" method="POST">
                    @method('PUT')
                    @csrf
                    <div class="card-header">
                        <h3 class="card-title">{{ __('location_configuration') }}
                        </h3>
                    </div>
                    <!-- ============== for text =============== -->
                    <div id="text-card" class="card-body">
                        <div class="form-group row">
                            <div class="col-12 d-flex">
                                <x-forms.label name="map_show" required="true" class="d-block" />
                                <x-info-tip message="{{ __('Toggle between map and dropdown effortlessly in settings for convenience.') }}" />
                                <div class="col-sm-8">
                                    <input type="hidden" name="map_show" value="0" />
                                    <input type="checkbox" id="map_show"
                                        {{ config('templatecookie.map_show') ? 'checked' : '' }} name="map_show"
                                        data-bootstrap-switch data-on-color="success" data-on-text="{{ __('show') }}"
                                        data-off-color="default" data-off-text="{{ __('hide') }}" data-size="small"
                                        value="1">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="set_country_language_currency" class="col-sm-4">
                                <x-info-tip message="{{ __('set_automatically_ip_based_country_language_currency') }}" />
                            </x-forms.label>

                            <div class="col-sm-8">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input onclick="changeIpLocationoType()" type="radio" id="ip_enable"
                                        name="ip_based_location" class="custom-control-input" value="1"
                                        {{ config('templatecookie.set_ip_based_location') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="ip_enable">{{ __('enable') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input onclick="changeIpLocationoType()" type="radio" id="ip_disable"
                                        name="ip_based_location" class="custom-control-input" value="0"
                                        {{ config('templatecookie.set_ip_based_location') ? '' : 'checked' }}>
                                    <label class="custom-control-label" for="ip_disable">{{ __('disable') }}</label>
                                </div>
                                @error('ip_based_location')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <x-forms.label name="map_type" class="col-sm-4" />
                            <div class="col-sm-8">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input onclick="changeMapType()" type="radio" id="leaflet-map" name="map_type"
                                        class="custom-control-input" value="leaflet"
                                        {{ $setting->default_map == 'leaflet' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="leaflet-map">{{ __('leaflet') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input onclick="changeMapType()" type="radio" id="google-mapp" name="map_type"
                                        class="custom-control-input" value="google-map"
                                        {{ $setting->default_map == 'google-map' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="google-mapp">
                                        {{ __('google_map') }}
                                    </label>
                                </div>
                                @error('map_type')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div id="googlemap_key"
                            class="{{ $setting->default_map == 'google-map' ? '' : 'd-none' }} form-group row">
                            <x-forms.label name="your_google_map_key" class="col-sm-4" />
                            <div class="col-sm-8">
                                <input value="{{ $setting->google_map_key }}" name="google_map_key" type="text"
                                    class="form-control @error('google_map_key') is-invalid @enderror" autocomplete="off"
                                    placeholder="{{ __('your_google_map_key') }}">
                                @error('google_map_key')
                                    <span class="text-left invalid-feedback"
                                        role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <!-- default lat & long -->
                        <div class="form-group row align-items-center">
                            <x-forms.label name="default_latitude_longitude" class="col-sm-4 mt-3" />
                            <div class="col-sm-8">
                                <div>
                                    <input value="{{ $setting->default_lat }}" name="default_latitude" type="text"
                                        class="form-control @error('default_latitude') is-invalid @enderror"
                                        autocomplete="off" placeholder="{{ __('latitude') }}">
                                    @error('default_latitude')
                                        <span class="text-left invalid-feedback"
                                            role="alert"><span>{{ $message }}</span></span>
                                    @enderror
                                </div>
                                <div class="mt-2">
                                    <input value="{{ $setting->default_long }}" name="default_longitude" type="text"
                                        class="form-control @error('default_longitude') is-invalid @enderror"
                                        autocomplete="off" placeholder="{{ __('longitude') }}">
                                    @error('default_longitude')
                                        <span class="text-left invalid-feedback"
                                            role="alert"><span>{{ $message }}</span></span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- default lat & long end -->
                        <div class="form-group row">
                            <x-forms.label name="app_country_type" :required="true" class="col-sm-4" />
                            <div class="col-sm-8">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input onclick="changeCountryType()" type="radio" id="single-country-base"
                                        name="app_country_type" class="custom-control-input" value="single_base"
                                        {{ $setting->app_country_type == 'single_base' ? 'checked' : '' }}>
                                    <label class="custom-control-label"
                                        for="single-country-base">{{ __('single_base') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input onclick="changeCountryType()" type="radio" id="multiple-country-base"
                                        name="app_country_type" class="custom-control-input" value="multiple_base"
                                        {{ $setting->app_country_type == 'multiple_base' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="multiple-country-base">
                                        {{ __('multiple_base') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="select_country" :required="true" class="col-sm-4" />

                            <div class="{{ $setting->app_country_type == 'single_base' ? '' : 'd-none' }} col-sm-8"
                                id="app_countries">
                                <select name="app_country" class="custom-select select2bs4 mr-sm-2" id="">
                                    @foreach ($countries as $country)
                                        <option {{ $setting->app_country == $country->id ? 'selected' : '' }}
                                            value="{{ $country->id }}">
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-8 {{ $setting->app_country_type == 'multiple_base' ? '' : 'd-none' }}"
                                id="app_countries_multiple">
                                @php
                                    $active_countries = $countries
                                        ->where('status', 1)
                                        ->pluck('id')
                                        ->toArray();
                                @endphp
                                <select name="multiple_country[]" class="custom-select mr-sm-2 multiple_country"
                                    id="" multiple>
                                    <option value="">{{ __('select_one') }}</option>
                                    @foreach ($countries as $country)
                                        <option {{ in_array($country->id, $active_countries) ? 'selected' : '' }}
                                            value="{{ $country->id }}">
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row pb-3 mt-5">
                            <div class="offset-sm-4 col-sm-8">
                                <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                                    {{ __('update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <style>
        .ck-editor__editable_inline {
            min-height: 300px;
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

        .image-container {
            height: 300px;
            overflow: hidden;
            position: relative;
            border: 2px solid #ced4da;
            cursor: pointer;
        }
        .image-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-top: 0px solid transparent;
            border-left: 56px solid var(--main-color);
            border-bottom: 32px solid transparent;
            display: none;
        }
        .image-container input {
            display: none;
        }

        .image-container:has(input:checked) {
            border: 2px solid var(--main-color);
        }
        .image-container:has(input:checked)::after {
            display: block;
        }

        .full-image {
            width: 100%;
            height: auto;
            transition: transform 2s ease;
        }

        .image-container:hover .full-image {
            transform: translateY(calc(-100% + 300px));
        }


    </style>
@endsection

@section('script')
    <script>
        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));

        });
    </script>
    <script>
        function changeCountryType(value) {
            var value = $("[name='app_country_type']:checked").val();

            if (value == 'single_base') {
                $('#app_countries').removeClass('d-none');
                $('#app_countries_multiple').addClass('d-none');
            } else {
                $('#app_countries').addClass('d-none');
                $('#app_countries_multiple').removeClass('d-none');
            }
        }

        function changeMapType(value) {
            var value = $("[name='map_type']:checked").val();

            if (value == 'google-map') {
                $('#googlemap_key').removeClass('d-none');
            } else {
                $('#googlemap_key').addClass('d-none');
            }
        }
        $('.multiple_country').select2({
            theme: 'bootstrap4',
            multiple: true,
            placeholder: 'Select Your Country'
        })
    </script>
@endsection
