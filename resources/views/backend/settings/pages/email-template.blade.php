@extends('backend.settings.setting-layout')

@section('title')
    {{ __('website_settings') }}
@endsection

@section('breadcrumbs')
    <div class="row mb-2 mt-4">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('email_templates') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('home') }}</a></li>
                <li class="breadcrumb-item">{{ __('settings') }}</li>
                <li class="breadcrumb-item active">{{ __('email_templates') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('website-settings')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-3">
                <ul class="nav nav-pills flex-column">
                    @foreach ($email_templates as $key => $email_template)
                        @php
                            $type = $email_template->type ?? "new";
                        @endphp
                        <li class="nav-item border rounded mb-1">
                            <a class="nav-link {{ $loop->first  ? 'active' : '' }}" data-toggle="tab"
                                href="#{{ $type }}">{{ $email_template->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-12 col-sm-12 col-md-9">
                <div class="tab-content no-padding">
                    @foreach ($email_templates as $key => $email_template)
                        @include("backend.layouts.partials.email-template-repeater", [
                            "active" => $loop->first,
                            "id" => $email_template->id,
                            "name" => $email_template->name ?? "",
                            "type" => $email_template->type ?? "",
                            "subject" => $email_template->subject ?? "",
                            "message" => $email_template->message ?? "",
                            "flags" => getEmailTemplateFormatFlagsByType($email_template->type ?? "")
                        ])
                    @endforeach
                    {{-- note: developer use only. uncomment add new email type. --}}
                    {{-- @include("backend.layouts.partials.email-template-repeater", [
                        "is_new" => true
                    ]) --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <style>
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
    </style>
@endsection

@section('script')
    <script>
        (function ($) {
            "use strict"
            $(document).ready(function () {
                $(".classic-editor").map(function (i, elem) {
                    console.log(elem);
                    ClassicEditor.create(elem)
                        .then(editor => {
                            editor.ui.view.editable.element.style.height = '250px';
                        })
                        .catch(error => {
                            console.error(error);
                        });
                });
            });
        })(jQuery)
    </script>
@endsection
