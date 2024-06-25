@extends('backend.layouts.app')
@section('title')
    {{ __('edit') }} {{ __('job') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title line-height-36">{{ __('edit') }} {{ __('job') }}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <form class="form-horizontal" action="{{ route('job.update', $job->id) }}" method="POST">
                        @method('PUT')
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
                                                value="{{ old('title', $job->title) }}" placeholder="{{ __('title') }}"
                                                class="form-control @error('title') is-invalid @enderror">
                                            @error('title')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Just Company Name  --}}
                                    <div class="row form-group" id="company-input-container">
                                        <div class="col-sm-6 form-group {{ !$job->company_id ? 'hidden' : '' }}"
                                            id="company-input-select">
                                            <label for="company_id" id="company_label">
                                                {{ __('select') }} {{ __('company') }}
                                                <span class="text-red font-weight-bold">*</span></label>
                                            <select name="company_id"
                                                class="form-control select2bs4 @error('company_id') is-invalid @enderror"
                                                id="company_id">
                                                <option value=""> {{ __('Select One') }}</option>
                                                @foreach ($companies as $company)
                                                    <option {{ $job->company_id == $company->id ? 'selected' : '' }}
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
                                        <div class="col-md-6 form-group {{ $job->company_id ? 'hidden' : '' }}"
                                            id="company_name_container">
                                            <label for="company_name"> :required="true"
                                                {{ __('company_name') }}
                                                <span class="text-red font-weight-bold ">*</span></label>
                                            <input id="company_name" type="text" name="company_name"
                                                placeholder="Company name"
                                                class="form-control @error('company_name') is-invalid @enderror"
                                                value="{{ $job->company_name }}">
                                            @error('company_name')
                                                <span class="invalid-feedback" style="display: block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <div class="form-check" style="margin-top: 38px">
                                                <div class="icheck-success d-inline">
                                                    <input value="range" name="is_just_name" type="checkbox"
                                                        class="form-check-input" {{ $job->company_id ? '' : 'checked' }}
                                                        {{--                                                {{ old('is_just_name')  ? 'checked':'' }} --}} id="just_name">
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
                                                id="category_id">
                                                <option value=""> {{ __('category') }}</option>
                                                @foreach ($job_category as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $category->id == $job->category_id ? 'selected' : '' }}>
                                                        {{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('company_id')
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
                                                value="{{ old('vacancies', $job->vacancies) }}"
                                                placeholder="{{ __('enter_vacancies') }}"
                                                class="form-control @error('vacancies') is-invalid @enderror">
                                            @error('vacancies')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-12 col-md-6">
                                            <label for="deadline">
                                                {{ __('deadline') }}
                                            </label>
                                            <input id="deadline" type="text" name="deadline" placeholder="MM/DD/YYYY"
                                                value="{{ old('deadline', $job->deadline) }}"
                                                class="form-control @error('deadline') is-invalid @enderror">
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
                            <div class="card">
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
                                    </div>
                                @else
                                    @php
                                        session([
                                            'selectedCountryId' => null,
                                            'selectedStateId' => null,
                                            'selectedCityId' => null,
                                        ]);
                                        session([
                                            'selectedCountryId' => $job->country,
                                            'selectedStateId' => $job->region,
                                            'selectedCityId' => $job->district,
                                        ]);
                                    @endphp
                                    <div class="card-header border-0">
                                        {{ __('location') }}
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
                                        <div class="col-lg-6 form-check" style="padding-left: 26px;">
                                            <div class="icheck-success d-inline">
                                                <input checked onclick="salaryModeChange('range')" value="range"
                                                    name="salary_mode" type="radio" class="form-check-input"
                                                    id="salary_rangee">
                                                <label class="form-check-label mr-5"
                                                    for="salary_rangee">{{ __('salary_range') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 form-check" style="padding-left: 26px;">
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
                                                value="{{ old('min_salary', $job->min_salary) }}"
                                                class="form-control @error('min_salary') is-invalid @enderror">
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
                                                value="{{ old('max_salary', $job->max_salary) }}" class="form-control">
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
                                                value="{{ old('custom_salary', $job->custom_salary) }}">
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label for="salary_type">
                                                {{ __('salary_type') }}
                                                <span class="text-red font-weight-bold">*</span>
                                            </label>
                                            <select name="salary_type"
                                                class="form-control select2bs4 @error('salary_type') is-invalid @enderror"
                                                id="salary_type">
                                                @foreach ($salary_types as $salary_type)
                                                    <option
                                                        {{ $salary_type->id == $job->salary_type_id ? 'selected' : '' }}
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
                                            <label for="experience">
                                                {{ __('experience') }}
                                            </label>
                                            <select name="experience"
                                                class="form-control select2bs4 @error('experience') is-invalid @enderror"
                                                id="experience">
                                                @foreach ($experiences as $experience)
                                                    <option {{ $experience->id == $job->experience_id ? 'selected' : '' }}
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
                                            <label for="role_id">
                                                {{ __('job_role') }}
                                                <span class="text-red font-weight-bold">*</span></label>
                                            <select name="role_id"
                                                class="form-control select2bs4 @error('role_id') is-invalid @enderror"
                                                id="role_id">
                                                <option value=""> {{ __('job_role') }}</option>
                                                @foreach ($job_roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        @if ($job->role_id == $role->id) selected @endif>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <label for="education">
                                                {{ __('education') }}
                                            </label>
                                            <select id="education" name="education"
                                                class="form-control select2bs4 @error('education') is-invalid @enderror">
                                                @foreach ($educations as $education)
                                                    <option {{ $education->id == $job->education_id ? 'selected' : '' }}
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
                                            <label for="job_type">
                                                {{ __('job_type') }}
                                            </label>
                                            <select name="job_type"
                                                class="form-control select2bs4 @error('job_type') is-invalid @enderror"
                                                id="job_type">
                                                @foreach ($job_types as $job_type)
                                                    <option {{ $job_type->id == $job->job_type_id ? 'selected' : '' }}
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
                                                        @foreach ($job->tags as $job_tag)
                                                {{ $job_tag->id == $tag->id ? 'selected' : '' }} @endforeach
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
                                                        @foreach ($job->benefits as $job_benefit)
                                                {{ $job_benefit->id == $benefit->id ? 'selected' : '' }} @endforeach
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
                                        <div class="col-md-12">
                                            <x-forms.label name="skills" for="skill" :required="true" />
                                            <select name="skills[]"
                                                class="form-control @error('skills') is-invalid @enderror" id="skills"
                                                multiple>
                                                @foreach ($skills as $skill)
                                                    <option
                                                        @foreach ($job->skills as $job_skill)
                                                {{ $job_skill->id == $skill->id ? 'selected' : '' }} @endforeach
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
                                            <x-forms.label name="receive_applications" for="apply_on" :required="true" />
                                            <select name="apply_on"
                                                class="form-control @error('apply_on') is-invalid @enderror"
                                                id="apply_on">
                                                <option value="" {{ $job->apply_on === '' ? 'selected' : '' }}>
                                                    {{ __('select_one') }}</option>
                                                <option value="app" {{ $job->apply_on === 'app' ? 'selected' : '' }}>
                                                    {{ __('on_our_platform') }}</option>
                                                <option value="email"
                                                    {{ $job->apply_on === 'email' ? 'selected' : '' }}>
                                                    {{ __('on_your_email_address') }}</option>
                                                <option value="custom_url"
                                                    {{ $job->apply_on === 'custom_url' ? 'selected' : '' }}>
                                                    {{ __('on_a_custom_url') }}</option>
                                            </select>
                                            @error('apply_on')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-12 col-md-12 {{ $job->apply_on === 'email' ? '' : 'd-none' }}"
                                            id="apply_email_div">
                                            <x-forms.label name="apply_email" for="apply_email" :required="true" />
                                            <input id="apply_email" type="email" name="apply_email"
                                                placeholder="{{ __('apply_email') }}" value="{{ $job->apply_email }}"
                                                class="form-control @error('apply_email') is-invalid @enderror">
                                            @error('apply_email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ __($message) }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-12 col-md-12 {{ $job->apply_on == 'custom_url' ? '' : 'd-none' }}"
                                            id="apply_url_div">
                                            <x-forms.label name="apply_url" for="apply_url" :required="true" />
                                            <input id="apply_url" type="url" name="apply_url"
                                                placeholder="{{ __('apply_url') }}" value="{{ $job->apply_url }}"
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
                                                    {{ $job->featured ? 'checked' : '' }}>
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
                                                    {{ $job->highlight ? 'checked' : '' }}>
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
                                                    {{ $job->is_remote ? 'checked' : '' }}>
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
                                                cols="30" rows="10">{{ old('description', $job->description) }}</textarea>
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
                            <button type="submit"
                                class="btn bg-success d-flex align-items-center justify-content-center">
                                <i class="fas fa-sync mr-1"></i> {{ __('save') }}
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
    <script src="{{ asset('frontend/assets/js/bootstrap-datepicker.min.js') }}"></script>
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
            });
        });

        var salary_mode = "{!! old('salary_mode', $job->salary_mode) !!}";

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

    {{-- Leaflet  --}}
    @include('map::set-edit-leafletmap', ['lat' => $lat, 'long' => $long])

    <!-- ============== google map ========= -->
    <x-website.map.google-map-check />
    <script>
        function initMap() {
            // Get Google Maps API key from Laravel settings
            var token = "{{ $setting->google_map_key }}";

            // Get latitude and longitude data or use defaults
            var oldlat = {!! $job->lat ? $job->lat : $setting->default_lat !!};
            var oldlng = {!! $job->long ? $job->long : $setting->default_long !!};

            // Create a Google Map instance

            const map = new google.maps.Map(document.getElementById("google-map"), {
                zoom: 5,
                center: {
                    lat: oldlat,
                    lng: oldlng
                },
            });

            const image = "https://gisgeography.com/wp-content/uploads/2018/01/map-marker-3-116x200.png";

            // Create a marker on the map
            const beachMarker = new google.maps.Marker({

                draggable: true,
                position: {
                    lat: oldlat,
                    lng: oldlng
                },
                map,
                // icon: image
            });

            // Function to handle updating the map marker and fetching location data
            function handleMapUpdate(lat, lng) {
                // Update the position of the existing marker with the new latitude and longitude
                beachMarker.setPosition({
                    lat: lat,
                    lng: lng
                });

                // Fetch location information using Google Maps Geocoding API
                axios.post(
                    `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${token}`
                ).then((data) => {
                    // Check if there's an error message in the API response
                    if (data.data.error_message) {
                        toastr.error(data.data.error_message, 'Error!');
                        toastr.error('Your location is not set due to an incorrect API key.', 'Error!');
                    }

                    // Extract relevant location data from the API response
                    const total = data.data.results.length;
                    let amount = '';
                    if (total > 4) {
                        amount = total - 3;
                    }
                    const result = data.data.results.slice(amount);
                    let country = '';
                    let region = '';
                    let district = '';

                    // Iterate through the results to extract country, region, and district
                    for (let index = 0; index < result.length; index++) {
                        const element = result[index];

                        if (element.types[0] == 'country') {
                            country = element.formatted_address;
                        }
                        if (element.types[0] == 'administrative_area_level_1') {
                            const str = element.formatted_address;
                            const first = str.split(' ').shift();
                            region = first;
                        }
                        if (element.types[0] == 'administrative_area_level_2') {
                            const str = element.formatted_address;
                            const first = str.split(' ').shift();
                            district = first;
                        }
                    }

                    // Create a form and populate it with location data
                    var form = new FormData();
                    form.append('lat', lat);
                    form.append('lng', lng);
                    form.append('country', country);
                    form.append('region', region);
                    form.append('exact_location', district + "," + region + "," + country);

                    // Store location data in session
                    setLocationSession(form);

                    // Update the UI with the fetched location information
                    $('.location_country').text(country);
                    $('.location_full_address').text(district + "," + region);
                    $('.loader_position').addClass('d-none');
                    $('.location_secion').removeClass('d-none');
                    $('.location_footer').removeClass('d-none');
                }).catch((error) => {
                    // Handle errors and display an error message
                    toastr.error('Something Went Wrong', 'Error!');
                    console.log(error);
                });
            }

            // Listen for a click event on the map
            google.maps.event.addListener(map, 'click',
                function(event) {
                    // Show loader and hide location section
                    $('.loader_position').removeClass('d-none');
                    $('.location_secion').addClass('d-none');

                    // Get latitude and longitude from the event
                    pos = event.latLng;
                    beachMarker.setPosition(pos);
                    let lat = beachMarker.position.lat();
                    let lng = beachMarker.position.lng();

                    // Make a request to Google Geocoding API
                    axios.post(
                        `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${token}`
                    ).then((data) => {
                        // Check for API error message
                        if (data.data.error_message) {
                            toastr.error(data.data.error_message, 'Error!');
                            toastr.error('Your location is not set because of a wrong API key.', 'Error!');
                        }

                        // Process geocoding results
                        const total = data.data.results.length;
                        let amount = '';
                        if (total > 4) {
                            amount = total - 3;
                        }
                        const result = data.data.results.slice(amount);
                        let country = '';
                        let region = '';
                        let district = '';

                        // Extract relevant location information from results
                        for (let index = 0; index < result.length; index++) {
                            const element = result[index];

                            if (element.types[0] == 'country') {
                                country = element.formatted_address;
                            }
                            if (element.types[0] == 'administrative_area_level_1') {
                                const str = element.formatted_address;
                                const first = str.split(' ').shift();
                                region = first;
                            }
                            if (element.types[0] == 'administrative_area_level_2') {
                                const str = element.formatted_address;
                                const first = str.split(' ').shift();
                                district = first;
                            }
                        }

                        // Create a FormData object with location details
                        var form = new FormData();
                        form.append('lat', lat);
                        form.append('lng', lng);
                        form.append('country', country);
                        form.append('region', region);
                        form.append('exact_location', district + "," + region + "," + country);

                        // Set location session data
                        setLocationSession(form);

                        // Update UI elements with location information
                        $('.location_country').text(country);
                        $('.location_full_address').text(district + "," + region);
                        $('.loader_position').addClass('d-none');
                        $('.location_secion').removeClass('d-none');
                    });
                });


            // Listen for a dragend event on the marker
            google.maps.event.addListener(beachMarker, 'dragend',
                function() {
                    // Show loader and hide location section
                    $('.loader_position').removeClass('d-none');
                    $('.location_secion').addClass('d-none');

                    // Get latitude and longitude from the beachMarker
                    let lat = beachMarker.position.lat();
                    let lng = beachMarker.position.lng();

                    // Send a geocoding request to Google Maps API
                    axios.post(
                        `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${token}`
                    ).then((data) => {
                        // Check if there's an error message in the response
                        if (data.data.error_message) {
                            // Display error messages using toastr library
                            toastr.error(data.data.error_message, 'Error!');
                            toastr.error('Your location is not set because of a wrong API key.', 'Error!');
                        }

                        // Calculate how many results to skip
                        const total = data.data.results.length;
                        let amount = '';
                        if (total > 4) {
                            amount = total - 3;
                        }

                        // Slice the results array based on the calculated amount
                        const result = data.data.results.slice(amount);

                        let country = '';
                        let region = '';
                        let district = '';

                        // Loop through the results to extract location information
                        for (let index = 0; index < result.length; index++) {
                            const element = result[index];

                            // Check the type of location and extract relevant information
                            if (element.types[0] == 'country') {
                                country = element.formatted_address;
                            }
                            if (element.types[0] == 'administrative_area_level_1') {
                                const str = element.formatted_address;
                                const first = str.split(',').shift();
                                region = first;
                            }
                            if (element.types[0] == 'administrative_area_level_2') {
                                const str = element.formatted_address;
                                const first = str.split(' ').shift();
                                district = first;
                            }
                        }

                        // Create a FormData object to send the location information
                        var form = new FormData();
                        form.append('lat', lat);
                        form.append('lng', lng);
                        form.append('country', country);
                        form.append('region', region);
                        form.append('exact_location', district + "," + region + "," + country);

                        // Set the location session using the FormData
                        setLocationSession(form);

                        // Update UI with location information
                        $('.location_country').text(country);
                        $('.location_full_address').text(district + "," + region);

                        // Hide loader and show location section
                        $('.loader_position').addClass('d-none');
                        $('.location_secion').removeClass('d-none');
                    });
                });


            // Get the input element with the ID 'searchInput'
            var input = document.getElementById('searchInput');

            // Attach the search input to the top-left corner of the map
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // Create an autocomplete object using the Google Maps Autocomplete service
            var autocomplete = new google.maps.places.Autocomplete(input);

            // Limit the autocomplete suggestions to the current map bounds
            autocomplete.bindTo('bounds', map);

            // Create an info window to display information about the selected place
            var infowindow = new google.maps.InfoWindow();

            // Create a marker to indicate the selected place
            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29) // Offset for marker position
            });

            // Listen for the 'place_changed' event on the autocomplete input
            autocomplete.addListener('place_changed', function() {
                // Close the info window and hide the marker
                infowindow.close();
                marker.setVisible(false);

                // Get the selected place details from the autocomplete object
                var place = autocomplete.getPlace();

                // Extract and parse the coordinates from the place's geometry
                const coordinates = String(place.geometry.location);
                const regex = /(-?\d+\.\d+)/g;
                const matches = coordinates.match(regex);

                // If coordinates are successfully extracted
                if (matches && matches.length >= 2) {
                    const lat = parseFloat(matches[0]);
                    const lng = parseFloat(matches[1]);

                    // Call the handleMapUpdate function with the extracted coordinates
                    handleMapUpdate(lat, lng);
                } else {
                    console.log("Invalid coordinate format.");
                }

                // Adjust the map view based on the selected place's geometry
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport); // Fit map to the place's viewport
                } else {
                    map.setCenter(place.geometry.location); // Center map on the selected place
                    map.setZoom(17); // Set zoom level
                }
            });

        }


        window.initMap = initMap;
    </script>
    <script>
        @php
            $link1 = 'https://maps.googleapis.com/maps/api/js?key=';
            $link2 = $setting->google_map_key;
            $Link3 = '&callback=initMap&libraries=places,geometry';
            $scr = $link1 . $link2 . $Link3;
        @endphp;
    </script>
    <script src="{{ $scr }}" async defer></script>
    <!-- =============== google map ========= -->
    <script type="text/javascript">
        $(document).ready(function() {
            $("[data-toggle=tooltip]").tooltip()
        })
    </script>
    <script>
        $("#just_name").change(function() {
            if (this.checked) {
                $('#company_name_container').show();
                $('#company_label').find('span').hide();
                $('#company-input-select').hide();
                $('#company_name_container').find('span').show();
            } else {
                $('#company_name_container').hide();
                $('#company-input-select').show();
                $('#company_label').find('span').show();
                $('#company-input-container').show();
            }
        });
    </script>
@endsection
