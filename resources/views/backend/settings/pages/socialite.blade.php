@extends('backend.settings.setting-layout')
@section('title')
    {{ __('social_login_setting') }}
@endsection
@section('breadcrumbs')
    <div class="row mb-2 mt-4">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('social_login_setting') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('home') }}</a></li>
                <li class="breadcrumb-item">{{ __('settings') }}</li>
                <li class="breadcrumb-item active">{{ __('social_login_setting') }}</li>
            </ol>
        </div>
    </div>
@endsection
@section('website-settings')
    <div class="alert alert-warning mb-3">
        <h5>
            {{ __('enable_social_media_login_to_your_website') }}
        </h5>
        <hr class="my-2">
        {{ __('65_of_users_prefer_social_logins_but_60_believe_that_companies_offering_social_logging_are_more_up_to_date_and_innovative_not_only_do_they_benefit_the_user_they_benefit_your_brand_logic_and_these_statistics_dictate_that_social_login_is_a_no_brainer') }}
    </div>

    <div class="row">
        <div class="col-sm-6">
            {{-- Google Login Credential Setting --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title line-height-36">
                            {{ __('google_login_credential') }}
                            <a target="_blank"
                                href="https://developers.google.com/identity/sign-in/web/sign-in"><small>({{ __('get_help') }})</small></a>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('settings.social.login.update') }}" method="POST"
                        id="google-social-form">
                        @method('PUT')
                        @csrf
                        <input type="hidden" value="google" name="type">
                        <input type="hidden" value="{{ config('services.google.active') }}" name="status">
                        <div class="form-group row">
                            <x-forms.label name="Google Client Id" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ config('services.google.client_id') }}" name="google_client_id" type="text"
                                    class="form-control @error('google_client_id') is-invalid @enderror" autocomplete="off">
                                @error('google_client_id')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="Google Client Secret" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ config('services.google.client_secret') }}" name="google_client_secret"
                                    type="text" class="form-control @error('google_client_secret') is-invalid @enderror"
                                    autocomplete="off">
                                @error('google_client_secret')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="status" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input {{ config('services.google.active') ? 'checked' : '' }} type="checkbox"
                                    name="google" data-bootstrap-switch value="1">
                            </div>
                        </div>
                        @if (userCan('setting.update'))
                            <div class="form-group row">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                                        {{ __('Update') }}</button>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Facebook Login Credential Setting --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title line-height-36">
                            {{ __('facebook_login_credential') }}
                            <a target="_blank"
                                href="https://developers.facebook.com/docs/development/create-an-app/"><small>({{ __('get_help') }})</small></a>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('settings.social.login.update') }}" method="POST">
                        @method('PUT')
                        @csrf
                        <input type="hidden" value="facebook" name="type">
                        <div class="form-group row">
                            <x-forms.label name="Facebook Client Id" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ config('services.facebook.client_id') }}" name="facebook_client_id"
                                    type="text" class="form-control @error('facebook_client_id') is-invalid @enderror"
                                    autocomplete="off">
                                @error('facebook_client_id')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="Facebook Client Secret" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ config('services.facebook.client_secret') }}" name="facebook_client_secret"
                                    type="text"
                                    class="form-control @error('facebook_client_secret') is-invalid @enderror"
                                    autocomplete="off">
                                @error('facebook_client_secret')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="status" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input {{ config('services.facebook.active') ? 'checked' : '' }} type="checkbox"
                                    name="facebook" data-bootstrap-switch value="1">
                            </div>
                        </div>
                        @if (userCan('setting.update'))
                            <div class="form-group row">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                                        {{ __('Update') }}</button>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            {{-- Twitter Login Credential Setting --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title line-height-36">
                            {{ __('twitter_login_credential') }}
                            <a target="_blank"
                                href="https://developer.twitter.com/en/docs/apps/overview"><small>({{ __('get_help') }})</small></a>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('settings.social.login.update') }}" method="POST">
                        @method('PUT')
                        @csrf
                        <input type="hidden" value="twitter" name="type">
                        <div class="form-group row">
                            <x-forms.label name="Twitter Client Id" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ config('services.twitter.client_id') }}" name="twitter_client_id"
                                    type="text" class="form-control @error('twitter_client_id') is-invalid @enderror"
                                    autocomplete="off">
                                @error('twitter_client_id')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="Twitter Client Secret" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ config('services.twitter.client_secret') }}" name="twitter_client_secret"
                                    type="text"
                                    class="form-control @error('twitter_client_secret') is-invalid @enderror"
                                    autocomplete="off">
                                @error('twitter_client_secret')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="status" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input {{ config('services.twitter.active') ? 'checked' : '' }} type="checkbox"
                                    name="twitter" data-bootstrap-switch value="1">
                            </div>
                        </div>
                        @if (userCan('setting.update'))
                            <div class="form-group row">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                                        {{ __('Update') }}</button>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            {{-- Linkedin Login Credential Setting --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title line-height-36">
                            {{ __('linkedin_login_credential') }}
                            <a target="_blank"
                                href="https://www.linkedin.com/developers/tools/oauth"><small>({{ __('get_help') }})</small></a>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('settings.social.login.update') }}" method="POST">
                        @method('PUT')
                        @csrf
                        <input type="hidden" value="linkedin" name="type">
                        <div class="form-group row">
                            <x-forms.label name="Linkedin Client Id" class="col-sm-5" for="linkedin_client_id" />
                            <div class="col-sm-7">
                                <input value="{{ config('services.linkedin-openid.client_id') }}" name="linkedin_client_id"
                                    type="text" class="form-control @error('linkedin_client_id') is-invalid @enderror"
                                    autocomplete="off">
                                @error('linkedin_client_id')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="Linkedin Client Secret" class="col-sm-5" for="linkedin_client_secret" />
                            <div class="col-sm-7">
                                <input value="{{ config('services.linkedin-openid.client_secret') }}" name="linkedin_client_secret"
                                    type="text"
                                    class="form-control @error('linkedin_client_secret') is-invalid @enderror"
                                    autocomplete="off">
                                @error('linkedin_client_secret')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="status" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input {{ config('services.linkedin-openid.active') ? 'checked' : '' }} type="checkbox"
                                    name="linkedin" data-bootstrap-switch value="1">
                            </div>
                        </div>
                        @if (userCan('setting.update'))
                            <div class="form-group row">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                                        {{ __('Update') }}</button>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Github Login Credential Setting --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title line-height-36">
                            {{ __('github_login_credential') }}
                            <a target="_blank"
                                href="https://docs.github.com/en/developers/apps/building-oauth-apps/creating-an-oauth-app"><small>({{ __('get_help') }})</small></a>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('settings.social.login.update') }}" method="POST">
                        @method('PUT')
                        @csrf
                        <input type="hidden" value="github" name="type">
                        <div class="form-group row">
                            <x-forms.label name="Github Client Id" for="github_client_id" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ config('services.github.client_id') }}" name="github_client_id"
                                    type="text" class="form-control @error('github_client_id') is-invalid @enderror"
                                    autocomplete="off">
                                @error('github_client_id')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="Github Client Secret" for="github_client_secret" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input value="{{ config('services.github.client_secret') }}" name="github_client_secret"
                                    type="text"
                                    class="form-control @error('github_client_secret') is-invalid @enderror"
                                    autocomplete="off">
                                @error('github_client_secret')
                                    <span class="invalid-feedback" role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="status" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input {{ config('services.github.active') ? 'checked' : '' }} type="checkbox"
                                    name="github" data-bootstrap-switch value="1">
                            </div>
                        </div>
                        @if (userCan('setting.update'))
                            <div class="form-group row">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                                        {{ __('Update') }}</button>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        })

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
@endsection
