@extends('backend.layouts.app')
@section('title')
    {{ __('team_size_list') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-warning">
                    This list will be displayed on the company job page. The company can choose his team size from a
                    list.
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header ">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title line-height-36">{{ __('team_size_list') }}
                                ({{ count($team_sizes) }})</h3>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>{{ __('name') }}</th>
                                    @if (userCan('team_sizes.update') || userCan('team_sizes.delete'))
                                        <th width="10%">{{ __('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($team_sizes as $team)
                                    <tr>
                                        <td>
                                            <h5>{{ $team->name }}</h5>
                                            <div>
                                                @foreach ($team->translations as $translation)
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
                                        <td>
                                            @if (userCan('team_sizes.update'))
                                                <a href="{{ route('teamSize.edit', $team->id) }}" class="btn bg-info"><i
                                                        class="fas fa-edit"></i></a>
                                            @endif
                                            @if (userCan('team_sizes.delete'))
                                                <form action="{{ route('teamSize.destroy', $team->id) }}" method="POST"
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
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                @if (!empty($team_size) && userCan('team_sizes.update'))
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title line-height-36">{{ __('edit') }} {{ __('team_size') }}</h3>
                            <a href="{{ route('teamSize.index') }}"
                                class="btn bg-primary float-right d-flex align-items-center justify-content-center"><i
                                    class="fas fa-plus mr-1"></i>{{ __('create') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="pt-3 pb-4">
                                <form class="form-horizontal" action="{{ route('teamSize.update', $team_size->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    @foreach ($app_language as $key => $language)
                                        @php
                                            $label = __('name') . ' ' . getLanguageByCode($language->code);
                                            $name = "name_{$language->code}";
                                            $code = $team_size->translations[$key]['locale'] ?? '';
                                            $data = $team_size->translations->where('locale', $language->code)->first();
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
                @if (empty($team_size) && userCan('team_sizes.create'))
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title line-height-36">{{ __('create') }} {{ __('team_size') }}</h3>
                        </div>
                        <div class="card-body">
                            @if (userCan('job_role.create'))
                                <form class="form-horizontal" action="{{ route('teamSize.store') }}" method="POST">
                                    @csrf
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

            <div class="mt-3 d-flex overflow-auto justify-content-center">
                {{ $team_sizes->links() }}
            </div>
    </div>
@endsection
