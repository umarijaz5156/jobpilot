@extends('backend.layouts.app')
@section('title')
    {{ __('tag_list') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-warning">
                    This list will be displayed on the job creation page. The company can select relevant job tags from a
                    list.
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header ">
                        <div class="d-flex flex-wrap justify-content-between">
                            <h3 class="card-title line-height-36">{{ __('tag_list') }}
                                ({{ count($tags) }})</h3>

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
                                    <th>{{ __('name') }}</th>
                                    <th>
                                        {{ __('show_popular_list') }}
                                        <span class="ml-2 btn m-0 p-0" data-toggle="tooltip" data-placement="right"
                                            title="{{ __('these_tags_will_show_the_user_as_a_popular_tag_suggestion_on_the_job_page') }}">
                                            <i class="text-danger text-lg fas fa-exclamation-circle"></i>
                                        </span>
                                    </th>
                                    @if (userCan('tags.update') || userCan('tags.delete'))
                                        <th width="10%">{{ __('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tags as $item)
                                    <tr>
                                        <td>
                                            <h5>{{ Str::ucfirst($item->name) }}</h5>
                                            <div>
                                                @foreach ($item->translations as $translation)
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
                                        <td tabindex="0">
                                            <a href="#">
                                                <label class="switch ">
                                                    <input data-id="{{ $item->id }}" type="checkbox"
                                                        class="success tag-status"
                                                        {{ $item->show_popular_list == 1 ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </a>
                                        </td>
                                        <td>
                                            @if (userCan('tags.update'))
                                                <a href="{{ route('tags.edit', $item->id) }}" class="btn bg-info"><i
                                                        class="fas fa-edit"></i></a>
                                            @endif
                                            @if (userCan('tags.delete'))
                                                <form action="{{ route('tags.destroy', $item->id) }}" method="POST"
                                                    class="d-inline">
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
                        <div class="col-3 m-auto pt-1">
                            {{ $tags->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                @if (!empty($tag) && userCan('tags.update'))
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title line-height-36">{{ __('edit') }} {{ 'tag' }}</h3>
                            <a href="{{ route('tags.index') }}"
                                class="btn bg-primary float-right d-flex align-items-center justify-content-center"><i
                                    class="fas fa-plus mr-1"></i>{{ __('create') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="pt-3 pb-4">
                                <form class="form-horizontal" action="{{ route('tags.update', $tag->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    @foreach ($app_language as $key => $language)
                                        @php
                                            $label = __('name') . ' ' . getLanguageByCodeInLookUp($language->code,$app_language);
                                            $name = "name_{$language->code}";
                                            $code = $tag->translations[$key]['locale'] ?? '';
                                            $data = $tag->translations->where('locale', $language->code)->first();
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
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-plus mr-1"></i>
                                            {{ __('save') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
                @if (empty($tag) && userCan('tags.create'))
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title line-height-36">{{ __('create') }} {{ 'tag' }}</h3>
                        </div>
                        <div class="card-body">
                            @if (userCan('tags.create'))
                                <form class="form-horizontal" action="{{ route('tags.store') }}" method="POST">
                                    @csrf
                                    @foreach ($app_language as $key => $language)
                                        @php
                                            $label = __('name') . ' ' . getLanguageByCodeInLookUp($language->code,$app_language);
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
                                    <div class="form-group">
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
                    <form action="{{ route('admin.tags.bulk.import') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-warning" role="alert">
                                Before importing, please download the example file and match the fields structure. If any
                                field data is missing, the system will generate it
                            </div>
                            <div class="form-group">
                                <label for="experience">{{ __('example_file') }}</label> <br>
                                <a href="/backend/dummy/tags_example.xlsx" target="_blank"
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
    <script>
        // tag status change call ajax
        $('.tag-status').on('change', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');

            var url = "{{ route('tags.status.change', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
                type: "POST",
                dataType: "json",
                url: url,
                data: {
                    'status': status,
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });
        });
    </script>
@endsection
