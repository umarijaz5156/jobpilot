@extends('backend.layouts.app')
@section('title')
    {{ __('post_list') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-12 col-md-2 p-1">
                                <h3 class="card-title line-height-36">{{ __('city_list') }}</h3>
                            </div>
                            <div class="col align-self-end p-1">
                                @if (userCan('post.create'))
                                    <a href="{{ route('location.city.create') }}"
                                        class="btn bg-primary float-right d-flex align-items-center mx-1 justify-content-center">
                                        <i class="fas fa-plus"></i>&nbsp;{{ __('create_city') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <!-- filter -->
                        <form id="formSubmit" action="" method="GET" onchange="this.submit();">
                            <div class="card-body border-bottom row">
                                <div class="col-12 col-md-3">
                                    <label>{{ __('search') }}</label>
                                    <input name="keyword" type="text" placeholder="{{ __('title') }}"
                                        class="form-control" value="{{ request('keyword') }}">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>{{ __('state') }}</label>
                                    <select name="state" class="select2bs4  form-control w-100-p">
                                        <option value="" {{ !request('state') ? 'selected' : '' }}>
                                            {{ __('all') }}
                                        </option>
                                        @foreach ($categories as $category)
                                            <option {{ request('state') == $category->id ? 'selected' : '' }}
                                                value="{{ $category->id }}">
                                                {{ Str::ucfirst($category->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3 col-md-2">
                                    <label></label>
                                    <button type="submit" class="mt-2 form-control btn btn-primary">
                                        {{ __('search') }}
                                    </button>
                                </div>
                            </div>

                        </form>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline"
                                    role="grid" aria-describedby="example1_info">
                                    <thead>
                                        <tr role="row" class="text-center">
                                            <th class="sorting_desc" tabindex="0" aria-controls="example1" rowspan="1"
                                                colspan="1"
                                                aria-label="Rendering engine: activate to sort column ascending"
                                                aria-sort="descending" width="50%">{{ __('city_lga') }}</th>
                                            <th class="sorting_desc" tabindex="0" aria-controls="example1" rowspan="1"
                                                colspan="1"
                                                aria-label="Rendering engine: activate to sort column ascending"
                                                aria-sort="descending" width="20%">{{ __('state') }}</th>
                                            @if (userCan('post.edit') || userCan('post.delete'))
                                                <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                                    colspan="1" aria-label="CSS grade: activate to sort column ascending"
                                                    width="100px"> {{ __('actions') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($posts->count() > 0)
                                            @foreach ($posts as $post)
                                                <tr role="row" class="odd">
                                                    <td class="sorting_1 text-center" tabindex="0">{{ $post->name }}
                                                    </td>
                                                    <td class="sorting_1 text-center" tabindex="0">
                                                        {{ Str::ucfirst($post->state->name) }}</td>
                                                    @if (userCan('post.update') || userCan('post.delete'))
                                                        <td class="sorting_1 text-center" tabindex="0">
                                                            @if (userCan('post.update'))
                                                                <a data-toggle="tooltip" data-placement="top"
                                                                    title="{{ __('edit_city') }}"
                                                                    href="{{ route('location.city.edit', $post->id) }}"
                                                                    class="btn bg-info"><i class="fas fa-edit"></i></a>
                                                            @endif
                                                            @if (userCan('post.delete'))
                                                                <form
                                                                    action="{{ route('location.city.destroy', $post->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @method('DELETE')
                                                                    @csrf
                                                                    <button data-toggle="tooltip" data-placement="top"
                                                                        title="{{ __('delete_post') }}"
                                                                        onclick="return confirm('{{ __('Are you sure want to delete this item?') }}');"
                                                                        class="btn bg-danger"><i
                                                                            class="fas fa-trash"></i></button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center" colspan="4">{{ __('no_data_found') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                @if (request('perpage') != 'all' && $posts->total() > $posts->count())
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-center">
                                            {{ $posts->appends(['state' => request('state')])->links() }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('style')
    <link rel="stylesheet"
        href="{{ asset('backend') }}/plugins/bootstrap-iconpicker/dist/css/bootstrap-iconpicker.min.css" />
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

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
    <script type="text/javascript"
        src="{{ asset('backend') }}/plugins/bootstrap-iconpicker/dist/js/bootstrap-iconpicker.bundle.min.js"></script>
    <script src="{{ asset('backend') }}/plugins/select2/js/select2.full.min.js"></script>
    <script>
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })


        $('#target').iconpicker({
            align: 'left', // Only in div tag
            arrowClass: 'btn-danger',
            arrowPrevIconClass: 'fas fa-angle-left',
            arrowNextIconClass: 'fas fa-angle-right',
            cols: 15,
            footer: true,
            header: true,
            icon: 'flag-icon-gb',
            iconset: 'flagicon',
            labelHeader: '{0} of {1} pages',
            labelFooter: '{0} - {1} of {2} icons',
            placement: 'bottom', // Only in button tag
            rows: 5,
            search: true,
            searchText: 'Search',
            selectedClass: 'btn-success',
            unselectedClass: ''
        });

        $('#target').on('change', function(e) {
            $('#icon').val(e.icon)
        });
    </script>
@endsection
