@extends('backend.settings.setting-layout')

@section('title')
    {{ __('preferences') }}
@endsection

@section('breadcrumbs')
    <div class="row mb-2 mt-4">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('preferences') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('home') }}</a></li>
                <li class="breadcrumb-item">{{ __('settings') }}</li>
                <li class="breadcrumb-item active">{{ __('preferences') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('website-settings')
<div class="card">
    <div class="card-header">{{ __('contact_info') }}</div>
    <div class="card-body">
        <form action="{{ route('settings.preference.update') }}" method="post">
            @method('put')
            @csrf
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>{{ __('phone_no') }}:</label>
                        <input type="text" class="form-control @error('footer_phone_no') is-invalid @enderror p-2"
                            name="footer_phone_no" value="{{ $cms_setting?->footer_phone_no }}">
                        @error('footer_phone_no')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="alert alert-warning">
                        {{ __('leave_the_social_media_input_field_empty_to_remove_the_link_from_website') }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>{{ __('facebook') }}:</label>
                        <input type="text"
                            class="form-control @error('footer_facebook_link') is-invalid @enderror p-2"
                            name="footer_facebook_link" value="{{ $cms_setting->footer_facebook_link }}">
                        @error('footer_facebook_link')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>{{ __('instagram') }}:</label>
                        <input type="text"
                            class="form-control @error('footer_instagram_link') is-invalid @enderror p-2"
                            name="footer_instagram_link" value="{{ $cms_setting->footer_instagram_link }}">
                        @error('footer_instagram_link')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>{{ __('twitter') }}:</label>
                        <input type="text"
                            class="form-control @error('footer_twitter_link') is-invalid @enderror p-2"
                            name="footer_twitter_link" value="{{ $cms_setting->footer_twitter_link }}">
                        @error('footer_twitter_link')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>{{ __('youtube') }}:</label>
                        <input type="text"
                            class="form-control @error('footer_youtube_link') is-invalid @enderror p-2"
                            name="footer_youtube_link" value="{{ $cms_setting->footer_youtube_link }}">
                        @error('footer_youtube_link')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12">
                    @if (userCan('setting.update'))
                        <div class="form-group row text-center justify-content-center">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-sync"></i>
                                {{ __('update') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row justify-content-between">
    <div class="col-md-6">
        <div class="card" id="mode_settings">
            <div class="card-header">
                <h3 class="card-title">
                    {{ __('application_mode') }}
                </h3>
            </div>
            <div class="card-body applied-job-on">
                <form class="form-horizontal" action="{{ route('settings.system.mode.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div
                    class="d-flex justify-content-between">
                        <div class="col-md-4">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="live-mode" name="app_mode" class="custom-control-input" value="live" {{ config('app.mode') == 'live' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="live-mode">
                                    {{ __('live_mode') }}
                                </label>
                              </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="maintenance-mode" name="app_mode" class="custom-control-input" value="maintenance" {{ config('app.mode') == 'maintenance' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="maintenance-mode">
                                    {{ __('maintenance_mode') }}
                                </label>
                              </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="comingsoon-mode" name="app_mode" class="custom-control-input" value="comingsoon" {{ config('app.mode') == 'comingsoon' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="comingsoon-mode">
                                    {{ __('coming_soon_mode') }}
                                </label>
                              </div>
                        </div>
                    </div>
                    <div class="col-sm-12 mt-3">
                        @if (userCan('setting.update'))
                            <div class="form-group row text-center justify-content-center">
                                <button type="submit" class="btn btn-success" id="setting_button">
                                    <i class="fas fa-sync"></i>
                                    {{ __('update') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <div class="card" id="mode_settings">
            <div class="card-header">
                <h3 class="card-title">
                    {{ __('job_deadline_max_expiration_days') }}
                </h3>
            </div>
            <div class="card-body applied-job-on">
                <form class="form-horizontal" action="{{ route('settings.system.jobdeadline.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div
                    class="d-flex justify-content-between">
                        <div class="col-md-4">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="job_deadline_30" name="job_deadline_expiration_limit" class="custom-control-input" value="30" {{ $setting->job_deadline_expiration_limit == 30 ? 'checked' : '' }}>
                                <label class="custom-control-label" for="job_deadline_30">
                                    30 {{ __('days') }}
                                </label>
                              </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="job_deadline_60" name="job_deadline_expiration_limit" class="custom-control-input" value="60" {{ $setting->job_deadline_expiration_limit == 60 ? 'checked' : '' }}>
                                <label class="custom-control-label" for="job_deadline_60">
                                    60 {{ __('days') }}
                                </label>
                              </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="job_deadline_90" name="job_deadline_expiration_limit" class="custom-control-input" value="90" {{ $setting->job_deadline_expiration_limit == 90 ? 'checked' : '' }}>
                                <label class="custom-control-label" for="job_deadline_90">
                                    90 {{ __('days') }}
                                </label>
                              </div>
                        </div>
                    </div>
                    <div class="col-sm-12 mt-3">
                        @if (userCan('setting.update'))
                            <div class="form-group row text-center justify-content-center">
                                <button type="submit" class="btn btn-success" id="setting_button">
                                    <i class="fas fa-sync"></i>
                                    {{ __('update') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card" id="mode_settings">
            <div class="card-header">
                <h3 class="card-title">
                    {{ __('pay_per_job') }} {{ __('and') }} {{ __('options') }}
                </h3>
            </div>
            <div class="card-body applied-job-on">
                <form action="{{ route('settings.payperjob.update') }}" method="post">
                    @method('put')
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ __('per_job_price') }}:</label>
                                <input type="text"
                                    class="form-control @error('per_job_price') is-invalid @enderror p-2"
                                    name="per_job_price" value="{{ $setting->per_job_price }}">
                                @error('per_job_price')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ __('status') }}:</label> <br>
                                <input {{ $setting->per_job_active == 1 ? 'checked' : '' }} type="checkbox"
                                name="status" data-bootstrap-switch value="1" data-on-text="{{ __('on') }}"
                                data-off-color="default" data-on-color="success" data-off-text="{{ __('off') }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ __('featured_job_price') }}:</label>
                                <input type="text"
                                    class="form-control @error('featured_job_price') is-invalid @enderror p-2"
                                    name="featured_job_price" value="{{ $setting->featured_job_price }}">
                                @error('featured_job_price')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ __('hightlight_job_price') }}:</label>
                                <input type="text"
                                    class="form-control @error('highlight_job_price') is-invalid @enderror p-2"
                                    name="highlight_job_price" value="{{ $setting->highlight_job_price }}">
                                @error('highlight_job_price')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ __('featured_jobs_duration') }}:</label>
                                <input min="0" type="number"
                                    class="form-control @error('featured_job_days') is-invalid @enderror p-2"
                                    name="featured_job_days" value="{{ $setting->featured_job_days }}">
                                @error('featured_job_days')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="text-danger">{{ __('job_post_will_not_be_expired_anytime_soon_if_you_put') }} 0</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ __('highlight_jobs_duration') }}:</label>
                                <input min="0" type="number"
                                    class="form-control @error('highlight_job_days') is-invalid @enderror p-2"
                                    name="highlight_job_days" value="{{ $setting->highlight_job_days }}">
                                @error('highlight_job_days')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="text-danger">{{ __('job_post_will_not_be_expired_anytime_soon_if_you_put') }} 0</small>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            @if (userCan('setting.update'))
                                <div class="form-group row text-center justify-content-center">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-sync"></i>
                                        {{ __('update') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $("#app_debug").bootstrapSwitch();
        $("#google_analytics").bootstrapSwitch();
        $("#language_changing").bootstrapSwitch();
        $("#search_engine_indexing").bootstrapSwitch();
        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        })


        $("input[data-bootstrap-switch]").on('switchChange.bootstrapSwitch', function(event, state) {

            let oldData = event.target.attributes.oldvalue.value;
            let newData = event.currentTarget.checked ? 1 : 0;
            let button = event.target.attributes.button.value;

            if (oldData == newData) {
                $('#' + button).prop('disabled', true);
            } else {
                $('#' + button).prop('disabled', false);
            }
        });
    </script>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
@endsection
