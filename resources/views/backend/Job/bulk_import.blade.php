@extends('backend.layouts.app')
@section('title')
    {{ __('bulk_import') }}
@endsection
@section('content')
    @php
        $userr = auth()->user();
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title line-height-36">{{ __('bulk_import') }}</h3>
                    </div>
                </div>
                <div class="card-body table-responsive p-0 m-0">
                    @include('backend.layouts.partials.message')
                    <div class="row">
                        <div class="col-sm-12">
                            sdas
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
