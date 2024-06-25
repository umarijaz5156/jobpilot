@extends('frontend.layouts.app')

@section('title')
    {{ __('post_job') }}
@endsection

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <x-website.company.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right">
                        <div class="dashboard-right-header rt-mb-32">
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>
                        <form action="{{ route('company.payperjob.store') }}" method="POST" class="rt-from">
                            @csrf
                            <div class="post-job-item rt-mb-15">
                                <div class="row">
                                    <div class="col-lg-8 rt-mb-20">
                                        <x-forms.label name="job_title" :required="true" class="tw-text-sm tw-mb-2" />
                                        <input value="{{ old('title') }}" name="title"
                                            class="form-control @error('title') is-invalid @enderror" type="text"
                                            placeholder="{{ __('job_title') }}" id="m">
                                        @error('title')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 rt-mb-20 col-md-4">
                                        <x-forms.label name="job_category" :required="true" />
                                        <select
                                            class="w-100-p select2-taggable select2-search form-control @error('category_id') is-invalid @enderror"
                                            name="category_id">
                                            @foreach ($jobCategories as $category)
                                                <option {{ old('category_id') == $category->id ? 'selected' : '' }}
                                                    value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-8 rt-mb-20 col-md-8">
                                        <x-forms.label name="tags" :required="false" class="tw-text-sm tw-mb-2">
                                            ({{ __('saerch_or_write_tag_and_hit_enter') }})
                                        </x-forms.label>
                                        <select
                                            class="w-100-p rt-selectactive select2-taggable form-control @error('tags') is-invalid @enderror"
                                            name="tags[]" multiple>
                                            @foreach ($tags as $tag)
                                                <option
                                                    {{ old('tags') ? (in_array($tag->id, old('tags')) ? 'selected' : '') : '' }}
                                                    value="{{ $tag->id }}">{{ $tag->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('tags')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 rt-mb-20 col-md-4">
                                        <x-forms.label name="job_role" :required="true" class="tw-text-sm tw-mb-2" />
                                        <select
                                            class="w-100-p select2-taggable select2-search form-control @error('role_id') is-invalid @enderror"
                                            name="role_id">
                                            @foreach ($roles as $role)
                                                <option {{ old('role_id') == $role->id ? 'selected' : '' }}
                                                    value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="post-job-item rt-mb-15">
                                <h4 class="f-size-18 ft-wt-5 rt-mb-20 lh-1">{{ __('salary') }}</h4>
                                <div class="tw-flex tw-gap-5 mb-3">
                                    <div class="tw-flex tw-items-center tw-border ll-radio tw-border-gray-200 tw-rounded">
                                        <input checked onclick="salaryModeChange('range')" id="salary_rangee" type="radio"
                                            value="range" name="salary_mode" class="tw-scale-150">
                                        <label for="salary_rangee"
                                            class="tw-w-full tw-py-4 tw-ms-3 tw-text-sm tw-font-medium">{{ __('salary_range') }}</label>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-border ll-radio tw-border-gray-200 tw-rounded">
                                        <input onclick="salaryModeChange('custom')" id="custom_salary" type="radio"
                                            value="custom" name="salary_mode" class="tw-scale-150">
                                        <label for="custom_salary"
                                            class="tw-w-full tw-py-4 tw-ms-3 tw-text-sm tw-font-medium">{{ __('custom_salary') }}</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="rt-mb-20 col-md-8 d-none" id="custom_salary_part">
                                        <x-forms.label name="custom_salary" :required="true" class="tw-text-sm tw-mb-2" />
                                        <div class="position-relative">
                                            <input value="{{ old('custom_salary', 'Competitive') }}" name="custom_salary"
                                                class="form-control @error('custom_salary') is-invalid @enderror"
                                                type="text" id="m">
                                            @error('custom_salary')
                                                <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="rt-mb-20 col-md-4 salary_range_part">
                                        <x-forms.label name="min_salary" :required="false" class="tw-text-sm tw-mb-2" />
                                        <div class="position-relative">
                                            <input step="0.01" value="{{ old('min_salary', '50.00') }}"
                                                class="form-control @error('min_salary') is-invalid @enderror"
                                                name="min_salary" type="number" placeholder="{{ __('min_salary') }}"
                                                id="m">
                                            <div class="usd">{{ $currency_symbol }}</div>
                                            @error('min_salary')
                                                <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="rt-mb-20 col-md-4 salary_range_part">
                                        <x-forms.label name="max_salary" :required="false" class="tw-text-sm tw-mb-2" />
                                        <div class="position-relative">
                                            <input step="0.01" value="{{ old('max_salary', '100.00') }}"
                                                class="form-control @error('max_salary') is-invalid @enderror"
                                                name="max_salary" type="number" placeholder="{{ __('max_salary') }}"
                                                id="m">
                                            <div class="usd">{{ $currency_symbol }}</div>
                                            @error('max_salary')
                                                <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 rt-mb-20 col-md-6">
                                        <x-forms.label name="{{ __('salary_type') }}" :required="true"
                                            class="tw-text-sm tw-mb-2" />
                                        <select
                                            class="rt-selectactive form-control @error('salary_type') is-invalid @enderror w-100-p"
                                            name="salary_type">
                                            @foreach ($salary_types as $type)
                                                <option {{ old('salary_type') == $type->id ? 'selected' : '' }}
                                                    value="{{ $type->id }}">
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('salary_type')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="post-job-item rt-mb-15">
                                <h4 class="f-size-18 ft-wt-5 rt-mb-20 lh-1">{{ __('advance_information') }}</h4>
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 rt-mb-20">
                                        <x-forms.label name="education" :required="true" class="tw-text-sm tw-mb-2" />
                                        <select
                                            class="select2-taggable form-control @error('education') is-invalid @enderror w-100-p"
                                            name="education">
                                            @foreach ($educations as $education)
                                                <option {{ old('education') == $education->id ? 'selected' : '' }}
                                                    value="{{ $education->id }}">
                                                    {{ $education->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('education')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 col-md-6 rt-mb-20">
                                        <x-forms.label name="experience" :required="true" class="tw-text-sm tw-mb-2" />
                                        <select
                                            class="select2-taggable form-control @error('experience') is-invalid @enderror w-100-p"
                                            name="experience">
                                            @foreach ($experiences as $experience)
                                                <option {{ old('experience') == $experience->id ? 'selected' : '' }}
                                                    value="{{ $experience->id }}">
                                                    {{ $experience->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('experience')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 col-md-6 rt-mb-20">
                                        <x-forms.label name="job_type" :required="true" class="tw-text-sm tw-mb-2" />
                                        <select
                                            class="rt-selectactive form-control @error('job_type') is-invalid @enderror w-100-p"
                                            name="job_type">
                                            @foreach ($job_types as $job_type)
                                                <option {{ old('job_type') == $job_type->id ? 'selected' : '' }}
                                                    value="{{ $job_type->id }}">
                                                    {{ $job_type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('job_type')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 col-md-6 rt-mb-20">
                                        <x-forms.label name="vacancies" :required="true" class="tw-text-sm tw-mb-2" />
                                        <input value="{{ old('vacancies', 1) }}" name="vacancies" type="text"
                                            placeholder="{{ __('vacancies') }}"
                                            class="form-control @error('vacancies') is-invalid @enderror" id="vacancies">
                                        @error('vacancies')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 col-md-6 rt-mb-20">
                                        <x-forms.label name="deadline_expired" :required="true"
                                            class="tw-text-sm tw-mb-2" />
                                        <div class="fromGroup">
                                            <div class="form-control-icon date datepicker">
                                                <input value="{{ old('deadline') }}" name="deadline"
                                                    class="form-control !tw-ps-[55px] @error('deadline') is-invalid @enderror"
                                                    type="text" value="{{ old('deadline') ? old('deadline') : '' }}"
                                                    id="date" placeholder="d/m/y">
                                                <span class="input-group-addon has-badge">
                                                    <span @error('deadline') rt-mr-12 @enderror>
                                                        <x-svg.calendar-icon />
                                                    </span>
                                                </span>
                                                @error('deadline')
                                                    <span class="error invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="tw-text-sm tw-font-medium tw-text-red-500">
                                            {{ __('maximum_deadline_limit') }}:
                                            {{ $setting->job_deadline_expiration_limit }} {{ __('days') }}
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12 rt-mb-15">
                                    @php
                                        $map = $setting->default_map;
                                    @endphp
                                    <div class="location-wrapper">
                                        <div class="row">
                                            <div class="col-12">
                                                <h2>
                                                    {{ __('location') }} <span class="text-danger">*</span>
                                                    <small class="h6">
                                                        ({{ __('click_to_add_a_pointer') }})
                                                    </small>
                                                </h2>
                                            </div>
                                            <div class="col-md-12 col-sm-12 rt-mb-24">
                                                <x-website.map.map-warning />

                                                <div id="google-map-div"
                                                    class="{{ $map == 'google-map' ? '' : 'd-none' }}">
                                                    <input id="searchInput" class="mapClass" type="text"
                                                        placeholder="{{ __('enter_location') }}">
                                                    <div class="map mymap" id="google-map"></div>
                                                </div>
                                                <div id="google-map-div"
                                                    class="{{ $map == 'google-map' ? '' : 'd-none' }}">
                                                    <input id="searchInput" class="mapClass" type="text"
                                                        placeholder="{{ __('enter_location') }}">
                                                    <div class="map mymap" id="google-map"></div>
                                                </div>
                                                <div class="{{ $map == 'leaflet' ? '' : 'd-none' }}">
                                                    <input type="text" autocomplete="off" id="leaflet_search"
                                                        placeholder="{{ __('enter_city_name') }}" class="full-width" />
                                                    <br>
                                                    <div id="leaflet-map"></div>
                                                </div>
                                                @error('location')
                                                    <span class="ml-3 text-md text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-12 mt-4 custom-checkbox-wrap">
                                                <label class="main tw-text-sm"
                                                    for="remoteWork">{{ __('fully_remote_position') }}-<span
                                                        class="tw-font-medium">{{ __('worldwide') }}</span>
                                                    <input type="checkbox" name="is_remote" id="remoteWork"
                                                        value="1" {{ old('is_remote') ? 'checked' : '' }}>
                                                    <span class="custom-checkbox"></span>
                                                </label>
                                                <input type="checkbox" name="is_remote" id="remoteWork" value="1"
                                                    {{ old('is_remote') ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="post-job-item rt-mb-32">
                                <h4 class="f-size-18 ft-wt-5 rt-mb-20 lh-1">{{ __('benefits') }}</h4>
                                <div class="benefits-tags" id="benefit_list">
                                    @foreach ($benefits as $benefit)
                                        <label for="benefit_{{ $benefit->id }}">
                                            <input
                                                {{ old('benefits') ? (in_array($benefit->id, old('benefits')) ? 'checked' : '') : '' }}
                                                type="checkbox" id="benefit_{{ $benefit->id }}" name="benefits[]"
                                                value="{{ $benefit->id }}">
                                            <span>{{ $benefit->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('benefits')
                                    <span class="error invalid-feedback d-block">{{ $message }}</span>
                                @enderror

                                <div class="mt-3">
                                    <a onclick="showHideCreateBenefit()" href="javascript:void(0)"
                                        class="text-decoration-underline">{{ __('create_new') }} {{ __('benefit') }}</a>

                                    <div class="d-flex tw-justify-between tw-gap-2 mt-3 d-none" id="create_benefit">
                                        <input value="{{ old('title') }}" name="new_benefit"
                                            class="form-control @error('title') is-invalid @enderror" type="text"
                                            placeholder="{{ __('benefit') }}" id="m">

                                        <button onclick="createBenefit()" type="button"
                                            class="btn btn-primary rt-mr-10">
                                            <span class="button-content-wrapper ">
                                                <span class="button-text">
                                                    {{ __('create') }} {{ __('benefit') }}
                                                </span>
                                                <span class="button-icon align-icon-right">
                                                    <i class="ph ph-plus"></i>
                                                </span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="post-job-item rt-mb-32">
                                <h4 class="f-size-18 ft-wt-5 tw-mb-3 lh-1">
                                    {{ __('job_description') }}
                                    <span class="form-label-required text-danger">*</span>
                                </h4>
                                <div class="col-md-12">
                                    <textarea id="image_ckeditor" class="form-control @error('description') is-invalid @enderror" name="description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Additional Questions --}}
                            @if (currentCompany()->question_feature_enable)
                                <div x-data="appQuestion()" x-init="select2Alpine"
                                    class="post-job-item rt-mb-15 tw-w-full tw-overflow-hidden ">
                                    <h4 class="f-size-18 ft-wt-5 rt-mb-20 lh-1">{{ __('add_screening_questions') }}</h4>
                                    <div class="row">
                                        <div class="rt-mb-20">
                                            <div class="col-lg-12">
                                                <div x-show="isAddingNewQuestion" class="tw-flex justify-content-between">
                                                    <label class="tw-text-sm tw-mb-2 mb-2" for="for">
                                                        {{ __('create_new_screening_question') }}
                                                    </label>
                                                    <a x-show="isAddingNewQuestion" href="#"
                                                        @click.prevent="isAddingNewQuestion = false">
                                                        {{ __('choose_from_existing_question') }}
                                                    </a>
                                                </div>
                                                <div x-show="!isAddingNewQuestion"
                                                    class="tw-flex justify-content-between">
                                                    <label class="tw-text-sm tw-mb-2 mb-2" for="for">
                                                        {{ __('choose_from_existing_question') }}
                                                    </label>
                                                    <a href="#" x-show="!isAddingNewQuestion"
                                                        @click.prevent="isAddingNewQuestion = true"
                                                        href="#">{{ __('create_new_screening_question') }}</a>
                                                </div>
                                                <input x-show="isAddingNewQuestion" value="" x-model="newQuestion"
                                                    class="form-control " type="text" placeholder="Add Question">
                                            </div>
                                            <div x-show="isAddingNewQuestion"
                                                class="tw-flex tw-gap-5 mb-3 flex justify-content-between tw-mt-4">
                                                <div class="tw-flex justify-between ">
                                                    <div
                                                        class="ll-radio tw-flex tw-items-center tw-border tw-border-gray-200 tw-rounded tw-ps-1 tw-mr-4">
                                                        <label class="mt-2">
                                                            <input x-model="newQuestionSave" class="tw-scale-150"
                                                                type="checkbox" style="margin-right: 10px">
                                                            {{ __('save_for_letter') }}
                                                        </label>
                                                    </div>
                                                    <div
                                                        class="ll-radio tw-flex tw-items-center tw-border tw-border-gray-200 tw-rounded tw-ps-1">
                                                        <label class="mt-2">
                                                            <input x-model="isRequired" class="tw-scale-150"
                                                                type="checkbox" style="margin-right: 10px">
                                                            {{ __('candidate_must_answer') }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button @click.prevent="addQuestion" type="button"
                                                        class="btn btn-primary"> {{ __('save') }} </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="isAddingNewQuestion == false" class="q-select">
                                        <select id="questionSelect" multiple="multiple" x-ref="select"
                                            data-placeholder="Select Questions" name="companyQuestions[]" class="">
                                            <option></option>
                                            @foreach ($questions as $question)
                                                <option value="{{ $question->id }}"> {{ $question->title }}
                                                    {{ $question->required ? '(required)' : '' }} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div x-show="selectedQuestions.length">
                                        <h4 class="f-size-18 ft-wt-5 rt-mb-20 lh-1 mt-4">
                                            {{ __('selected_screening_questions') }}</h4>
                                        <ul>
                                            <template x-for="question in selectedQuestions">
                                                <div class="tw-flex justify-content-between my-2">
                                                    <li class="flex-grow-1"
                                                        x-text="question.required  ? question.title+' (required)' : question.title ">
                                                    </li>
                                                    <div class="cursor-pointer f" style="color:red;">
                                                        <svg @click="remove(question.id)" width="20" height="20"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>

                                                    </div>
                                                </div>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <div class="row tw-mb-8">
                                <div class="col-12">
                                    <div class="applied-job-on">
                                        <div class="row">
                                            <h2>{{ __('apply_job_on') }}:</h2>
                                            <!-- apply_on -->
                                            <div id="applied_on_app"
                                                class="radio-check col-lg-4 d-flex {{ old('apply_on') === 'app' ? 'checked' : '' }}"
                                                onclick="RadioChecked('app')">
                                                <input type="radio" {{ old('apply_on') === 'app' ? 'checked' : '' }}
                                                    checked name="apply_on" value="app" id="app-app">
                                                <label for="app-app">
                                                    <h4 class="d-inline-block">{{ __('onn') }}
                                                        {{ config('app.name') }}</h4>
                                                    <p class="tw-mb-0">{{ __('candidate_will_apply_job_using') }}
                                                        {{ config('app.name') }} &
                                                        {{ __('all_application_will_show_on_your_dashboard') }}.</p>
                                                </label>
                                            </div>
                                            <div id="applied_on_custom_url"
                                                class="radio-check col-lg-4 d-flex {{ old('apply_on') === 'custom_url' ? 'checked' : '' }}"
                                                onclick="RadioChecked('custom_url')">
                                                <input type="radio"
                                                    {{ old('apply_on') === 'custom_url' ? 'checked' : '' }}
                                                    name="apply_on" value="custom_url" id="app-custom_url">
                                                <label for="app-custom_url">
                                                    <h4 class="d-inline-block">{{ __('external_platform') }}</h4>
                                                    <p class="tw-mb-0">
                                                        {{ __('candidate_apply_job_on_your_website_all_application_on_your_own_website') }}.
                                                    </p>
                                                </label>
                                            </div>
                                            <div id="applied_on_email"
                                                class="radio-check col-lg-4 d-flex {{ old('apply_on') === 'email' ? 'checked' : '' }}"
                                                onclick="RadioChecked('email')">
                                                <input type="radio" {{ old('apply_on') === 'email' ? 'checked' : '' }}
                                                    name="apply_on" value="email" id="app-email">
                                                <label for="app-email">
                                                    <h4 class="d-inline-block">{{ __('on_your_email') }}</h4>
                                                    <p class="tw-mb-0">
                                                        {{ __('candidate_apply_job_on_your_email_address_and_all_application_in_your_email') }}.
                                                    </p>
                                                </label>
                                            </div>
                                            <!-- apply_on end-->
                                            <div class="col-12 d-none" id="apply_on_custom_url">
                                                <x-forms.label name="website_url" :required="true" />
                                                <div class="fromGroup has-icon2">
                                                    <div class="form-control-icon">
                                                        <input value="{{ old('apply_url') }}" name="apply_url"
                                                            class="form-control @error('apply_url') is-invalid @enderror"
                                                            type="url" placeholder="{{ __('website') }}">
                                                        <div class="icon-badge-2 @error('apply_url') mt-n-11 @enderror">
                                                            <x-svg.link-icon />
                                                        </div>
                                                        @error('apply_url')
                                                            <span class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 d-none" id="apply_on_email">
                                                <x-forms.label name="email_address" :required="true" />
                                                <div class="fromGroup has-icon2">
                                                    <div class="form-control-icon">
                                                        <input value="{{ old('apply_email') }}" name="apply_email"
                                                            class="form-control @error('apply_email') is-invalid @enderror"
                                                            type="email" placeholder="{{ __('email_address') }}">
                                                        <div class="icon-badge-2 @error('apply_email') mt-n-11 @enderror">
                                                            <x-svg.envelope-icon />
                                                        </div>
                                                        @error('apply_email')
                                                            <span class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="promote-form-wrapper">
                                <p>
                                    {{ __('highlight_jobs_or_show_them_on_the_home_page_as_a_feature') }}
                                </p>
                                <div class="input-wrapper tw-flex tw-flex-wrap tw-gap-6 rt-mb-32">
                                    <label class="form-check promote-form from-radio-custom tw-max-w-[384px]">
                                        <img src="{{ asset('frontend/assets/images/all-img/always-on-top.png') }}"
                                            alt="on-top">
                                        <div class="tw-flex tw-items-center tw-ml-6 tw-mt-6">
                                            <input
                                                onclick="promoteJobPrice('{{ $setting->featured_job_price }}', '{{ $setting->per_job_price }}')"
                                                value="featured" class="form-check-input" type="checkbox" name="badge"
                                                id="flexRadioDefault1" value="featured">
                                            <label class="form-check-label f-size-14 pointer tw-mt-1"
                                                for="flexRadioDefault1">
                                                {{ __('featured') }} ({{ __('on_the_top') }})
                                                (+{{ $setting->featured_job_price }})
                                            </label>
                                        </div>
                                    </label>
                                    <label class="form-check promote-form from-radio-custom tw-max-w-[384px]">
                                        <img src="{{ asset('frontend/assets/images/all-img/highlight-job.png') }}"
                                            alt="highlighted">
                                        <div class="tw-flex tw-items-center tw-ml-6 tw-mt-6">
                                            <input
                                                onclick="promoteJobPrice('{{ $setting->highlight_job_price }}', '{{ $setting->per_job_price }}')"
                                                value="highlight" class="form-check-input" type="checkbox"
                                                name="badge" id="flexRadioDefault2">
                                            <label class="form-check-label f-size-14 pointer tw-mt-1"
                                                for="flexRadioDefault2">
                                                {{ __('highlight') }} (+{{ $setting->highlight_job_price }})
                                            </label>
                                        </div>
                                    </label>

                                </div>
                            </div>
                            <div class="post-job-item rt-mb-15 mt-5">
                                <button type="submit" class="btn btn-primary rt-mr-10">
                                    <span class="button-content-wrapper ">
                                        <span class="button-icon align-icon-right">
                                            <i class="ph-arrow-right"></i>
                                        </span>
                                        <span class="button-text">
                                            {{ __('post_job') }} - <span
                                                id="per_job_price">{{ $setting->per_job_price }}</span>
                                            {{ currentCurrencyCode() }}
                                        </span>
                                        <input type="hidden" id="total_price_perjob" name="total_price_perjob"
                                            value="{{ $setting->per_job_price }}">
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="text" id="current_rate" value="{{ getCurrencyRate() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/bootstrap-datepicker.min.css">
    <x-map.leaflet.map_links />
    <x-map.leaflet.autocomplete_links />
    @include('map::links')
    <style>
        .ck-editor__editable_inline {
            min-height: 300px;
        }

        .mymap {
            border-radius: 12px;
        }

        .mt-n-11 {
            margin-top: -11px;
        }

        .custom-checkbox-wrap .main input:checked~.custom-checkbox:after {
            left: 8% !important;
        }
    </style>
@endsection

@section('frontend_scripts')
    <script src="{{ asset('frontend/assets/js/bootstrap-datepicker.min.js') }}"></script>
    <script defer src="{{ asset('backend/js/alpine.min.js') }}"></script>
    <script>
        const checkboxes = document.querySelectorAll('input[name="badge"]');

        checkboxes.forEach((checkbox) => {

            checkbox.addEventListener('change', () => {
                checkboxes.forEach((otherCheckbox) => {
                    if (otherCheckbox !== checkbox) {
                        otherCheckbox.checked = false;
                    }
                })
                if (!checkbox.checked && checkbox.id === 'flexRadioDefault1') {
                    promoteJobPrice('0', '{{ $setting->per_job_price }}')
                } else if (checkbox.checked && checkbox.id === 'flexRadioDefault1') {
                    promoteJobPrice('{{ $setting->featured_job_price }}',
                        '{{ $setting->per_job_price }}')
                } else if (!checkbox.checked && checkbox.id === 'flexRadioDefault2') {
                    promoteJobPrice('0', '{{ $setting->per_job_price }}')
                } else if (checkbox.checked && checkbox.id === 'flexRadioDefault2') {
                    promoteJobPrice('{{ $setting->highlight_job_price }}',
                        '{{ $setting->per_job_price }}')
                }
            });
        });
        function appQuestion() {
            return {
                allQuestions: @json($questions),
                selectedQuestions: [],
                selectedQuestionsIds: [],
                newQuestion: '',
                isAddingNewQuestion: false,
                newQuestionSave: true,
                isRequired: false,
                addQuestion: function() {

                    if (!this.newQuestion) return;


                    axios.post('/company/questions', {
                        newQuestion: this.newQuestion,
                        newQuestionSave: this.newQuestionSave,
                        isRequired: this.isRequired

                    }).then((response) => {
                        this.selectedQuestions.push(response.data);
                        this.allQuestions.push(response.data);

                        this.selectedQuestionsIds.push(response.data.id);
                        var optionValue = response.data.id;
                        var optionText = response.data.title;
                        if (response.data.required) {

                            optionText += '(required)'
                        }
                        var newOption = new Option(optionText, optionValue, false, true);
                        this.select2 = $(this.$refs.select).select2();

                        this.select2.append(newOption).trigger('change');

                    })


                    this.newQuestion = "";
                    this.newQuestionSave = true;
                    this.isRequired = false;

                },
                remove: function(idToRemove) {
                    this.selectedQuestionsIds = this.selectedQuestionsIds.filter((id) => {
                        return id != idToRemove;
                    })
                    this.selectedQuestions = this.selectedQuestions.filter((ques) => {
                        return ques.id != idToRemove;
                    })
                    this.select2 = $(this.$refs.select).select2();
                    this.select2.val(this.selectedQuestionsIds);
                    this.select2.trigger('change');

                }
            }
        }

        function select2Alpine() {

            this.select2 = $(this.$refs.select).select2();
            this.select2.on("select2:select", (event) => {
                var values = [];
                var old_values = [];

                // copy all option values from selected
                $(event.currentTarget).find("option:selected").each(function(i, selected) {
                    values[i] = $(selected).val();
                });

                this.selectedQuestionsIds = values;
                console.log(this.allQuestions);
                var items = [];

                this.allQuestions.forEach((item) => {
                    if (values.includes(item.id.toString())) {

                        items.push(item);
                    }

                });

                this.selectedQuestions = items;



            });
            this.select2.on("select2:unselect", (event) => {
                var values = [];
                $(event.currentTarget).find("option:selected").each(function(i, selected) {
                    values[i] = $(selected).val();
                });

                this.selectedQuestionsIds = values;
                console.log(values);
                var items = [];

                this.allQuestions.forEach((item) => {
                    console.log(values);
                    console.log(item.id);
                    if (values.includes(item.id.toString())) {
                        items.push(item);
                    }

                });

                this.selectedQuestions = items;


            });
        }
    </script>

    {{-- @if (app()->getLocale() == 'ar')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.ar.min.js
    "></script>
    @endif --}}
    @include('map::set-leafletmap')
    <script>
        var max_days = '{{ $setting->job_deadline_expiration_limit }}'

        $("#date").attr("autocomplete", "off");
        $('#date').off('focus').datepicker({
            format: 'dd-mm-yyyy',
            startDate: '0d',
            endDate: `+${max_days}d`,
            isRTL: "{{ app()->getLocale() == 'ar' ? true : false }}",
            language: "{{ app()->getLocale() }}",
        }).on('click',
            function() {
                $(this).datepicker('show');
            }
        );
    </script>
    @include('map::set-googlemap')


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

        function RadioChecked(param) {
            var value = param;
            if (value === 'email') {
                $('#applied_on_email').addClass('checked');
                $('#apply_on_custom_url').addClass('d-none');
                $('#apply_on_email').removeClass('d-none');
                $('#applied_on_app').removeClass('checked');
                $('#applied_on_custom_url').removeClass('checked');
            }
            if (value === 'custom_url') {
                $('#applied_on_custom_url').addClass('checked');
                $('#apply_on_email').addClass('d-none');
                $('#apply_on_custom_url').removeClass('d-none');
                $('#applied_on_app').removeClass('checked');
                $('#applied_on_email').removeClass('checked');
            }
            if (value === 'app') {
                $('#applied_on_app').addClass('checked');
                $('#applied_on_email').removeClass('checked');
                $('#applied_on_custom_url').removeClass('checked');
                $('#apply_on_email').addClass('d-none');
                $('#apply_on_custom_url').addClass('d-none');
            }
        }
        $('.radio-check').on('click', function() {
            $('input:radio', this).prop('checked', true);
        });

        if ($('#app-app').is(':checked')) {
            $('#applied_on_app').addClass('checked');
        }
        if ($('#app-custom_url').is(':checked')) {
            $('#apply_on_custom_url').removeClass('d-none');
        }
        if ($('#app-email').is(':checked')) {
            $('#apply_on_email').removeClass('d-none');
        }

        var apply_url = "{!! $errors->first('apply_url') !!}";
        var apply_url1 = "{!! old('apply_email') !!}";
        var apply_email = "{!! $errors->first('apply_email') !!}";
        var apply_email1 = "{!! old('apply_email') !!}";

        if (apply_url) {
            $('#apply_on_custom_url').removeClass('d-none');
        }
        if (apply_url1) {
            $('#apply_on_custom_url').removeClass('d-none');
        }
        if (apply_email) {
            $('#apply_on_email').removeClass('d-none');
        }
        if (apply_email1) {
            $('#apply_on_email').removeClass('d-none');
        }

        function promoteJobPrice(promoteJob, jobPrice) {
            let currency_rate = $('#current_rate').val();
            let totalPrice = parseInt(promoteJob) + parseInt(jobPrice)
            let currentConvertPrice = totalPrice * currency_rate
            let roundPrice = currentConvertPrice.toFixed(2)

            $('#per_job_price').html(roundPrice)
            $('#total_price_perjob').val(roundPrice)
        }

        function showHideCreateBenefit() {
            $('#create_benefit').toggleClass('d-none');
        }

        function createBenefit() {
            var benefit = $('input[name="new_benefit"]').val();

            if (benefit == '') {
                alert('Please enter benefit name');
                return false;
            }

            axios.post("/job/benefits/create", {
                benefit: benefit
            }).then((response) => {
                var data = response.data;

                if (data.length && typeof data == 'string') {
                    return Swal.fire('Error', data, 'error');
                }

                $('#benefit_list').append(`<label for="benefit_${data.id}">
                    <input type="checkbox" id="benefit_${data.id}" name="benefits[]" value="${data.id}">
                    <span>${data.name}</span>
                </label>`);

                $('input[name="new_benefit"]').val('');
            }).catch((err) => {
                this.errors = err.response.data.errors;
            });
        }
    </script>
@endsection
