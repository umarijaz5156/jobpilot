@extends('backend.layouts.app')
@section('title')
    {{ __('create') }} {{ __('job') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title line-height-36">{{ __('create') }} {{ __('job') }}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <form class="form-horizontal" id="myForm" action="{{ route('job.store') }}" method="POST">
                        @csrf
                        <div class="section pt-3" id="job-details">
                            <div class="card mb-0">
                                <div class="card-header">
                                    <div class="card-title">{{ __('job_details') }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="row form-group">
                                        <div class="col-12">
                                            <label for="title">
                                                {{ __('title') }}
                                                <span class="text-red font-weight-bold">*</span></label>
                                            <input id="title" type="text" name="title"
                                                placeholder="{{ __('title') }}"
                                                class="form-control @error('title') is-invalid @enderror"
                                                value="{{ old('title') }}" required>
                                            @error('title')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row" id="company-input-container">
                                        <div class="col-sm-12 form-group" id="company-input-select">
                                            <label for="company_id" id="company_label">
                                                {{ __('select') }} {{ __('company') }}
                                                <span class="text-red font-weight-bold">*</span></label>
                                            <select name="company_id"
                                                class="form-control select2bs4 @error('company_id') is-invalid @enderror"
                                                id="company_id" required>
                                                <option value=""> {{ __('Choose a company') }}</option>
                                                @foreach ($companies as $company)
                                                    <option {{ old('company_id') == $company->id ? 'selected' : '' }}
                                                        value="{{ $company->id }}"> {{ $company->user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('company_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-12 col-md-12 d-none mt-2" id="apply_email_div">
                                            <x-forms.label name="apply_email" for="apply_email" :required="true" />
                                            <input id="apply_email" type="email" name="apply_email"
                                                placeholder="{{ __('apply_email') }}"
                                                class="form-control @error('apply_email') is-invalid @enderror">
                                            @error('apply_email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>


                                        <div class="col-sm-12 form-group hidden" id="company_name_container">
                                            <x-forms.label name="company_name" for="company_name" :required="true">
                                                {{ __('company_name') }}
                                                <span class="text-red font-weight-bold hidden">*</span></x-forms.label>
                                            <input id="company_name" type="text" name="company_name"
                                                placeholder="{{ __('company_name') }}"
                                                class="form-control  @error('company_name') is-invalid @enderror"
                                                value="{{ old('company_name') }}">
                                            @error('company_name')
                                                <span class="invalid-feedback" style="display: block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>
                                        <div class="col-sm-12 form-group">
                                            <div class="form-check">
                                                <div class="icheck-success d-inline">
                                                    <input value="range" name="is_just_name" type="checkbox"
                                                        class="form-check-input" {{--                                                {{ old('is_just_name')  ? 'checked':'' }} --}} id="just_name">
                                                    <label class="form-check-label mr-5"
                                                        for="just_name">{{ __('create_a_job_without_comapany_account') }}</label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-12">
                                            <label for="category_id">
                                                {{ __('category') }}
                                                <span class="text-red font-weight-bold">*</span></label>
                                            <select name="category_id"
                                                class="form-control select2bs4 @error('category_id') is-invalid @enderror"
                                                id="category_id" required>
                                                <option value=""> {{ __('category') }}</option>
                                                @foreach ($job_category as $category)
                                                    <option {{ old('category_id') == $category->id ? 'selected' : '' }}
                                                        value="{{ $category->id }}"> {{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-md-6">
                                            <x-forms.label name="vacancies" for="vacancies" :required="true" />
                                            <input id="vacancies" type="text" name="vacancies"
                                                placeholder="{{ __('vacancies') }}"
                                                class="form-control @error('vacancies') is-invalid @enderror"
                                                value="{{ old('vacancies') }}" required>
                                            @error('vacancies')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <x-forms.label name="deadline" for="deadline" :required="true" />
                                            <input id="deadline" type="text" name="deadline"
                                                placeholder="MM/DD/YYYY"
                                                class="form-control @error('deadline') is-invalid @enderror"
                                                value="{{ old('deadline') }}" required>
                                            @error('deadline')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="section pt-3" id="location">
                            <div class="card mb-0">
                                {{-- <div class="card-header">
                                    <div class="card-title">
                                        {{ __('location') }}
                                        <span class="text-red font-weight-bold">*</span>
                                        <small class="h6">
                                            ({{ __('click_to_add_a_pointer') }})
                                        </small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <x-website.map.map-warning />
                                    @php
                                        $map = $setting->default_map;
                                    @endphp
                                    <div id="google-map-div" class="{{ $map == 'google-map' ? '' : 'd-none' }}">
                                        <input id="searchInput" class="mapClass" type="text"
                                            placeholder="Enter a location">
                                        <div class="map mymap" id="google-map"></div>
                                    </div>
                                    <div class="{{ $map == 'leaflet' ? '' : 'd-none' }}">
                                        <input type="text" autocomplete="off" id="leaflet_search"
                                            placeholder="{{ __('enter_city_name') }}" class="form-control" /> <br>
                                        <div id="leaflet-map"></div>
                                    </div>
                                    @error('location')
                                        <span class="ml-3 text-md text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                @php
                                    $location = session()->get('location');
                                @endphp
                                <div class="card-footer location_footer d-none">
                                    <span>
                                        <img src="{{ asset('frontend/assets/images/loader.gif') }}" alt="loading"
                                            width="50px" height="50px" class="loader_position d-none">
                                    </span>
                                    <div class="location_secion">
                                        {{ __('country') }}: <span
                                            class="location_country">{{ $location && array_key_exists('country', $location) ? $location['country'] : '-' }}</span>
                                        <br>
                                        {{ __('full_address') }}: <span
                                            class="location_full_address">{{ $location && array_key_exists('exact_location', $location) ? $location['exact_location'] : '-' }}</span>
                                    </div>
                                </div> --}}
                                @if (config('templatecookie.map_show'))
                                <div class="card-header">
                                    <div class="card-title">
                                        {{ __('location') }}
                                        <span class="text-red font-weight-bold">*</span>
                                        <small class="h6">
                                            ({{ __('click_to_add_a_pointer') }})
                                        </small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <x-website.map.map-warning />
                                    @php
                                        $map = $setting->default_map;
                                    @endphp
                                    <div id="google-map-div" class="{{ $map == 'google-map' ? '' : 'd-none' }}">
                                        <input id="searchInput" class="mapClass" type="text"
                                            placeholder="Enter a location">
                                        <div class="map mymap" id="google-map"></div>
                                    </div>
                                    <div class="{{ $map == 'leaflet' ? '' : 'd-none' }}">
                                        <input type="text" autocomplete="off" id="leaflet_search"
                                            placeholder="{{ __('enter_city_name') }}" class="form-control" /> <br>
                                        <div id="leaflet-map"></div>
                                    </div>
                                    @error('location')
                                        <span class="ml-3 text-md text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                @php
                                    $location = session()->get('location');
                                @endphp
                                <div class="card-footer location_footer d-none">
                                    <span>
                                        <img src="{{ asset('frontend/assets/images/loader.gif') }}" alt="loader"
                                            width="50px" height="50px" class="loader_position d-none">
                                    </span>
                                    <div class="location_secion">
                                        {{ __('country') }}: <span
                                            class="location_country">{{ $location && array_key_exists('country', $location) ? $location['country'] : '-' }}</span>
                                        <br>
                                        {{ __('full_address') }}: <span
                                            class="location_full_address">{{ $location && array_key_exists('exact_location', $location) ? $location['exact_location'] : '-' }}</span>
                                    </div>
                                </div>
                                @else
                                <div class="card-header border-0">
                                    {{ __('location') }}
                                    <span class="text-red font-weight-bold">*</span>
                                </div>
                                <div class="card-body pt-0 row">
                                    <div class="col-12">
                                        @livewire('country-state-city')
                                        @error('location')
                                            <span class="ml-3 text-md text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="section pt-3" id="salary-details">
                            <div class="card mb-0">
                                <div class="card-header">
                                    <div class="card-title">{{ __('salary_details') }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-6 form-check" style="padding-left: 26px;">
                                            <div class="icheck-success d-inline">
                                                <input checked onclick="salaryModeChange('range')" value="range"
                                                    name="salary_mode" type="radio" class="form-check-input"
                                                    id="salary_rangee">
                                                <label class="form-check-label mr-5"
                                                    for="salary_rangee">{{ __('salary_range') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 form-check" style="padding-left: 26px;">
                                            <input onclick="salaryModeChange('custom')" value="custom" name="salary_mode"
                                                type="radio" class="form-check-input" id="custom_salary">
                                            <label class="form-check-label mr-5"
                                                for="custom_salary">{{ __('custom_salary') }}</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6 form-group salary_range_part">
                                            <label for="min_salary">
                                                {{ __('min_salary') }} ({{ $currency_symbol }})
                                            </label>
                                            <input id="min_salary" type="number" name="min_salary"
                                                placeholder="{{ __('min_salary') }}"
                                                class="form-control @error('min_salary') is-invalid @enderror"
                                                value="{{ old('min_salary') }}">
                                            @error('min_salary')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6 form-group salary_range_part">
                                            <label for="max_salary">
                                                {{ __('max_salary') }} ({{ $currency_symbol }})
                                            </label>
                                            <input id="max_salary" type="number" name="max_salary"
                                                placeholder="{{ __('max_salary') }}"
                                                class="form-control @error('max_salary') is-invalid @enderror"
                                                value="{{ old('max_salary') }}">
                                            @error('max_salary')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6 form-group d-none" id="custom_salary_part">
                                            <label for="custom_salary">
                                                {{ __('custom_salary') }}
                                            </label>
                                            <input id="custom_salary" type="text" name="custom_salary"
                                                class="form-control @error('custom_salary') is-invalid @enderror"
                                                value="{{ old('custom_salary', 'Competitive') }}">
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label for="salary_type">
                                                {{ __('salary_type') }}
                                                <span class="text-red font-weight-bold">*</span>
                                            </label>
                                            <select name="salary_type"
                                                class="form-control select2bs4 @error('salary_type') is-invalid @enderror"
                                                id="salary_type" required>
                                                @foreach ($salary_types as $salary_type)
                                                    <option {{ $salary_type->id == old('salary_type') ? 'selected' : '' }}
                                                        value="{{ $salary_type->id }}"> {{ $salary_type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('salary_type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="section pt-3" id="attributes">
                            <div class="card mb-0">
                                <div class="card-header">
                                    <div class="card-title">{{ __('attributes') }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <x-forms.label name="experience" for="experience" :required="true" />
                                            <select name="experience"
                                                class="form-control select2bs4 @error('experience') is-invalid @enderror"
                                                id="experience" required>
                                                <option value=""> {{ __('experience') }}</option>
                                                @foreach ($experiences as $experience)
                                                    <option {{ $experience->id == old('experience') ? 'selected' : '' }}
                                                        value="{{ $experience->id }}"> {{ $experience->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('experience')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <x-forms.label name="job_role" for="role_id" :required="true" />
                                            <select name="role_id"
                                                class="form-control select2bs4 @error('role_id') is-invalid @enderror"
                                                id="role_id" required>
                                                <option value=""> {{ __('job_role') }}</option>
                                                @foreach ($job_roles as $role)
                                                    <option {{ $role->id == old('role_id') ? 'selected' : '' }}
                                                        value="{{ $role->id }}">
                                                        {{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('role_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <x-forms.label name="education" for="education" :required="true" />
                                            <select id="education" name="education"
                                                class="form-control select2bs4 @error('education') is-invalid @enderror"
                                                required>
                                                <option value=""> {{ __('education') }}</option>
                                                @foreach ($educations as $education)
                                                    <option {{ $education->id == old('education') ? 'selected' : '' }}
                                                        value="{{ $education->id }}">{{ $education->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('education')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <x-forms.label name="job_type" for="job_type" :required="true" />
                                            <select name="job_type"
                                                class="form-control select2bs4 @error('job_type') is-invalid @enderror"
                                                id="job_type" required>
                                                @foreach ($job_types as $job_type)
                                                    <option {{ $job_type->id == old('job_type') ? 'selected' : '' }}
                                                        value="{{ $job_type->id }}">{{ $job_type->name }} </option>
                                                @endforeach
                                            </select>
                                            @error('job_type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-12 mb-2">
                                            <x-forms.label name="tags" for="tags" :required="true" />
                                            <select name="tags[]"
                                                class="form-control select2tags @error('tags') is-invalid @enderror"
                                                multiple id="tags">
                                                @foreach ($tags as $tag)
                                                    <option
                                                        {{ old('tags') ? (in_array($tag->id, old('tags')) ? 'selected' : '') : '' }}
                                                        value="{{ $tag->id }}">
                                                        {{ $tag->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tags')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <x-forms.label name="benefits" for="benefits" :required="true" />
                                            <select name="benefits[]"
                                                class="form-control @error('benefits') is-invalid @enderror"
                                                id="benefits" multiple>
                                                @foreach ($benefits as $benefit)
                                                    <option
                                                        {{ old('benefits') ? (in_array($benefit->id, old('benefits')) ? 'selected' : '') : '' }}
                                                        value="{{ $benefit->id }}">
                                                        {{ $benefit->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('benefits')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <x-forms.label name="skills" for="skill" :required="true" />
                                            <select name="skills[]"
                                                class="form-control @error('skills') is-invalid @enderror" id="skills"
                                                multiple>
                                                @foreach ($skills as $skill)
                                                    <option
                                                        {{ old('skills') ? (in_array($skill->id, old('skills')) ? 'selected' : '') : '' }}
                                                        value="{{ $skill->id }}">
                                                        {{ $skill->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('skills')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="section pt-3" id="applicant-options">
                            <div class="card mb-0">
                                <div class="card-header">
                                    <div class="card-title">{{ __('applicant_options') }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-md-12">
                                            <x-forms.label name="receive_applications" for="apply_on"
                                                :required="true" />
                                            <select name="apply_on"
                                                class="form-control @error('apply_on') is-invalid @enderror"
                                                id="apply_on" required>
                                                <option {{ old('apply_on') ? '' : 'selected' }} value=""
                                                    class="d-none">
                                                    {{ __('select_one') }}</option>
                                                <option {{ old('apply_on') == 'app' ? 'selected' : '' }} value="app"
                                                    selected>
                                                    {{ __('on_our_platform') }}</option>
                                                <option {{ old('apply_on') == 'email' ? 'selected' : '' }}
                                                    value="email">
                                                    {{ __('on_your_email_address') }}</option>
                                                <option {{ old('apply_on') == 'custom_url' ? 'selected' : '' }}
                                                    value="custom_url">
                                                    {{ __('on_a_custom_url') }}</option>
                                            </select>
                                            @error('apply_on')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-12 col-md-12 d-none mt-2" id="apply_email_div">
                                            <x-forms.label name="apply_email" for="apply_email" :required="true" />
                                            <input id="apply_email" type="email" name="apply_email"
                                                placeholder="{{ __('apply_email') }}"
                                                class="form-control @error('apply_email') is-invalid @enderror">
                                            @error('apply_email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-12 col-md-12 d-none mt-2" id="apply_url_div">
                                            <x-forms.label name="apply_url" for="apply_url" :required="true" />
                                            <input id="apply_url" type="url" name="apply_url"
                                                placeholder="{{ __('apply_url') }}"
                                                class="form-control @error('apply_url') is-invalid @enderror">
                                            @error('apply_url')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="section pt-3" id="promote">
                            <div class="card mb-0">
                                <div class="card-header">
                                    <div class="card-title">{{ __('promote') }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="row p-md-4">
                                        <div class="col-md-4 form-check">
                                            <div class="icheck-success d-inline">
                                                <input value="featured" name="badge" type="checkbox"
                                                    class="form-check-input" id="featured"
                                                    {{ old('badge') == 'featured' ? 'checked' : '' }}>
                                                <label class="form-check-label mr-5"
                                                    for="featured">{{ __('featured') }}</label>
                                            </div>
                                            @error('featured')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 form-check">
                                            <div class="icheck-success d-inline">
                                                <input value="highlight" name="badge" type="checkbox"
                                                    class="form-check-input" id="highlight"
                                                    {{ old('badge') == 'highlight' ? 'checked' : '' }}>
                                                <label class="form-check-label mr-5"
                                                    for="highlight">{{ __('highlight') }}</label>
                                            </div>
                                            @error('highlight')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 form-check">
                                            <div class="icheck-success d-inline">
                                                <input value="1" name="is_remote" type="checkbox"
                                                    class="form-check-input" id="is_remote"
                                                    {{ old('is_remote') ? 'checked' : '' }}>
                                                <label class="form-check-label mr-5"
                                                    for="is_remote">{{ __('remote') }}</label>
                                            </div>
                                            @error('is_remote')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section pt-3" id="description">
                            <div class="card mb-0">
                                <div class="card-header">
                                    <div class="card-title">{{ __('description') }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="row form-group">
                                        <div class="col-12">
                                            <label for="image_ckeditor" class="pt-2">{{ __('description') }}<span
                                                    class="text-red font-weight-bold">*</span></label>
                                            <textarea name="description" id="image_ckeditor" class="form-control @error('description') is-invalid @enderror"
                                                cols="30" rows="10">{{ old('description') }}</textarea>
                                            <div id="error-message" style="color: red;"></div>
                                            @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="section pt-3">
                            <button type="submit" class="btn btn-block bg-success">
                                <i class="fas fa-plus mr-1"></i>
                                {{ __('save') }}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 pt-3">
                    <div class="card tc-sticky-sidebar">
                        <div class="card-body">
                            <h5 class="mb-4">Job Information</h5>
                            <div class="tc-vertical-step">
                                <ul class="list-unstyled">
                                    <li>
                                        <a href="#job-details" class="step-menu active">Job Details</a>
                                    </li>
                                    <li>
                                        <a href="#location" class="step-menu">Location</a>
                                    </li>
                                    <li>
                                        <a href="#salary-details" class="step-menu">Salary Details</a>
                                    </li>
                                    <li>
                                        <a href="#attributes" class="step-menu">Attributes</a>
                                    </li>
                                    <li>
                                        <a href="#applicant-options" class="step-menu">Applicant Options</a>
                                    </li>
                                    <li>
                                        <a href="#promote" class="step-menu">Promote</a>
                                    </li>
                                    <li>
                                        <a href="#description" class="step-menu">Description</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('style')
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/bootstrap-datepicker.min.css">

    <style>
        .ck-editor__editable_inline {
            min-height: 400px;
        }

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

        .tc-vertical-step,
        .tc-vertical-step-link {
            position: relative;
        }

        .tc-vertical-step ul:before,
        .tc-vertical-step-link ul:before {
            content: "";
            position: absolute;
            left: 5px;
            top: 10px;
            width: 2px;
            height: 100%;
            background: #dfe3e8;
        }

        .tc-vertical-step ul li:not(:last-child),
        .tc-vertical-step-link ul li:not(:last-child) {
            padding-bottom: 1rem;
        }

        .tc-vertical-step ul li a,
        .tc-vertical-step-link ul li a {
            position: relative;
            display: block;
            color: #454f5b;
            padding-left: 26px;
            -webkit-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }

        .tc-vertical-step ul li a:before,
        .tc-vertical-step-link ul li a:before {
            content: "";
            position: absolute;
            left: 1px;
            top: 50%;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
            width: 10px;
            height: 10px;
            background: #dfe3e8;
            border-radius: 50%;
            z-index: 2;
        }

        .tc-vertical-step ul li a:after,
        .tc-vertical-step-link ul li a:after {
            content: "";
            position: absolute;
            left: -3px;
            top: 50%;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1px solid var(--main-color);
            z-index: 1;
            opacity: 0;
        }

        .step-menu.active:before,
        .step-menu.active:before {
            background-color: var(--main-color) !important;
        }

        .step-menu.active:after,
        .step-menu.active:after {
            opacity: 1;
        }

        .tc-sticky-sidebar {
            position: sticky !important;
            top: 1rem;
            -webkit-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
            z-index: 8;
        }
    </style>
    <!-- >=>Leaflet Map<=< -->
    <x-map.leaflet.map_links />
    <x-map.leaflet.autocomplete_links />
    @include('map::links')
@endsection

@section('script')
    @livewireScripts
    <script>
        $(document).ready(function() {
            $('.select21').select2();
        });
        window.addEventListener('render-select2', event => {
            console.log('fired');
            $('.select21').select2();
        })
    </script>
    @stack('js')
    <script>
        const stepMenus = document.querySelectorAll('.step-menu');
        const sections = document.querySelectorAll('.section');


        function isElementInViewport(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)
            );
        }

        function updateActiveStepMenuItem() {
            let activeSection = null;

            for (let i = 0; i < sections.length; i++) {
                if (isElementInViewport(sections[i])) {
                    activeSection = sections[i];
                    break;
                }
            }

            stepMenus.forEach(menu => menu.classList.remove('active'));

            if (activeSection) {
                const targetId = activeSection.id;
                const activeMenuItem = document.querySelector(`.step-menu[href="#${targetId}"]`);
                if (activeMenuItem) {
                    activeMenuItem.classList.add('active');
                }
            }
        }

        function handleStepMenuItemClick(event) {
            event.preventDefault();

            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        stepMenus.forEach(menuItem => {
            menuItem.addEventListener('click', handleStepMenuItemClick);
        });

        window.addEventListener('scroll', updateActiveStepMenuItem);

        updateActiveStepMenuItem();
    </script>
    <script type="text/javascript"
        src="{{ asset('backend') }}/plugins/flagicon/dist/js/bootstrap-iconpicker.bundle.min.js"></script>
    <!-- Custom Script -->
    @if (app()->getLocale() == 'ar')
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.ar.min.js
                                                                                                                                                                                                            ">
        </script>
    @endif
    <script>
        $('#benefits').select2({
            theme: 'bootstrap4',
            tags: true,
            placeholder: 'Select Benefits'
        });

        $('#skills').select2({
            theme: 'bootstrap4',
            tags: true,
            placeholder: 'Select Skill'
        });

        $('#tags').select2({
            theme: 'bootstrap4',
            tags: true,
            placeholder: 'Select Tag',
        });

        //init datepicker
        $(document).ready(function() {
            var dateToday = new Date();
            $('#deadline').datepicker({
                format: "yyyy-mm-dd",
                minDate: dateToday,
                startDate: dateToday,
                todayHighlight: true,
                isRTL: "{{ app()->getLocale() == 'ar' ? true : false }}",
                language: "{{ app()->getLocale() }}",
            });
        });
    </script>

    <script>
        var salary_mode = "{!! old('salary_mode') !!}";

        if (salary_mode) {
            salaryModeChange(salary_mode);
        }

        function salaryModeChange(param) {
            var value = param;

            if (value === 'range') {
                $('#custom_salary_part').addClass('d-none');
                $('.salary_range_part').removeClass('d-none');
                $('#salary_rangee').prop('checked', true)
                $('#custom_salary').prop('checked', false)
            } else {
                $('#custom_salary_part').removeClass('d-none');
                $('.salary_range_part').addClass('d-none');
                $('#salary_rangee').prop('checked', false)
                $('#custom_salary').prop('checked', true)
            }
        }

        // condidtion based apply on show hide
        $('#apply_on').on('change', function() {
            var applyOn = $('#apply_on').val();
            var applyEmail = $('#apply_email_div');
            var applyUrl = $('#apply_url_div');
            applyOn == 'email' ? applyEmail.removeClass("d-none") : applyEmail.addClass("d-none");
            applyOn == 'custom_url' ? applyUrl.removeClass("d-none") : applyUrl.addClass("d-none");
        });
    </script>
    <script>
        $("#just_name").change(function() {
            if (this.checked) {
                $('#company_name_container').show();
                $('#company_label').find('span').hide();
                $('#company-input-select').hide();
                $('#company_name_container').find('span').show();
                // Set the company_name field as required and remove required from company_id
                $('#company_name').prop('required', true);
                $('#company_id').prop('required', false);
                $('#company_id').val(null);
            } else {
                $('#company_name_container').hide();
                $('#company-input-select').show();
                $('#company_label').find('span').show();
                $('#company-input-container').show();

                // Set the company_id field as required and remove required from company_name
                $('#company_name').prop('required', false);
                $('#company_id').prop('required', true);
            }
        });
    </script>
    {{-- textarea validation  --}}
    <script>
        document.getElementById("myForm").addEventListener("submit", function(event) {
            var descriptionInput = document.getElementById("image_ckeditor");
            var errorMessage = document.getElementById("error-message");

            if (descriptionInput.value.trim() === "") {
                errorMessage.textContent = "Description is required.";
                event.preventDefault();
            } else {
                errorMessage.textContent = ""; // Clear any previous error message
            }
        });
    </script>
    @include('map::set-leafletmap')
    @include('map::set-googlemap')
@endsection
