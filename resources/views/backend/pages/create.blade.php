@extends('backend.layouts.app')
@section('title')
    {{ __('create_page') }}
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
                <li class="breadcrumb-item active">{{ __('create_page') }}</li>
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
                        <h3 class="card-title line-height-36">{{ __('create_page') }}</h3>
                        <a href="{{ route('settings.pages.index') }}" class="btn bg-primary float-right d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('back') }}
                        </a>
                    </div>
                    <div class="row pt-3 pb-4">
                        <div class="col-md-6 offset-md-3">
                            <form class="form-horizontal" action="{{ route('settings.pages.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <x-forms.label name="title" class="col-sm-2" />
                                    <div class="col-sm-10">
                                        <input type="text" name="title" id="title"
                                            class="form-control @error('title') is-invalid @enderror"
                                            value="{{ old('title') }}" placeholder="{{ __('title') }}" required>
                                        @error('title')
                                            <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <x-forms.label name="page_url" class="col-sm-2" />
                                    <div class="col-sm-10">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">{{ route('website.home') }}/</span>
                                            </div>
                                            <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="slug" required>
                                          </div>
                                          <small class="mt-0 py-0">{{ __('use_character_number_hypen_only') }}</small>

                                        @error('slug')
                                            <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <x-forms.label name="footer_column_position" class="col-sm-2" />
                                    <div class="col-sm-10">
                                        <select name="footer_column_position" id="footer_column_position"
                                            class="form-control @error('footer_column_position') is-invalid @enderror" required>
                                            <option value="1" {{ old('footer_column_position') == 1 ? 'selected' : '' }}>{{__('company') }}</option>
                                            <option value="2" {{ old('footer_column_position') == 2 ? 'selected' : '' }}>{{__('candidate') }}</option>
                                            <option value="3" {{ old('footer_column_position') == 3 ? 'selected' : '' }}>{{__('employer') }}</option>
                                            <option value="4" {{ old('footer_column_position') == 4 ? 'selected' : '' }}>{{__('support') }}</option>
                                        </select>
                                        @error('footer_column_position')
                                            <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                        @enderror
                                    </div>     
                                </div>
                                
                                <div class="form-group row">
                                    <x-forms.label name="content" class="col-sm-2" />
                                    <div class="col-sm-10">
                                        <textarea id="image_ckeditor" type="text" class="form-control" name="content"
                                        placeholder="{{ __('enter') }}  {{ __('content') }}">{{ old('content') }}</textarea>
                                        @error('content')
                                            <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <h4>{{ __('seo') }} {{ __('content') }}</h4>
                                <hr>
                                <div class="form-group row">
                                    <x-forms.label name="meta_title" class="col-sm-2" :required="false"/>
                                    <div class="col-sm-10">
                                        <input type="text" name="meta_title" id="meta_title"
                                            class="form-control @error('meta_title') is-invalid @enderror"
                                            value="{{ old('meta_title') }}" placeholder="{{ __('meta_title') }}">
                                        @error('meta_title')
                                            <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <x-forms.label name="meta_description" class="col-sm-2" :required="false"/>
                                    <div class="col-sm-10">
                                        <textarea type="text" class="form-control" name="meta_description" rows="8"
                                        placeholder="{{ __('enter') }}  {{ __('meta_description') }}">{{ old('meta_description') }}</textarea>
                                        @error('meta_description')
                                            <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <x-forms.label name="image" class="col-sm-2" :required="false"/>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control dropify"
                                        data-default-file="" name="meta_image"
                                        data-allowed-file-extensions='["jpg", "jpeg","png"]'
                                        accept="image/png, image/jpg, image/jpeg" data-max-file-size="3M">
                                        @error('meta_image')
                                            <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-plus"></i>&nbsp;{{ __('create') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('style')
    <link rel="stylesheet" href="{{ asset('backend/plugins/dropify/css/dropify.min.css') }}">
    <style>
        .ck-editor__editable_inline {
            min-height: 260px;
        }
    </style>
@endsection


