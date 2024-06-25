@extends('frontend.layouts.app')

@section('description')
    @php
        $data = metaData('company');
    @endphp
    {{ $data->description }}
@endsection
@section('og:image')
    {{ asset($data->image) }}
@endsection
@section('title')
    {{ $data->title }}
@endsection

@section('main')
    <div class="breadcrumbs style-two">
        <div class="container">
            <div class="row align-items-center ">
                <div class="col-12 position-relative ">
                    <div class="breadcrumb-menu">
                        <h6 class="f-size-18 m-0">{{ __('find_employers') }}</h6>
                        <ul>
                            <li><a href="{{ route('website.home') }}">{{ __('home') }}</a></li>
                            <li>/</li>
                            <li>{{ __('companies') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12 tw-mb-6">
                    <form action="{{ route('website.company') }}" id="formSubmit">
                        <div class="tw-mt-6">
                            <div class="top-content tw-flex tw-flex-col lg:tw-flex-row lg:tw-items-center tw-filter-box">
                                <div class="tw-p-3 tw-flex-grow ll-input-border">
                                    <div class="fromGroup tw-w-full has-icon position-relative">
                                        <input id="search" name="keyword" type="text"
                                            placeholder="{{ __('company_title_keyword') }}" value="{{ request('keyword') }}"
                                            autocomplete="off" class="tw-border-0">
                                        <div class="icon-badge">
                                            <x-svg.search-icon />
                                        </div>
                                        <span id="autocomplete_job_results"></span>
                                    </div>
                                </div>
                                @php
                                    $oldLocation = request('location');
                                    $map = $setting->default_map;
                                @endphp
                                <div class="fromGroup tw-px-3 position-relative">

                                    @if ($map == 'google-map')
                                        <input type="hidden" name="lat" id="lat" value="">
                                        <input type="hidden" name="long" id="long" value="">
                                        <input type="text" id="searchInput" placeholder="{{ __('enter_location') }}"
                                            name="location" value="{{ request('location') }}"
                                            class="tw-border-0 tw-pl-12" />
                                        <div id="google-map" class="d-none"></div>
                                    @else
                                        <input name="long" class="leaf_lon" type="hidden">
                                        <input name="lat" class="leaf_lat" type="hidden">
                                        <input type="text" id="leaflet_search" placeholder="{{ __('enter_location') }}"
                                            name="location" value="{{ request('location') }}" class="tw-border-0 tw-pl-12"
                                            autocomplete="off" />
                                    @endif

                                    <div class="tw-absolute tw-top-1/2 -tw-translate-y-1/2 tw-left-3">
                                        <x-svg.location-icon width="24" height="24"
                                            stroke="{{ $setting->frontend_primary_color }}" />
                                    </div>
                                </div>
                                <div class="tw-p-3">
                                    <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-3">
                                        <div>
                                            <button type="button"
                                                class="btn tw-inline-flex gap-3 tw-items-center hover:tw-bg-[#F1F2F4] tw-bg-[#F1F2F4] hover:tw-text-[#18191C] tw-text-[#18191C] tw-border-0"
                                                data-bs-toggle="modal" data-bs-target="#companyFiltersModal">
                                                <span class="">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12.0001 10.125L12.0001 20.25" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path d="M12.0001 3.75L12.0001 6.375" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M12.0001 10.125C13.0357 10.125 13.8751 9.28553 13.8751 8.25C13.8751 7.21447 13.0357 6.375 12.0001 6.375C10.9646 6.375 10.1251 7.21447 10.1251 8.25C10.1251 9.28553 10.9646 10.125 12.0001 10.125Z"
                                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path d="M18.7501 17.625L18.7502 20.25" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path d="M18.7502 3.75L18.7501 13.875" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M18.7501 17.625C19.7857 17.625 20.6251 16.7855 20.6251 15.75C20.6251 14.7145 19.7857 13.875 18.7501 13.875C17.7146 13.875 16.8751 14.7145 16.8751 15.75C16.8751 16.7855 17.7146 17.625 18.7501 17.625Z"
                                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path d="M5.25007 14.625L5.25 20.25" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path d="M5.25 3.75L5.25007 10.875" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M5.25012 14.625C6.28566 14.625 7.12512 13.7855 7.12512 12.75C7.12512 11.7145 6.28566 10.875 5.25012 10.875C4.21459 10.875 3.37512 11.7145 3.37512 12.75C3.37512 13.7855 4.21459 14.625 5.25012 14.625Z"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                </span>
                                                <span>Filter</span>
                                            </button>
                                        </div>
                                        <div>
                                            <button type="submit"
                                                class="btn btn-primary md:tw-inline-block tw-w-full tw-block">
                                                {{ __('search_employers') }}
                                            </button>
                                        </div>
                                        <span id="autocomplete_job_results"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                @if (request('keyword') || request('organization_type') || request('team_size') || request('industry_type'))
                    <div class="col-12">
                        <div class="tw-flex tw-gap-2 tw-items-center tw-py-4 tw-mb-6"
                            style="border-top: 1px solid #E4E5E8; border-bottom: 1px solid #E4E5E8;">

                            <h2 class="tw-text-sm tw-text-[#767F8C] tw-whitespace-nowrap tw-mb-0">
                                {{ __('active_filter') }}:</h2>


                            <div class="d-flex w-100-p">
                                @if (Request::get('keyword'))
                                    <div class="rt-mr-2 icon-badge">
                                        <x-website.candidate.filter-data-component title="{{ __('keyword') }}"
                                            filter="{{ request('keyword') }}" />
                                    </div>
                                @endif
                                @if (Request::get('industry_type') && Request::get('industry_type') != 'all')
                                    <div class="rt-mr-2 icon-badge">
                                        <x-website.candidate.filter-data-component title="industry_type"
                                            filter="{{ request('industry_type') }}" />
                                    </div>
                                @endif
                                @if (Request::get('organization_type') && Request::get('organization_type') != 'all')
                                    <div class="rt-mr-2 icon-badge">
                                        <x-website.candidate.filter-data-component title="organization_type"
                                            filter="{{ request('organization_type') }}" />
                                    </div>
                                @endif
                                @if (Request::get('team_size') && Request::get('team_size') != 'all')
                                    <div class="rt-mr-2 icon-badge">
                                        <x-website.candidate.filter-data-component title="team_size"
                                            filter="{{ request('team_size') }}" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    {{-- <form id="form" action="{{ route('website.company') }}" method="GET">
        <div class="candidate-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 rt-mb-24">
                        <div class="joblist-left-content2">
                            <div class="md:tw-w-3/4 rt-mb-24 tw-ml-auto">
                                <div class="tw-flex md:tw-flex-row tw-flex-col tw-gap-5">
                                    <div class="md:tw-w-1/3 tw-w-full">
                                        <select onchange="$('#form').submit();" name="organization_type"
                                            class="only-select2-search form-control rt-selectactive gap w-100-p">
                                            <option {{ request('organization_type') ? '' : 'selected' }} value="">
                                                {{ __('all') }} {{ __('organization_type') }}
                                            </option>
                                            @foreach ($organization_types as $organization_type)
                                                <option
                                                    {{ request('organization_type') == $organization_type['id'] ? 'selected' : '' }}
                                                    value="{{ $organization_type['id'] }}">
                                                    {{ $organization_type['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:tw-w-1/3 tw-w-full">
                                        <select onchange="$('#form').submit();" name="industry_type"
                                            class="only-select2-search form-control rt-selectactive gap w-100-p">
                                            <option {{ request('industry_type') ? '' : 'selected' }} value="">
                                                {{ __('all') }} {{ __('industry_type') }}
                                            </option>
                                            @foreach ($industry_types as $industry_type)
                                                <option
                                                    {{ request('industry_type') == $industry_type['id'] ? 'selected' : '' }}
                                                    value="{{ $industry_type['id'] }}">
                                                    {{ $industry_type['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:tw-w-1/3 tw-w-full">
                                        <select onchange="$('#form').submit();" name="team_size"
                                            class="only-select2-search form-control rt-selectactive gap w-100-p">
                                            <option {{ request('team_size') ? '' : 'selected' }} value="">
                                                {{ __('all') }} {{ __('team_size') }}
                                            </option>
                                            @foreach ($team_sizes as $team_size)
                                                <option {{ request('team_size') == $team_size['id'] ? 'selected' : '' }}
                                                    value="{{ $team_size['id'] }}">
                                                    {{ $team_size['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="rt-spacer-10"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form> --}}

    <div class="container">
        <div class="row">
            <div class="col-xl-12" id="togglclass1">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="row">
                            @if ($companies->count() > 0)
                                @foreach ($companies as $company)
                                    {{-- <div class="col-md-4 fade-in-bottom  condition_class rt-mb-24">
                                        <a href="{{ route('website.employe.details', $company->user->username) }}"
                                            class="card jobcardStyle1">
                                            <div class="tw-p-6">
                                                <div class="tw-flex tw-gap-4 tw-items-center">
                                                    <div class="tw-w-[56px] tw-h-[56px]">
                                                        <img src="{{ url($company->logo_url) }}" alt="logo"
                                                            draggable="false"
                                                            class="tw-w-full tw-h-full tw-rounded-[4px]">
                                                    </div>
                                                    <div class="">
                                                        <div class="tw-mb-1.5">
                                                            <div
                                                                class="tw-flex tw-gap-3 tw-justify-start tw-items-center text-primary">
                                                                <p
                                                                    class="tw-text-[#191F33] tw-text-lg tw-font-medium tw-mb-0">
                                                                    {{ $company->user->name }}</p>
                                                                @if ($company->is_profile_verified)
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" fill="currentColor"
                                                                        viewBox="0 0 256 256">
                                                                        <path
                                                                            d="M225.86,102.82c-3.77-3.94-7.67-8-9.14-11.57-1.36-3.27-1.44-8.69-1.52-13.94-.15-9.76-.31-20.82-8-28.51s-18.75-7.85-28.51-8c-5.25-.08-10.67-.16-13.94-1.52-3.56-1.47-7.63-5.37-11.57-9.14C146.28,23.51,138.44,16,128,16s-18.27,7.51-25.18,14.14c-3.94,3.77-8,7.67-11.57,9.14C88,40.64,82.56,40.72,77.31,40.8c-9.76.15-20.82.31-28.51,8S41,67.55,40.8,77.31c-.08,5.25-.16,10.67-1.52,13.94-1.47,3.56-5.37,7.63-9.14,11.57C23.51,109.72,16,117.56,16,128s7.51,18.27,14.14,25.18c3.77,3.94,7.67,8,9.14,11.57,1.36,3.27,1.44,8.69,1.52,13.94.15,9.76.31,20.82,8,28.51s18.75,7.85,28.51,8c5.25.08,10.67.16,13.94,1.52,3.56,1.47,7.63,5.37,11.57,9.14C109.72,232.49,117.56,240,128,240s18.27-7.51,25.18-14.14c3.94-3.77,8-7.67,11.57-9.14,3.27-1.36,8.69-1.44,13.94-1.52,9.76-.15,20.82-.31,28.51-8s7.85-18.75,8-28.51c.08-5.25.16-10.67,1.52-13.94,1.47-3.56,5.37-7.63,9.14-11.57C232.49,146.28,240,138.44,240,128S232.49,109.73,225.86,102.82Zm-52.2,6.84-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z">
                                                                        </path>
                                                                    </svg>
                                                                @endif
                                                            </div>
                                                            <p class="tw-text-[#767F8C] tw-text-sm tw-mb-0">
                                                                <span class="tw-flex tw-items-center tw-gap-1">
                                                                    <i class="ph-map-pin"></i>
                                                                    {{ $company->exact_location ? $company->exact_location : $company->full_address }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="post-info d-flex">
                                                    <div class="flex-grow-1">
                                                        @if ($company->activejobs == 0)
                                                            <div class="d-block text-dark border-0 text-center">
                                                                <div class="button-content-wrapper ">
                                                                    <span class="button-text">
                                                                        {{ __('no_open_position') }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span>
                                                                <button type="button"
                                                                    class="btn btn-primary2-50 d-block mt-2">
                                                                    <div class="button-content-wrapper ">
                                                                        <span class="button-icon align-icon-right">
                                                                            <i class="ph-arrow-right"></i>
                                                                        </span>
                                                                        <span class="button-text">
                                                                            {{ __('open_position') }}
                                                                        </span>
                                                                    </div>
                                                                </button>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div> --}}
                                    <div
                                        class="col-xl-4 col-md-6 fade-in-bottom  condition_class rt-mb-24 tw-self-stretch">
                                        <a href="{{ route('website.employe.details', $company->user->username) }}"
                                            class="card jobcardStyle1 tw-relative tw-h-full">
                                            <div class="tw-p-6 !tw-pb-[72px]">
                                                <div class="rt-single-icon-box tw-gap-3">
                                                    <div class="tw-w-14 tw-h-14">
                                                        <img class="tw-w-full tw-h-full tw-object-cover"
                                                            src="{{ $company->logo_url }}" alt="logo"
                                                            draggable="false" class="object-fit-contain">
                                                    </div>
                                                    <div class="iconbox-content">
                                                        <div class="">
                                                            <span
                                                                class="tw-text-[#191F33] tw-text-lg tw-font-medium  tw-inline-block">{{ $company->user->name }}</span>
                                                        </div>
                                                        @isset($company->country)
                                                            <span class="loacton text-gray-400 ">
                                                                <i class="ph-map-pin"></i>
                                                                {{ $company->country }}
                                                            </span>
                                                        @endisset
                                                    </div>
                                                </div>
                                                <div class="post-info">
                                                    <div class="tw-flex tw-flex-wrap tw-gap-3">
                                                        <span
                                                            class="tw-px-3 tw-py-1 tw-inline-block tw-text-sm tw-font-medium tw-text-[#474C54] tw-rounded-[52px] ll-gray-border">{{ $company?->industry?->name ?? '' }}</span>

                                                        @if ($company->activejobs !== 0)
                                                            <span
                                                                class="tw-px-3 tw-py-1 tw-inline-block tw-text-sm tw-font-medium tw-text-[#474C54] tw-rounded-[52px] ll-gray-border">{{ $company->activejobs }}
                                                                {{ __('open_job') }}</span>
                                                        @endif

                                                    </div>
                                                    <div
                                                        class="tw-absolute tw-bottom-6 tw-left-6 tw-text-base tw-font-semibold tw-capitalize tw-inline-flex tw-items-center tw-gap-1">
                                                        <span>{{ __('view_profile') }}</span>
                                                        <i class="ph-bold ph-arrow-right"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12">
                                    <div class="card text-center">
                                        <x-not-found message="{{ __('no_data_found') }}" />
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="rt-pt-30">
                            <nav>
                                {{ $companies->links('vendor.pagination.frontend') }}
                            </nav>
                        </div>
                    </div>
                    <div class="tab-pane" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        @if ($companies->count() > 0)
                            @foreach ($companies as $company)
                                <div class="card jobcardStyle1 body-24 rt-mb-24">
                                    <div class="card-body">
                                        <div class="rt-single-icon-box tw-flex-wrap">
                                            <div class="icon-thumb">
                                                <img src="{{ url($company->logo_url) }}" alt="logo"
                                                    draggable="false" class="object-fit-contain">
                                            </div>
                                            <div class="iconbox-content">
                                                <div class="post-info2">
                                                    <div class="post-main-title"> <a
                                                            href="{{ route('website.employe.details', $company->user->username) }}">{{ $company->user->name }}</a>
                                                    </div>
                                                    <div class="body-font-4 tw-space-y-2 text-gray-600 pt-2">
                                                        <span class="info-tools">
                                                            <svg width="22" height="22" viewBox="0 0 22 22"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M19.25 9.16699C19.25 15.5837 11 21.0837 11 21.0837C11 21.0837 2.75 15.5837 2.75 9.16699C2.75 6.97896 3.61919 4.88054 5.16637 3.33336C6.71354 1.78619 8.81196 0.916992 11 0.916992C13.188 0.916992 15.2865 1.78619 16.8336 3.33336C18.3808 4.88054 19.25 6.97896 19.25 9.16699Z"
                                                                    stroke="#C5C9D6" stroke-width="1.5"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path
                                                                    d="M11 11.917C12.5188 11.917 13.75 10.6858 13.75 9.16699C13.75 7.64821 12.5188 6.41699 11 6.41699C9.48122 6.41699 8.25 7.64821 8.25 9.16699C8.25 10.6858 9.48122 11.917 11 11.917Z"
                                                                    stroke="#C5C9D6" stroke-width="1.5"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>

                                                            {{ $company->exact_location ? $company->exact_location : $company->full_address }}
                                                        </span>
                                                        <span class="info-tools">
                                                            <svg width="22" height="22" viewBox="0 0 22 22"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M18.563 6.1875H3.43799C3.05829 6.1875 2.75049 6.4953 2.75049 6.875V17.875C2.75049 18.2547 3.05829 18.5625 3.43799 18.5625H18.563C18.9427 18.5625 19.2505 18.2547 19.2505 17.875V6.875C19.2505 6.4953 18.9427 6.1875 18.563 6.1875Z"
                                                                    stroke="#C5C9D6" stroke-width="1.3"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path
                                                                    d="M14.4375 6.1875V4.8125C14.4375 4.44783 14.2926 4.09809 14.0348 3.84023C13.7769 3.58237 13.4272 3.4375 13.0625 3.4375H8.9375C8.57283 3.4375 8.22309 3.58237 7.96523 3.84023C7.70737 4.09809 7.5625 4.44783 7.5625 4.8125V6.1875"
                                                                    stroke="#C5C9D6" stroke-width="1.3"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path
                                                                    d="M19.2506 10.8545C16.7432 12.3052 13.8967 13.0669 10.9999 13.0623C8.10361 13.0669 5.25759 12.3054 2.75049 10.8552"
                                                                    stroke="#C5C9D6" stroke-width="1.3"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path d="M9.96875 10.3125H12.0312" stroke="#C5C9D6"
                                                                    stroke-width="1.3" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>

                                                            {{ $company->activejobs }} - {{ __('open_job') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="iconbox-extra align-self-center">
                                                <div>
                                                    @if ($company->activejobs !== 0)
                                                        <a
                                                            href="{{ route('website.job', 'company=' . $company->user->username) }}">
                                                            <button type="button" class="btn btn-primary2-50">
                                                                <div class="button-content-wrapper ">
                                                                    <span class="button-icon align-icon-right">
                                                                        <i class="ph-arrow-right"></i>
                                                                    </span>
                                                                    <span class="button-text">
                                                                        {{ __('open_position') }}
                                                                    </span>
                                                                </div>
                                                            </button>
                                                        </a>
                                                    @else
                                                        <div class="text-dark border-0 text-center">
                                                            <div class="button-content-wrapper ">
                                                                <span class="button-text">
                                                                    {{ __('no_open_position') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-12">
                                <div class="card text-center">
                                    <x-not-found message="{{ __('no_data_found') }}" />
                                </div>
                            </div>
                        @endif
                        @if (request('perpage') != 'all')
                            <div class="rt-pt-30">
                                <nav>
                                    {{ $companies->links('vendor.pagination.frontend') }}
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="rt-spacer-100 rt-spacer-md-50"></div>

    {{-- Subscribe Newsletter --}}
    <x-website.subscribe-newsletter />
    {{-- filter modal --}}
    <form id="form" action="{{ route('website.company') }}">
        <div class="modal fade" id="companyFiltersModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div
                class="modal-dialog  modal-wrapper tw-mx-0 md:tw-max-w-[352px] tw-w-[90%] tw-my-0 tw-absolute tw-top-0 tw-bootom-0 tw-left-0">
                <div class="modal-content tw-rounded-none tw-relative tw-min-h-screen tw-max-h-screen">
                    <div class="tw-h-screen tw-overflow-x-hidden tw-overflow-y-auto tw-pb-24">
                        <div class="tw-p-5">
                            <div class="tw-flex tw-justify-between items-center">
                                <h2 class="tw-text-[#18191C] tw-text-xl tw-font-medium tw-mb-0">Filter</h2>
                                <button type="button" class="tw-p-0 tw-border-0 tw-bg-transparent"
                                    data-bs-dismiss="modal" aria-label="Close">
                                    <x-svg.close-icon />
                                </button>
                            </div>
                        </div>
                        <div class="tw-px-5">
                            <h2 class="tw-text-[#0A65CC] tw-text-sm tw-font-medium">Industry</h2>
                            <ul class="tw-flex tw-flex-col tw-list-none tw-p-0 tw-m-0">
                                <label for="industries" class="tw-block ll-filter-category__item">
                                    <input class="tw-scale-125" hidden type="radio" checked id="industries"
                                        value="" name="industry_type">
                                    <div
                                        class="tw-text-sm tw-text-[#18191C] tw-font-medium hover:tw-text-[#0A65CC] tw-flex tw-cursor-pointer hover:tw-bg-[#E7F0FA] tw-px-3 tw-py-2 tw-mt-[2px]">
                                        All Industry</div>
                                </label>
                                @foreach ($industries as $industry)
                                    <label for="{{ $industry->name }}_{{ $industry->id }}"
                                        class="tw-block ll-filter-category__item">
                                        <input {{ $industry->name == request('industry_type') ? 'checked' : '' }}
                                            data-id="{{ Route::current()->parameter('industry_type') }}"
                                            aria-data-id="category" type="radio" hidden
                                            id="{{ $industry->name }}_{{ $industry->id }}" class="tw-scale-125"
                                            name="industry_type" value="{{ $industry->name }}">
                                        <div
                                            class="tw-text-sm tw-text-[#18191C] tw-font-medium hover:tw-text-[#0A65CC] tw-flex tw-cursor-pointer hover:tw-bg-[#E7F0FA] tw-px-3 tw-py-2 tw-mt-[2px]">
                                            {{ $industry->name }}</div>
                                    </label>
                                @endforeach
                            </ul>
                        </div>
                        <hr class="tw-bg-[#E4E5E8] tw-m-0">
                        <div class="tw-px-5">
                            <h2 class="tw-text-sm tw-text-[#0A65CC] tw-mt-4 tw-font-medium">Organization</h2>
                            <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                                <input class="tw-scale-125" type="radio" id="organization_type" value=""
                                    name="organization_type">
                                <label for="organization_type" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">All
                                    Organization</label>
                            </div>
                            @foreach ($organization_types as $organization_type)
                                <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                                    <input {{ $organization_type->name == request('organization_type') ? 'checked' : '' }}
                                        data-id="{{ Route::current()->parameter('organization_type') }}"
                                        aria-data-id="category" type="radio"
                                        id="{{ $organization_type->name }}_{{ $organization_type->id }}"
                                        class="tw-scale-125" name="organization_type"
                                        value="{{ $organization_type->name }}">
                                    <label for="{{ $organization_type->name }}_{{ $organization_type->id }}"
                                        class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">{{ $organization_type->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <hr class="tw-bg-[#E4E5E8] tw-m-0">
                        <div class="tw-p-5">
                            <h2 class="tw-text-sm tw-text-[#0A65CC] tw-mb-4 tw-font-medium">Team Szie</h2>
                            <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                                <input type="radio" id="allTeam" value=" " class="tw-scale-125"
                                    name="team_size">
                                <label for="allTeam" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">All Team
                                    Size</label>
                            </div>

                            @foreach ($teamsizes as $teamsize)
                                <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                                    <input {{ $teamsize->name == request('team_size') ? 'checked' : '' }}
                                        data-id="{{ Route::current()->parameter('team_size') }}" aria-data-id="category"
                                        type="radio" id="{{ $teamsize->name }}_{{ $teamsize->id }}"
                                        class="tw-scale-125" name="team_size" value="{{ $teamsize->name }}">
                                    <label for="{{ $teamsize->name }}_{{ $teamsize->id }}"
                                        class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">{{ $teamsize->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div
                        class="tw-absolute tw-bottom-0 tw-left-0 tw-right-0 tw-p-5 tw-bg-white tw-z-50 tw-flex tw-justify-end tw-items-center tw-mt-3">
                        <div>
                            <button type="submit"
                                class="btn btn-primary tw-inline-block">{{ __('apply_filter') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('frontend_links')
    <x-map.leaflet.autocomplete_links />
    <style>
        .candidate-profession+.select2-container--default .select2-selection--single {
            border: none !important;
        }
    </style>
@endpush

@push('frontend_scripts')
    <x-map.leaflet.autocomplete_scripts />

    <script>
        // filter close
        function FilterClose(name) {
            $('[name="' + name + '"]').val('');
            console.log('ddd');
            $('#form').submit();
        }
        // autocomplete
        var path = "{{ route('website.job.autocomplete') }}";

        $('#search').keyup(function(e) {
            var keyword = $(this).val();

            if (keyword != '') {
                $.ajax({
                    url: path,
                    type: 'GET',
                    dataType: "json",
                    data: {
                        search: keyword
                    },
                    success: function(data) {
                        $('#autocomplete_job_results').fadeIn();
                        $('#autocomplete_job_results').html(data);
                    }
                });
            } else {
                $('#autocomplete_job_results').fadeOut();
            }
        });
    </script>
    <!-- ============== gooogle map ========== -->
    @if ($map == 'google-map')
        <script>
            function initMap() {
                var token = "{{ $setting->google_map_key }}";
                var oldlat = {{ Session::has('location') ? Session::get('location')['lat'] : $setting->default_lat }};
                var oldlng = {{ Session::has('location') ? Session::get('location')['lng'] : $setting->default_long }};
                const map = new google.maps.Map(document.getElementById("google-map"), {
                    zoom: 7,
                    center: {
                        lat: oldlat,
                        lng: oldlng
                    },
                });
                // Search
                var input = document.getElementById('searchInput');
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                let country_code = '{{ current_country_code() }}';
                if (country_code) {
                    var options = {
                        componentRestrictions: {
                            country: country_code
                        }
                    };
                    var autocomplete = new google.maps.places.Autocomplete(input, options);
                } else {
                    var autocomplete = new google.maps.places.Autocomplete(input);
                }

                autocomplete.bindTo('bounds', map);
                var infowindow = new google.maps.InfoWindow();
                var marker = new google.maps.Marker({
                    map: map,
                    anchorPoint: new google.maps.Point(0, -29)
                });
                autocomplete.addListener('place_changed', function() {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    const total = place.address_components.length;
                    let amount = '';
                    if (total > 1) {
                        amount = total - 2;
                    }
                    const result = place.address_components.slice(amount);
                    let country = '';
                    let region = '';
                    for (let index = 0; index < result.length; index++) {
                        const element = result[index];
                        if (element.types[0] == 'country') {
                            country = element.long_name;
                        }
                        if (element.types[0] == 'administrative_area_level_1') {
                            const str = element.long_name;
                            const first = str.split(',').shift()
                            region = first;
                        }
                    }
                    const text = country;
                    $('#insertlocation').val(text);
                    $('#lat').val(place.geometry.location.lat());
                    $('#long').val(place.geometry.location.lng());
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
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
    @endif
    <!-- ============== gooogle map ========== -->
@endpush
