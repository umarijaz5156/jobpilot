@extends('backend.settings.setting-layout')

@section('title')
    {{ __('upgrade_guide') }}
@endsection

@section('website-settings')
    <!-- Application Information -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ __('Application Information') }}</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap table-bordered">
                            <tr>
                                <th>Current Version</th>
                                <th>{{ $data['app_version'] }}</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Server Information -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ __('Server Information') }}</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>{{ __('Current Version') }}</th>
                                    <th>{{ __('Required Version') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PHP version</td>
                                    <td>{{ $data['current_php_version'] }}</td>
                                    <td>{{ $data['minimum_php_version'] }}</td>
                                    <td>
                                        @if ($data['matched_php_requirement'])
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Laravel version </td>
                                    <td>{{ $data['current_laravel_version'] }}</td>
                                    <td>-</td>
                                    <td>
                                        @if ($data['matched_php_requirement'])
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>MYSQL</td>
                                    <td>{{ $data['current_mysql_version'] }}</td>
                                    <td>{{ $data['minimum_mysql_version'] }}</td>
                                    <td>
                                        @if ($data['matched_mysql_requirement'])
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PHP.ini Configuration -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ __('php.ini Configuration') }}</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap table-bordered">
                            <thead>
                                <tr>
                                    <th>Config Name </th>
                                    <th>{{ __('Current') }}</th>
                                    <th>{{ __('Recommended') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>file_uploads</td>
                                    <td>{{ $data['php_ini']['file_uploads'] ? __('On') : __('Off') }}</td>
                                    <td>{{ __('On') }}</td>
                                    <td>
                                        @if ($data['php_ini']['file_uploads'])
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>max_file_uploads</td>
                                    <td>{{ $data['php_ini']['max_file_uploads'] }}</td>
                                    <td>20+</td>
                                    <td>
                                        @if ($data['php_ini']['matched_upload_max_filesize'])
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>upload_max_filesize </td>
                                    <td>{{ $data['php_ini']['upload_max_filesize'] }}</td>
                                    <td>128M+</td>
                                    <td>
                                        @if ($data['php_ini']['matched_post_max_size'])
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>post_max_size</td>
                                    <td>{{ $data['php_ini']['post_max_size'] }}</td>
                                    <td>128M+</td>
                                    <td>
                                        @if ($data['php_ini']['file_uploads'])
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>allow_url_fopen</td>
                                    <td>{{ $data['php_ini']['allow_url_fopen'] ? __('On') : __('Off') }}</td>
                                    <td>{{ __('On') }}</td>
                                    <td>
                                        @if ($data['php_ini']['allow_url_fopen'])
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>max_execution_time</td>
                                    <td>{{ $data['php_ini']['max_execution_time'] }}</td>
                                    <td>600+</td>
                                    <td>
                                        @if ($data['php_ini']['max_execution_time'] == '-1' || $data['php_ini']['max_execution_time'] >= 600)
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>max_input_time</td>
                                    <td>{{ $data['php_ini']['max_input_time'] }}</td>
                                    <td>120+</td>
                                    <td>
                                        @if ($data['php_ini']['max_input_time'] == '-1' || $data['php_ini']['max_input_time'] >= 120)
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>max_input_vars</td>
                                    <td>{{ $data['php_ini']['max_input_vars'] }}</td>
                                    <td>1000+</td>
                                    <td>
                                        @if ($data['php_ini']['max_input_vars'] >= 1000)
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>memory_limit</td>
                                    <td>{{ $data['php_ini']['memory_limit'] == '-1' ? __('Unlimited') : $data['php_ini']['memory_limit'] }}
                                    </td>
                                    <td>256M+</td>
                                    <td>
                                        @if ($data['php_ini']['matched_memory_limit'])
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="green" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="rounded-full text-white ml-4">

                                                <svg style="margin-right: -10px" width="34" height="34"
                                                    xmlns="http://www.w3.org/2000/svg" fill="red" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Extension Information -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ __('Extension Configuration') }}</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap table-bordered">
                            <thead>
                                <tr>
                                    <th>Extension Name </th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['extension_permissions'] as $index => $extension_permission)
                                    <tr>
                                        <td>
                                            {{ $index }}
                                        </td>
                                        <td>
                                            @if ($extension_permission)
                                                <span class="rounded-full text-white ml-4">

                                                    <svg style="margin-right: -10px" width="34" height="34"
                                                        xmlns="http://www.w3.org/2000/svg" fill="green"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </span>
                                            @else
                                                <span class="rounded-full text-white ml-4">

                                                    <svg style="margin-right: -10px" width="34" height="34"
                                                        xmlns="http://www.w3.org/2000/svg" fill="red"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Extension Information -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ __('Filesystem Permissions') }}</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap table-bordered">
                            <thead>
                                <tr>
                                    <th>Extension Name</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['fileSystemPermissions'] as $index => $filePath)
                                    <tr>
                                        <td>
                                            {{ $index }}
                                        </td>
                                        <td>
                                            @if ($filePath)
                                                <span class="rounded-full text-white ml-4">

                                                    <svg style="margin-right: -10px" width="34" height="34"
                                                        xmlns="http://www.w3.org/2000/svg" fill="green"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </span>
                                            @else
                                                <span class="rounded-full text-white ml-4">

                                                    <svg style="margin-right: -10px" width="34" height="34"
                                                        xmlns="http://www.w3.org/2000/svg" fill="red"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
