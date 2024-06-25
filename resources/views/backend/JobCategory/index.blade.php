@extends('backend.layouts.app')
@section('title')
    {{ __('job_category_list') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-wrap justify-content-between">
                            <h3 class="card-title line-height-36">{{ __('job_category_list') }} ({{ count($jobCategories) }})
                            </h3>

                            <button data-toggle="modal" data-target="#bulk_import_modal" class="btn bg-info"><i
                                    class="fas fa-plus mr-1"></i>
                                {{ __('bulk_import') }}
                            </button>
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>{{ __('image') }}</th>
                                    <th>{{ __('icon') }}</th>
                                    <th>{{ __('name') }}</th>
                                    @if (userCan('job_category.update') || userCan('job_category.delete'))
                                        <th width="10%">{{ __('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jobCategories as $category)
                                    <tr>
                                        <td class="vertical-middle"><img src="{{ $category->image_url }}" alt="category"
                                                height="50px" width="50px"></td>
                                        <td class="vertical-middle">
                                            <i class="{{ $category->icon }}"></i>
                                        </td>
                                        <td class="vertical-middle">
                                            <h5>{{ $category->name }}</h5>
                                            <div>
                                                @foreach ($category->translations as $translation)
                                                    @if (app()->getLocale() == $translation->locale)
                                                    @else
                                                        <span
                                                            class="d-block"><b>{{ getLanguageByCodeInLookUp($translation->locale,$app_language) }}</b>:
                                                            {{ $translation->name }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="vertical-middle">
                                            @if (userCan('job_category.update'))
                                                <a href="{{ route('jobCategory.edit', $category->id) }}"
                                                    class="btn bg-info"><i class="fas fa-edit"></i></a>
                                            @endif
                                            @if (userCan('job_category.delete'))
                                                <form action="{{ route('jobCategory.destroy', $category->id) }}"
                                                    method="POST" class="d-inline">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button
                                                        onclick="return confirm('{{ __('are_you_sure_you_want_to_delete_this_item') }}');"
                                                        class="btn bg-danger"><i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            {{ __('no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    {{ $jobCategories->links() }}
                </div>
            </div>
            <div class="col-md-4">
                @if (!empty($jobCategory) && userCan('job_category.update'))
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title line-height-36">{{ __('edit') }} {{ 'category' }}</h3>
                            <a href="{{ route('jobCategory.index') }}"
                                class="btn bg-primary float-right d-flex align-items-center justify-content-center"><i
                                    class="fas fa-plus mr-1"></i>{{ __('create') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <form class="form-horizontal" action="{{ route('jobCategory.update', $jobCategory->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                @foreach ($app_language as $key => $language)
                                    @php
                                        $label = __('name') . ' ' . getLanguageByCode($language->code);
                                        $name = "name_{$language->code}";
                                        $code = $jobCategory->translations[$key]['locale'] ?? '';
                                        $data = $jobCategory->translations->where('locale', $language->code)->first();
                                        $value = $data ? $data->name : '';
                                    @endphp
                                    <div class="form-group">
                                        <x-forms.label :name="$label" for="name" :required="true" />
                                        <input id="name" type="text" name="{{ $name }}"
                                            placeholder="{{ __('name') }}" value="{{ $value }}"
                                            class="form-control @if ($errors->has($name)) is-invalid @endif">
                                        @if ($errors->has($name))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first($name) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                                <div class="form-group row">
                                    <label>{{ __('image') }}</label>
                                    <input name="image" class="form-control-file" autocomplete="image" type="file"
                                        id="image">
                                </div>
                                <div class="form-group row">
                                    <div>
                                        <label>{{ __('icon') }}</label>
                                    </div>
                                    <div class="col-sm-12" style="overflow-x: auto;">
                                        <input type="hidden" name="icon" id="icon"
                                            value="{{ $jobCategory->icon }}" />
                                        <div id="target"></div>
                                        @error('icon')
                                            <span class="invalid-feedback d-block"
                                                role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row m-auto">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-sync mr-1"></i>
                                        {{ __('save') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
                @if (empty($jobCategory) && userCan('job_category.create'))
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title line-height-36">{{ __('create') }} {{ 'category' }}</h3>
                        </div>
                        <div class="card-body">
                            @if (userCan('job_category.create'))
                                <form class="form-horizontal" action="{{ route('jobCategory.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    {{-- <div class="form-group row">
                                        <label for="name">
                                            {{ __('name') }} <x-forms.required />
                                        </label>
                                        <input id="name" type="text" name="name" placeholder="{{ __('name') }}"
                                            value="{{ old('name') }}"
                                            class="form-control @error('name') is-invalid @enderror">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div> --}}
                                    @foreach ($app_language as $key => $language)
                                        @php
                                            $label = __('name') . ' ' . getLanguageByCode($language->code);
                                            $name = "name_{$language->code}";
                                        @endphp
                                        <div class="form-group">
                                            <x-forms.label :name="$label" for="name" :required="true" />
                                            <input id="name" type="text" name="{{ $name }}"
                                                placeholder="{{ __('name') }}" value="{{ old('name') }}"
                                                class="form-control @if ($errors->has($name)) is-invalid @endif">
                                            @if ($errors->has($name))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first($name) }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                    <div class="form-group row">
                                        <label>{{ __('image') }}</label>
                                        <input name="image" class="form-control-file" autocomplete="image" type="file"
                                            id="image">
                                    </div>
                                    <div class="form-group row">
                                        <div>
                                            <label>{{ __('icon') }}
                                                <x-forms.required />
                                            </label>
                                        </div>
                                        <div style="overflow-x: auto;">
                                            <input type="hidden" name="icon" id="icon"
                                                value="{{ old('icon') }}" />
                                            <div id="target"></div>
                                            @error('icon')
                                                <span class="invalid-feedback d-block"
                                                    role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row m-auto">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-plus mr-1"></i>
                                            {{ __('save') }}
                                        </button>
                                    </div>
                                </form>
                            @else
                                <p>{{ __('dont_have_permission') }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="modal fade" id="bulk_import_modal" tabindex="-1" role="dialog"
            aria-labelledby="bulk_import_modalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{ __('bulk_import') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.job.category.bulk.import') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-warning" role="alert">
                                Before importing, please download the example file and match the fields structure. If any
                                field data is missing, the system will generate it
                            </div>
                            <div class="form-group">
                                <label for="experience">{{ __('example_file') }}</label> <br>
                                <a href="/backend/dummy/job_category_example.xlsx" target="_blank"
                                    class="btn btn-primary btn-block">
                                    <i class="fas fa-download"></i>
                                    {{ __('download') }} {{ __('example_file') }}
                                </a>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="experience">{{ __('choose_file') }}</label> <br>
                                <input type="file" class="form-control dropify" name="import_file"
                                    data-allowed-file-extensions='["csv", "xlsx","xls"]' accept=".csv,.xlsx,.xls"
                                    data-max-file-size="3M">
                                @error('import_file')
                                    <span class="invalid-feedback d-block" role="alert">{{ __($message) }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <!-- Bootstrap-Iconpicker -->
    <link rel="stylesheet"  href="{{ asset('backend') }}/plugins/bootstrap-iconpicker/dist/css/bootstrap-iconpicker.min.css" />
    <style>
        .vertical-middle {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('script')
    <!-- Bootstrap-Iconpicker Bundle -->
    <script type="text/javascript"
        src="{{ asset('backend') }}/plugins/bootstrap-iconpicker/dist/js/bootstrap-iconpicker.bundle.min.js"></script>
    <script type="text/javascript"
        src="{{ asset('backend') }}/plugins/bootstrap-iconpicker/dist/js/bootstrap-iconpicker.min.js"></script>

    <script>
        // iconpicker call
        $('#target').iconpicker({
            align: 'center', // Only in div tag
            arrowClass: 'btn-danger',
            arrowPrevIconClass: 'fas fa-angle-left',
            arrowNextIconClass: 'fas fa-angle-right',
            cols: 12,
            footer: true,
            header: true,
            icon: '{{ $jobCategory->icon ?? 'fas fa-bomb' }}',
            iconset: 'fontawesome5',
            labelHeader: '{0} of {1} pages',
            labelFooter: '{0} - {1} of {2} icons',
            placement: 'bottom', // Only in button tag
            rows: 4,
            search: true,
            searchText: 'Search',
            selectedClass: 'btn-success',
            unselectedClass: '',
        });

        $('#target').on('change', function(e) {
            $('#icon').val(e.icon)
        });

        $('#target').on('change', function(e) {
            $('#icon').val(e.icon)
        });
    </script>
@endsection
