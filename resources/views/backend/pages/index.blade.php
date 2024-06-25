@extends('backend.layouts.app')

@section('title')
    {{ __('pages') }}
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
                <li class="breadcrumb-item active">{{ __('pages') }}</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ __('pages') }}</h3>
                        <a href="{{ route('settings.pages.create') }}" class="btn bg-primary float-right d-flex align-items-center justify-content-center">
                            <i class="fas fa-plus"></i>
                            {{ __('create') }}
                        </a>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('title') }}</th>
                                    <th>{{ __('page_url') }}</th>
                                    <th>{{ __('show_on_header') }}</th>
                                    <th>{{ __('show_on_footer') }}</th>
                                    <th width="15%">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pages as $key => $page)
                                    <tr>
                                        <td>{{ $page->title }}</td>
                                        <td><a href="{{ route('showCustomPage', $page->slug) }}" target="_blank">{{ route('showCustomPage', $page->slug) }}</a></td>
                                        <td tabindex="0">
                                            <a href="javascript:void(0)">
                                                <label class="switch ">
                                                    <input data-id="{{ $page->id }}" type="checkbox" class="success show_in_header" {{ $page->show_header ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </a>
                                        </td>
                                        <td tabindex="0">
                                            <a href="javascript:void(0)">
                                                <label class="switch ">
                                                    <input data-userid="{{ $page->id }}" type="checkbox" class="success show_in_footer" {{ $page->show_footer ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </a>
                                        </td>
                                        <td class="d-flex  align-items-center">
                                            <a href="{{ route('settings.pages.edit', $page->id) }}" class="btn btn-info mt-0 mr-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('settings.pages.delete', $page->id) }}"
                                                class="d-inline" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <button data-toggle="tooltip" data-placement="top"
                                                    title="{{ __('delete') }}"
                                                    onclick="return confirm('{{ __('are_you_sure_want_to_delete_this_item') }}');"
                                                    class="btn bg-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            @if (userCan('setting.pages.create'))
                                                <x-admin.not-found word="{{ __('page') }}" route="setting.pages.create" />
                                            @else
                                                <x-admin.not-found word="page" route="" />
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('style')
    <link rel="stylesheet"
        href="{{ asset('backend') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 35px;
            height: 19px;
        }

        /* Hide default HTML checkbox */
        .switch input {
            display: none;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 15px;
            width: 15px;
            left: 3px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input.success:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(15px);
            -ms-transform: translateX(15px);
            transform: translateX(15px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('backend') }}/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script>
        $('.show_in_header').on('change', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('settings.pages.header.status') }}',
                data: {
                    'status': status,
                    'id': id
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });
        });

        $('.show_in_footer').on('change', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('userid');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('settings.pages.footer.status') }}',
                data: {
                    'status': status,
                    'id': id
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });
        });





    </script>
@endsection
