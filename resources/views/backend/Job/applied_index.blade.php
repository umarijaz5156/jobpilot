@extends('backend.layouts.app')
@section('title')
    {{ __('applied_jobs') }}
@endsection
@section('content')
    @php
        $userr = auth()->user();
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h3 class="card-title line-height-36">{{ __('applied_jobs') }}</h3>
                        <div class="d-flex flex-column flex-md-row">
                            {{-- <button data-toggle="modal" data-target="#bulk_import_modal"
                                class="btn mx-md-1 my-1 my-md-0 bg-info"><i class="fas fa-plus mr-1"></i>
                                {{ __('bulk_import') }}
                            </button> --}}
                            {{-- <a href="{{ route('admin.job.bulk.index') }}" class="btn bg-info"><i class="fas fa-plus mr-1"></i>
                                {{ __('bulk_import') }}
                            </a> --}}
                            {{-- @if (request('title') ||
                                    request('job_category') ||
                                    request('job_type') ||
                                    request('experience') ||
                                    request('sort_by') ||
                                    request('filter_by'))
                                <a href="{{ route('job.index') }}" class="btn bg-danger"><i class="fas fa-times"></i>
                                    &nbsp;{{ __('clear') }}
                                </a>
                            @endif --}}
                        </div>
                    </div>
                </div>

                {{-- Filter  --}}
                <form id="formSubmit" action="{{ route('job.index') }}" method="GET" onchange="this.submit();">
                    <div class="card-body border-bottom row">
                        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
                            <label>{{ __('search') }}</label>
                            <input name="title" type="text" placeholder="{{ __('search') }}" class="form-control"
                                value="{{ request('title') }}">
                        </div>
                    </div>
                </form>
                <div class="card-body table-responsive p-0 m-0">
                    @include('backend.layouts.partials.message')
                    <div class="row">

                        <div class="col-sm-12">
                            <table class="ll-table table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th width="10%">{{ __('candidate') }}</th>
                                        <th width="10%">{{ __('company') }}</th>
                                        <th width="10%">{{ __('job') }}</th>
                                        <th width="10%">{{ __('cover_latter') }}</th>
                                        @if (userCan('job.update') || userCan('job.delete'))
                                            <th width="10%">{{ __('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($applied_jobs->count() > 0)
                                        @foreach ($applied_jobs as $job)
                                            <tr>
                                                    <td tabindex="0">
                                                        <a href="{{ route('candidate.show', $job->appliedcandidate->id) }}" class="company">
                                                            @if ($job->appliedcandidate->user->name)
                                                                <img src="{{ asset($job->appliedcandidate->photo) }}" alt="image">
                                                            @else
                                                                <x-svg.briefcase-logo />
                                                            @endif
                                                            <div>
                                                                <p>
                                                                    <span>{{ $job->appliedcandidate && $job->appliedcandidate->user ? $job->appliedcandidate->user->name : " " }}</span>
                                                                </p>
                                                            </div>
                                                        </a>
                                                    </td>
                                                    <td tabindex="0">
                                                        <a href="{{ route('company.show', $job->job->company->id) }}" class="company">
                                                            @if ($job->job->company)
                                                                <img src="{{ asset($job->job->company->logo_url) }}" alt="image">
                                                            @else
                                                                <x-svg.briefcase-logo />
                                                            @endif
                                                            <div>
                                                                <p>
                                                                    <span>{{ $job->job->company && $job->job->company->user ? $job->job->company->user->name : $job->job->company_name }}</span>
                                                                </p>
                                                            </div>
                                                        </a>
                                                    </td>
                                                    <td tabindex="0">
                                                        <a href="{{ route('job.show', $job->job->id) }}" class="company">
                                                            <p>{{ $job->job->title }}</p>
                                                        </a>
                                                    </td>
                                                    <td tabindex="0">
                                                        <a href="{{ route('job.show', $job->id) }}" class="company">
                                                            <p>{{ $job->cover_letter }}</p>
                                                        </a>
                                                    </td>
                                                <td>
                                                    <a data-toggle="tooltip" data-placement="top"
                                                        title="{{ __('details') }}"
                                                        href="{{ route('applied.job.show', $job->id) }}"
                                                        class="btn ll-btn ll-border-none">{{ __('view_details') }}
                                                        <x-svg.table-btn-arrow />
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">{{ __('no_data_found') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($applied_jobs->total() > $applied_jobs->perPage())
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $applied_jobs->links() }}
                        </div>
                    @endif
                </div>
            </div>
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
                <form action="{{ route('admin.job.bulk.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                            Before importing, please download the example file and match the fields structure. If any field
                            data is missing, the system will generate it
                        </div>
                        <div class="form-group">
                            <label for="experience">{{ __('example_file') }}</label> <br>
                            <a href="/backend/dummy/job_example.xlsx" target="_blank" class="btn btn-primary btn-block">
                                <i class="fas fa-download"></i>
                                {{ __('download') }} {{ __('example_file') }}
                            </a>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="company_id">{{ __('company') }}</label> <br>
                            <select required name="company"
                                class="form-control select2bs4 @error('company') is-invalid @enderror" id="experience">
                                <option value=""> {{ __('select') }} {{ __('company') }}</option>
                                @foreach ($companies as $company)
                                    <option {{ $company->id == old('company') ? 'selected' : '' }}
                                        value="{{ $company->id }}"> {{ $company->user->name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
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
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            validate();
            $('#title').keyup(validate);
        });

        function validate() {
            if ($('#title')?.val()?.length > 0) {
                $('#crossB').removeClass('d-none');
            } else {
                $('#crossB').addClass('d-none');
            }
        }

        function RemoveFilter(id) {
            $('#' + id).val('');
            $('#formSubmit').submit();
        }
    </script>
@endsection

@section('style')
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

        /* Style  radio button */
        .expired_radio::after {
            content: "";
            display: inline-block;
            border-radius: 50%;
            margin-right: 8px;
            background-color: red;
        }
    </style>
@endsection
