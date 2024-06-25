@extends('backend.settings.setting-layout')

@section('title')
    {{ __('upgrade_guide') }}
@endsection

@section('website-settings')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title" style="line-height: 36px;">{{ __('upgrade_guide') }} (<strong>Current version {{ config('app.version') }}</strong>)</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <ul class="mb-0">
                    <li class="">
                        Applicaion current version <strong>{{ config('app.version') }}</strong>
                        <a target="_blank" href="https://templatecookie.helpcenter.guide/articles/changelog-83c890-86e41">Check here to see latest version</a>
                    </li>
                    <li class="">
                        Get the most recent product zip file by visiting this page
                        <a target="_blank" href="https://codecanyon.net/downloads">Check Here</a>
                    </li>
                    <li class="">Extract downloaded zip file and find app.zip file</li>
                    <li class="">Put that app.zip file in the server's application root directory.</li>
                    <li class="">Extract app.zip (it will replace the most recent update in your application).</li>
                    <li class="">Finally click the below "Upgrade Now" button</li>
                </ul>
            </div>
        </div>
        <div class="card-footer text-center">
            <form action="{{ route('settings.upgrade.apply') }}" method="POST">
                @csrf
                <button onclick="return confirm('Would you like to upgrade your application ?')" style="width: 250px;" type="submit" class="btn btn-primary">{{ __('upgrade_now') }}</button>
            </form>
        </div>
    </div>
@endsection

