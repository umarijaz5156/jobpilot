@extends('frontend.layouts.app')

@section('title')
    {{ __('settings') }}
@endsection
@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <x-website.company.sidebar />

                <div class="col-lg-9">
                    <div class="dashboard-right tw-ps-0 lg:tw-ps-5 tw-mt-4 lg:tw-mt-0">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <div class="lg:tw-mb-4">
                                <h5 class="tw-mb-0 tw-text-[#18191C] tw-text-2xl tw-font-medium">{{ __('settings') }}</h5>
                            </div>
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>
                        <div class="cadidate-dashboard-tabs company tw-min-h-[500px]">
                            <div class="tw-overflow-x-auto">
                                <ul class="nav nav-pills tw-gap-x-8" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="nav-link tw-px-3 {{ !session('type') || session('type') == 'personal' ? 'active' : '' }}"
                                            id="pills-personal-tab" data-bs-toggle="pill" data-bs-target="#pills-personal"
                                            type="button" role="tab" aria-controls="pills-personal"
                                            aria-selected="true">
                                            <x-svg.user-icon />
                                            {{ __('company_info') }}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link tw-px-3 {{ session('type') == 'profile' ? 'active' : '' }}"
                                            id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile"
                                            type="button" role="tab" aria-controls="pills-profile"
                                            aria-selected="false">
                                            <x-svg.user-round-icon />
                                            {{ __('founding_info') }}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link tw-px-3 {{ session('type') == 'social' ? 'active' : '' }}"
                                            id="pills-social-tab" data-bs-toggle="pill" data-bs-target="#pills-social"
                                            type="button" role="tab" aria-controls="pills-social"
                                            aria-selected="false">
                                            <x-svg.globe2-icon />
                                            {{ __('social_media_profile') }}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="nav-link tw-px-3 {{ session('type') == 'account' || session('type') == 'password' || session('type') == 'account-delete' || session('type') == 'contact' ? 'active' : '' }} @error('password') active @enderror"
                                            id="pills-setting-tab" data-bs-toggle="pill" data-bs-target="#pills-setting"
                                            type="button" role="tab" aria-controls="pills-setting"
                                            aria-selected="false">
                                            <x-svg.cog-icon />
                                            {{ __('account_setting') }}
                                        </button>
                                    </li>
                                    <span class="glider"></span>
                                </ul>
                            </div>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade {{ session('type') == 'personal' ? 'show active' : '' }} {{ (session('type') ? false : true) ? 'show active' : '' }}"
                                    id="pills-personal" role="tabpanel" aria-labelledby="pills-personal-tab">
                                    <form action="{{ route('company.settingUpdateInformation') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" value="personal" name="type">
                                        <div class="company-logo-banner-info">
                                            <h6>{{ __('logo_banner_image') }}</h6>
                                            <div class="row">
                                                <x-website.company.photo-section :user="$user" />
                                                <x-website.company.banner-section :user="$user" />
                                            </div>
                                        </div>
                                        <div class="dashboard-account-setting-item">
                                            <!-- <h6>{{ __('employers_information') }}</h6> -->
                                            <div class="row">
                                                <div class="col-lg-6 mb-3">
                                                    <x-forms.label name="company_name" required="true"
                                                        class="pointer body-font-4 d-block text-gray-900 rt-mb-8" />
                                                    <div class="fromGroup">
                                                        <div class="form-control-icon">
                                                            <x-forms.input type="text" name="name"
                                                                value="{{ $user->name }}" placeholder="name"
                                                                id="name" />

                                                            @error('name')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 mb-3">
                                                    <x-forms.label :required="false" name="about_us"
                                                        class="pointer body-font-4 d-block text-gray-900 rt-mb-8" />
                                                    <textarea class="form-control ckedit  @error('about_us') is-invalid @enderror" name="about_us" id="image_ckeditor">
                                                    {!! $user->company->bio !!}</textarea>
                                                    @error('about_us')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-12 mt-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('save_changes') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade {{ session('type') == 'profile' ? 'show active' : '' }}"
                                    id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                    <div class="dashboard-account-setting-item pb-0">
                                        <form action="{{ route('company.settingUpdateInformation') }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="type" value="profile">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <x-forms.label name="organization_type"
                                                        class="body-font-4 d-block text-gray-900 rt-mb-8" />
                                                    <select name="organization_type" class="select2-taggable w-100-p">
                                                        @foreach ($organization_types as $organization_type)
                                                            <option
                                                                {{ $user->company->organization_type_id == $organization_type->id ? 'selected' : '' }}
                                                                value="{{ $organization_type->id }}">
                                                                {{ $organization_type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <x-forms.label name="industry_type"
                                                        class="body-font-4 d-block text-gray-900 rt-mb-8" />
                                                    <select name="industry_type" class="select2-taggable w-100-p">
                                                        @foreach ($industry_types as $industry_type)
                                                            <option
                                                                {{ $user->company->industry_type_id == $industry_type->id ? 'selected' : '' }}
                                                                value="{{ $industry_type->id }}">
                                                                {{ $industry_type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <x-forms.label name="team_size"
                                                        class="body-font-4 d-block text-gray-900 rt-mb-8"
                                                        :required="false" />
                                                    <select name="team_size" class="rt-selectactive w-100-p">
                                                        @foreach ($team_sizes as $team_size)
                                                            <option
                                                                {{ $user->company->team_size_id == $team_size->id ? 'selected' : '' }}
                                                                value="{{ $team_size->id }}">
                                                                {{ $team_size->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <x-forms.label name="year_of_establishment"
                                                        class="body-font-4 d-block text-gray-900 rt-mb-8"
                                                        :required="false" />
                                                    <div class="fromGroup">
                                                        <div
                                                            class="d-flex align-items-center form-control-icon date datepicker">
                                                            <input type="text" name="establishment_date"
                                                                value="{{ $user->company->establishment_date ? date('d-m-Y', strtotime($user->company->establishment_date)) : old('establishment_date') }}"
                                                                id="date" placeholder="m/d/y"
                                                                class="form-control border-cutom @error('establishment_date') is-invalid @enderror" />
                                                            <label for="date" class="input-group-addon tw-cursor-pointer input-group-text-custom">
                                                                <x-svg.calendar-icon />
                                                            </label>
                                                        </div>
                                                        @error('establishment_date')
                                                            <span class="text-danger">{{ __($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <x-forms.label name="website" :required="false"
                                                        class="body-font-4 d-block text-gray-900 rt-mb-8" />
                                                    <div class="fromGroup has-icon2">
                                                        <div class="form-control-icon">
                                                            <x-forms.input type="text" name="website"
                                                                value="{{ $user->company->website }}"
                                                                placeholder="Website url..." class="" />
                                                            <div class="icon-badge-2">
                                                                <x-svg.link-icon />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 mb-3">
                                                    <x-forms.label name="company_vision" :required="false"
                                                        class="body-font-4 d-block text-gray-900 rt-mb-8" />
                                                    <textarea name="vision" class="ckedit" id="image_ckeditor_2">{{ $user->company->vision }}</textarea>
                                                </div>
                                                <div class="col-lg-12 mt-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('save_changes') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade {{ session('type') == 'social' ? 'show active' : '' }}"
                                    id="pills-social" role="tabpanel" aria-labelledby="pills-social-tab">
                                    <div class="dashboard-account-setting-item">
                                        <form action="{{ route('company.settingUpdateInformation') }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" value="social" name="type">
                                            <div class="row">
                                                @forelse($socials as $social)
                                                    <div class="col-12 custom-select-padding">
                                                        <div class="d-flex">
                                                            <div class="d-flex mborder">
                                                                <div class="position-relative">
                                                                    <select
                                                                        class="w-100-p border-0 rt-selectactive form-control"
                                                                        name="social_media[]">
                                                                        <option value="" class="d-none" disabled>
                                                                            {{ __('select_one') }}</option>
                                                                        <option
                                                                            {{ $social->social_media == 'facebook' ? 'selected' : '' }}
                                                                            value="facebook">{{ __('facebook') }}
                                                                        </option>
                                                                        <option
                                                                            {{ $social->social_media == 'twitter' ? 'selected' : '' }}
                                                                            value="twitter">{{ __('twitter') }}
                                                                        </option>
                                                                        <option
                                                                            {{ $social->social_media == 'instagram' ? 'selected' : '' }}
                                                                            value="instagram">{{ __('instagram') }}
                                                                        </option>
                                                                        <option
                                                                            {{ $social->social_media == 'youtube' ? 'selected' : '' }}
                                                                            value="youtube">{{ __('youtube') }}
                                                                        </option>
                                                                        <option
                                                                            {{ $social->social_media == 'linkedin' ? 'selected' : '' }}
                                                                            value="linkedin">{{ __('linkedin') }}
                                                                        </option>
                                                                        <option
                                                                            {{ $social->social_media == 'pinterest' ? 'selected' : '' }}
                                                                            value="pinterest">{{ __('pinterest') }}
                                                                        </option>
                                                                        <option
                                                                            {{ $social->social_media == 'reddit' ? 'selected' : '' }}
                                                                            value="reddit">{{ __('reddit') }}
                                                                        </option>
                                                                        <option
                                                                            {{ $social->social_media == 'github' ? 'selected' : '' }}
                                                                            value="github">{{ __('github') }}
                                                                        </option>
                                                                        <option
                                                                            {{ $social->social_media == 'other' ? 'selected' : '' }}
                                                                            value="other">{{ __('other') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div class="w-100">
                                                                    <input class="border-0" type="url" name="url[]"
                                                                        id=""
                                                                        placeholder="{{ __('profile_link_url') }}..."
                                                                        value="{{ $social->url }}">
                                                                </div>
                                                            </div>
                                                            <div class="tw-ms-2">
                                                                <button
                                                                    class="tw-w-12 tw-h-12 tw-border-0 tw-rounded tw-bg-[#F1F2F4] tw-inline-flex tw-justify-center tw-items-center"
                                                                    type="button" id="remove_item">
                                                                    <svg width="24" height="24"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z"
                                                                            stroke="#18191C" stroke-width="1.5"
                                                                            stroke-miterlimit="10" />
                                                                        <path d="M15 9L9 15" stroke="#18191C"
                                                                            stroke-width="1.5" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                        <path d="M15 15L9 9" stroke="#18191C"
                                                                            stroke-width="1.5" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 custom-select-padding">
                                                        <div class="d-flex">
                                                            <div class="d-flex mborder">
                                                                <div class="position-relative">
                                                                    <select
                                                                        class="w-100-p border-0 rt-selectactive form-control"
                                                                        name="social_media[]">
                                                                        <option value="" class="d-none" disabled
                                                                            selected>{{ __('select_one') }}</option>
                                                                        <option value="facebook">{{ __('facebook') }}
                                                                        </option>
                                                                        <option value="twitter">{{ __('twitter') }}
                                                                        </option>
                                                                        <option value="instagram">
                                                                            {{ __('instagram') }}
                                                                        </option>
                                                                        <option value="youtube">{{ __('youtube') }}
                                                                        </option>
                                                                        <option value="linkedin">{{ __('linkedin') }}
                                                                        </option>
                                                                        <option value="pinterest">
                                                                            {{ __('pinterest') }}
                                                                        </option>
                                                                        <option value="reddit">{{ __('reddit') }}
                                                                        </option>
                                                                        <option value="github">{{ __('github') }}
                                                                        </option>
                                                                        <option value="other">{{ __('other') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div class="w-100">
                                                                    <input class="border-0" type="url" name="url[]"
                                                                        id=""
                                                                        placeholder="{{ __('profile_link_url') }}...">
                                                                </div>
                                                            </div>
                                                            <div class="tw-ms-2">
                                                                <button
                                                                    class="tw-w-12 tw-h-12 tw-border-0 tw-rounded tw-bg-[#F1F2F4] tw-inline-flex tw-justify-center tw-items-center"
                                                                    type="button" id="remove_item">
                                                                    <svg width="24" height="24"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z"
                                                                            stroke="#18191C" stroke-width="1.5"
                                                                            stroke-miterlimit="10" />
                                                                        <path d="M15 9L9 15" stroke="#18191C"
                                                                            stroke-width="1.5" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                        <path d="M15 15L9 9" stroke="#18191C"
                                                                            stroke-width="1.5" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforelse
                                                <div id="multiple_feature_part2">
                                                </div>
                                                <div class="col-12">
                                                    <button class="btn tw-bg-[#F1F2F4] w-100 add-new-social"
                                                        onclick="add_features_field()" type="button">
                                                        <svg width="20" height="20" viewBox="0 0 20 20"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5Z"
                                                                stroke="#18191C" stroke-width="1.5"
                                                                stroke-miterlimit="10" />
                                                            <path d="M6.875 10H13.125" stroke="#18191C" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                            <path d="M10 6.875V13.125" stroke="#18191C" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        <span>{{ __('add_new_social_link') }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-4">
                                        {{ __('save_changes') }}
                                    </button>
                                    </form>
                                </div>
                                <div class="tab-pane fade {{ session('type') == 'account' || session('type') == 'password' || session('type') == 'account-delete' || session('type') == 'contact' || session('type') == 'account' ? 'show active' : '' }} @error('password') show active @enderror"
                                    id="pills-setting" role="tabpanel" aria-labelledby="pills-setting-tab">
                                    {{-- Google map key wrong warning  --}}
                                    <form action="{{ route('company.settingUpdateInformation') }}" method="POST">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="type" value="contact">
                                        <div class="dashboard-account-setting-item pb-0">
                                            <x-website.map.map-warning />
                                            <h6>
                                                {{ __('company_location') }}
                                                <small class="h6">
                                                    ({{ __('click_to_add_a_pointer') }})
                                                </small>
                                            </h6>
                                            @if (config('templatecookie.map_show'))
                                                <div class="row">

                                                    <div id="google-map-div"
                                                        class="{{ $setting->default_map == 'google-map' ? '' : 'd-none' }}">
                                                        <input id="searchInput" class="mapClass" type="text"
                                                            placeholder="Enter a location">
                                                        <div class="map mymap" id="google-map"></div>
                                                    </div>
                                                    <div class="{{ $setting->default_map == 'leaflet' ? '' : 'd-none' }}">
                                                        <input type="text" autocomplete="off" id="leaflet_search"
                                                            placeholder="{{ __('enter_city_name') }}" class="full-width"
                                                            value="{{ $user->company->exact_location ? $user->company->exact_location : $user->company->full_address }}" />
                                                        <br>
                                                        <div id="leaflet-map"></div>
                                                    </div>
                                                    @error('location')
                                                        <span class="ml-3 text-md text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                @php
                                                    $session_location = session()->get('location');
                                                    $session_country = $session_location && array_key_exists('country', $session_location) ? $session_location['country'] : '-';
                                                    $session_exact_location = $session_location && array_key_exists('exact_location', $session_location) ? $session_location['exact_location'] : '-';

                                                    $company_country = $user->company->country;
                                                    $company_exact_location = $user->company->exact_location;
                                                @endphp
                                                <div class="card-footer row mt-4 border-0">
                                                    <span>
                                                        <img src="{{ asset('frontend/assets/images/loader.gif') }}"
                                                            alt="loading" width="50px" height="50px"
                                                            class="loader_position d-none">
                                                    </span>
                                                    <div class="location_secion">
                                                        {{ __('country') }}: <span
                                                            class="location_country">{{ $company_country ?: $session_country }}</span>
                                                        <br>
                                                        {{ __('full_address') }}: <span
                                                            class="location_full_address">{{ $company_exact_location ?: $session_exact_location }}</span>
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
                                                        'selectedCountryId' => $user->company->country,
                                                        'selectedStateId' => $user->company->region,
                                                        'selectedCityId' => $user->company->district,
                                                    ]);
                                                @endphp
                                                @livewire('country-state-city')
                                            @endif
                                        </div>

                                        <div class="dashboard-account-setting-item">
                                            <h6>{{ __('company_contact_public') }}</h6>
                                            <div class="row tw-mb-8">
                                                <div class="col-lg-6 tw-mb-3 md:tw-mb-0">
                                                    <x-forms.label :required="false" name="phone"
                                                        class="pointer tw-text-sm d-block text-gray-900 rt-mb-8" />
                                                    <x-forms.input type="text" id="phone" name="phone"
                                                        value="{{ $contact->phone }}"
                                                        placeholder="{{ __('phone_number') }}" class="phonecode" />
                                                </div>
                                                <div class="col-lg-6">
                                                    <x-forms.label :required="false" name="email"
                                                        class="pointer tw-text-sm d-block text-gray-900 rt-mb-8" />
                                                    <div class="fromGroup has-icon2">
                                                        <div class="form-control-icon">
                                                            <x-forms.input type="email" name="email"
                                                                value="{{ $contact->email }}"
                                                                placeholder="{{ __('email_address') }}" class="" />
                                                            <div class="icon-badge-2">
                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M21 5.25L12 13.5L3 5.25"
                                                                        stroke="var(--primary-500)" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                                    <path
                                                                        d="M3 5.25H21V18C21 18.1989 20.921 18.3897 20.7803 18.5303C20.6397 18.671 20.4489 18.75 20.25 18.75H3.75C3.55109 18.75 3.36032 18.671 3.21967 18.5303C3.07902 18.3897 3 18.1989 3 18V5.25Z"
                                                                        stroke="var(--primary-500)" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                                    <path d="M10.3628 12L3.23047 18.538"
                                                                        stroke="var(--primary-500)" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                                    <path d="M20.7692 18.5381L13.6367 12"
                                                                        stroke="var(--primary-500)" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('save_changes') }}
                                            </button>
                                        </div>

                                    </form>
                                    <form action="{{ route('company.settingUpdateInformation') }}" method="POST">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="type" value="account">
                                        <div class="dashboard-account-setting-item">
                                            <h6>{{ __('change_account_user_name_and_email') }} </h6>
                                            <div class="row tw-mb-8">

                                                {{-- user name update --}}
                                                <div class="col-lg-8 mt-2">
                                                    <div class="mb-2">
                                                        <x-forms.label :required="false" name="username"
                                                            class="pointer tw-text-sm d-block text-gray-900 rt-mb-8" />
                                                        <x-forms.input type="text" id="username" name="username"
                                                            value="{{ $user->username }}"
                                                            placeholder="{{ __('username') }}" class="phonecode" />
                                                        <span id="username_error"
                                                            class="invalid-feedback d-none">{{ __('username_has_already_been_taken') }}</span>

                                                    </div>
                                                    <p><b>{{ __('profile_link') }}: </b>
                                                        <a href="{{ config('app.url') }}/employer/{{ $user->username }}"
                                                            target="_blank"> {{ config('app.url') }}/employer/<span
                                                                id="profile_username">{{ $user->username }}</span>
                                                        </a>
                                                    </p>
                                                </div>

                                                {{-- emailupdate --}}
                                                <div class="col-lg-4 mt-2">
                                                    <x-forms.label :required="true" name="email"
                                                        class="f-size-14 text-gray-700 rt-mb-8" />
                                                    <div class="fromGroup rt-mb-15">
                                                        <input name="account_email" value="{{ auth()->user()->email }}"
                                                            class="form-control @error('account_email') is-invalid @enderror"
                                                            id="account_email" type="email"
                                                            placeholder="{{ __('email_address') }}" required>

                                                    </div>
                                                    @if (session('requested_email'))
                                                        <small> Your email address {{ session('requested_email') }} is
                                                            unverified . Check you email </small>
                                                    @endif
                                                    @error('account_email')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('save_changes') }}
                                            </button>
                                        </div>
                                    </form>

                                    <hr>
                                    <div class="dashboard-account-setting-item setting-border">
                                        <h6>{{ __('change_password') }}</h6>
                                        <form action="{{ route('company.settingUpdateInformation') }}" method="POST">
                                            @csrf
                                            @method('put')
                                            <input type="hidden" name="type" value="password">
                                            <div class="row">
                                                <div class="col-lg-6 rt-mb-32">
                                                    <x-forms.label :required="true" name="new_password"
                                                        class="f-size-14 text-gray-700 rt-mb-8" />
                                                    <div class="d-flex fromGroup rt-mb-15">
                                                        <input name="password"
                                                            class="form-control @error('password') is-invalid @enderror"
                                                            id="password-hide_show" type="password"
                                                            placeholder="{{ __('password') }}" required="">
                                                        <div class="has-badge">
                                                            <i class="ph-eye @error('password') m-3 @enderror"></i>
                                                        </div>
                                                    </div>
                                                    @error('password')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6 rt-mb-32">
                                                    <x-forms.label :required="true" name="confirm_password"
                                                        class="f-size-14 text-gray-700 rt-mb-8" />
                                                    <div class="fromGroup rt-mb-15">
                                                        <input name="password_confirmation"
                                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                                            id="password-hide_show1" type="password"
                                                            placeholder="{{ __('confirm_password') }}" required="">
                                                        <div class="has-badge select-icon__one">
                                                            <i class="ph-eye"></i>
                                                        </div>
                                                    </div>
                                                    @error('password_confirmation')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('save_changes') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="dashboard-account-setting-item setting-border">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <h4>{{ __('close') }}/{{ __('delete') }} {{ __('account') }}</h4>
                                                <p>{{ __('account_delete_msg') }}</p>
                                                <form action="{{ route('company.settingUpdateInformation') }}"
                                                    id="AccountDelete" method="POST">
                                                    @csrf
                                                    @method('put')
                                                    <input type="hidden" name="type" value="account-delete">
                                                    <button type="button" onclick="AccountDelete()"
                                                        class="btn p-0 text-danger-500">
                                                        <span class="button-content-wrapper ">
                                                            <span class="button-icon">
                                                                <i class="ph-x-circle"></i>
                                                            </span>
                                                            <span class="button-text">
                                                                {{ __('close_account') }}
                                                            </span>
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-footer text-center body-font-4 text-gray-500">
            <x-website.footer-copyright />
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/bootstrap-datepicker.min.css">
    <!-- >=>Leaflet Map<=< -->
    <x-map.leaflet.map_links />
    <x-map.leaflet.autocomplete_links />
    @include('map::links')
    <style>
        .ck-editor__editable_inline {
            min-height: 350px;
        }

        .input-group-text-custom {
            max-height: 48px;
            padding: 12px;
            background-color: #e9ecef;
            border-radius: 0 5px 5px 0;
        }

        .has-badge-cutom {
            top: 34% !important;
        }

        .border-cutom {
            border-radius: 5px 0 0 5px !important;
        }
    </style>

    <style>
        .mymap {
            border-radius: 12px;
        }
    </style>
@endsection

@section('script')
    @livewireScripts
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap-datepicker.min.js') }}"></script>
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
    <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>

    {{-- Leaflet  --}}
    @include('map::set-edit-leafletmap', ['lat' => $user->company->lat, 'long' => $user->company->long])
    <script>
        $('#username').keyup(function() {
            var username = $(this).val();

            if (username.length) {
                axios.get('/check/username/' + username, {
                        params: {
                            type: "company_username"
                        }
                    })
                    .then(function(response) {
                        var exists = response.data

                        if (exists) {
                            $('#username').addClass('is-invalid')
                            $('#username_error').removeClass('d-none')
                            $('#username_submit_btn').attr('disabled', 'disabled')
                        } else {
                            $('#username').removeClass('is-invalid')
                            $('#username_error').addClass('d-none')
                            $('#username_submit_btn').removeAttr('disabled')
                        }

                        $('#profile_username').html(username);
                    })
            }
        });

        function UploadMode(param) {
            if (param === 'photo') {
                $('#photo-uploadMode').removeClass('d-none');
                $('#photo-oldMode').addClass('d-none');
            } else {
                $('#banner-uploadMode').removeClass('d-none');
                $('#banner-oldMode').addClass('d-none');
            }
        }
        //init datepicker
        // $("#date").attr("autocomplete", "off");
        // //init datepicker
        // $('#date').off('focus').datepicker({
        //     format: 'dd-mm-yyyy',
        //     isRTL: "{{ app()->getLocale() == 'ar' ? true : false }}",
        //     language: "{{ app()->getLocale() }}",
        // }).on('click',
        //     function() {
        //         $(this).datepicker('show');
        //     }
        // );
        $('#date').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        // feature field
        function add_features_field() {
            $("#multiple_feature_part2").append(`
            <div class="col-12 custom-select-padding">
                <div class="d-flex tw-items-center">
                    <div class="d-flex mborder">
                        <div class="position-relative">
                            <select
                                class="w-100-p border-0 rt-selectactive-2 form-control" name="social_media[]">
                                <option value="" class="d-none" disabled selected>{{ __('select_one') }}</option>
                                <option value="facebook">{{ __('facebook') }}</option>
                                <option value="twitter">{{ __('twitter') }}</option>
                                <option value="instagram">{{ __('instagram') }}</option>
                                <option value="youtube">{{ __('youtube') }}</option>
                                <option value="linkedin">{{ __('linkedin') }}</option>
                                <option value="pinterest">{{ __('pinterest') }}</option>
                                <option value="reddit">{{ __('reddit') }}</option>
                                <option value="github">{{ __('github') }}</option>
                                <option value="other">{{ __('other') }}</option>
                            </select>
                        </div>
                        <div class="w-100">
                            <input class="border-0" type="url" name="url[]" id="" placeholder="{{ __('profile_link_url') }}...">
                        </div>
                    </div>
                    <div class="tw-ms-2">
                        <button class="tw-w-12 tw-h-12 tw-border-0 tw-rounded tw-bg-[#F1F2F4] tw-inline-flex tw-justify-center tw-items-center" type="button" id="remove_item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="#18191C" stroke-width="1.5" stroke-miterlimit="10"/>
                                <path d="M15 9L9 15" stroke="#18191C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15 15L9 9" stroke="#18191C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `);
            $(".rt-selectactive-2").select2({ // minimumResultsForSearch: Infinity,
            });
        }
        $(document).on("click", "#remove_item", function() {
            $(this).parent().parent().parent('div').remove();
        });

        $('#visibility').on('change', function() {
            $(this).submit();
        });
        $('#alert').on('change', function() {
            $(this).submit();
        });

        function AccountDelete() {
            if (confirm("{{ __('are_you_sure') }}") == true) {
                $('#AccountDelete').submit();
            } else {
                return false;
            }
        }
        setTimeout(function() {
            {{ session()->forget('type') }}
        }, 10000);

        var item = {!! $user->company !!};
    </script>

    @if ($setting->default_map == 'google-map')
        <!-- ============== google map ========= -->
        <x-website.map.google-map-check />
        <script>
            function initMap() {
                var token = "{{ $setting->google_map_key }}";
                var oldlat = parseFloat(item.lat);
                var oldlng = parseFloat(item.long);
                const map = new google.maps.Map(document.getElementById("google-map"), {
                    zoom: 7,
                    center: {
                        lat: oldlat,
                        lng: oldlng
                    },
                });
                const image =
                    "https://gisgeography.com/wp-content/uploads/2018/01/map-marker-3-116x200.png";
                const beachMarker = new google.maps.Marker({
                    draggable: true,
                    position: {
                        lat: oldlat,
                        lng: oldlng
                    },
                    map,
                    // icon: image
                });
                google.maps.event.addListener(map, 'click',
                    function(event) {
                        $('.loader_position').removeClass('d-none');
                        $('.location_secion').addClass('d-none');

                        pos = event.latLng
                        beachMarker.setPosition(pos);
                        let lat = beachMarker.position.lat();
                        let lng = beachMarker.position.lng();
                        axios.post(
                            `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${token}`
                        ).then((data) => {
                            if (data.data.error_message) {
                                toastr.error(data.data.error_message, 'Error!');
                                toastr.error('Your location is not set because of a wrong API key.', 'Error!');
                            }

                            const total = data.data.results.length;
                            let amount = '';
                            if (total > 1) {
                                amount = total - 2;
                            }
                            const result = data.data.results.slice(amount);
                            let country = '';
                            let region = '';
                            for (let index = 0; index < result.length; index++) {
                                const element = result[index];
                                if (element.types[0] == 'country') {
                                    country = element.formatted_address;
                                }
                                if (element.types[0] == 'administrative_area_level_1') {
                                    const str = element.formatted_address;
                                    const first = str.split(',').shift()
                                    region = first;
                                }
                            }
                            var form = new FormData();
                            form.append('lat', lat);
                            form.append('lng', lng);
                            form.append('country', country);
                            form.append('region', region);
                            form.append('exact_location', data.data.results[0].formatted_address);

                            setLocationSession(form);

                            $('.location_country').text(country);
                            $('.location_full_address').text(data.data.results[0].formatted_address ||
                                'No address found');
                            $('.loader_position').addClass('d-none');
                            $('.location_secion').removeClass('d-none');
                        })
                    });
                google.maps.event.addListener(beachMarker, 'dragend',
                    function() {
                        $('.loader_position').removeClass('d-none');
                        $('.location_secion').addClass('d-none');

                        let lat = beachMarker.position.lat();
                        let lng = beachMarker.position.lng();
                        axios.post(
                            `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${token}`
                        ).then((data) => {
                            if (data.data.error_message) {
                                toastr.error(data.data.error_message, 'Error!');
                                toastr.error('Your location is not set because of a wrong API key.', 'Error!');
                            }

                            const total = data.data.results.length;
                            let amount = '';
                            if (total > 1) {
                                amount = total - 2;
                            }
                            const result = data.data.results.slice(amount);
                            let country = '';
                            let region = '';
                            for (let index = 0; index < result.length; index++) {
                                const element = result[index];
                                if (element.types[0] == 'country') {
                                    country = element.formatted_address;
                                }
                                if (element.types[0] == 'administrative_area_level_1') {
                                    const str = element.formatted_address;
                                    const first = str.split(' ').shift()
                                    region = first;
                                }
                            }
                            var form = new FormData();
                            form.append('lat', lat);
                            form.append('lng', lng);
                            form.append('country', country);
                            form.append('region', region);
                            form.append('exact_location', data.data.results[0].formatted_address);

                            setLocationSession(form);

                            $('.location_country').text(country);
                            $('.location_full_address').text(data.data.results[0].formatted_address ||
                                'No address found');
                            $('.loader_position').addClass('d-none');
                            $('.location_secion').removeClass('d-none');
                        })
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
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }
                });
            }
            window.initMap = initMap;

            @php
                $link1 = 'https://maps.googleapis.com/maps/api/js?key=';
                $link2 = $setting->google_map_key;
                $Link3 = '&callback=initMap&libraries=places,geometry';
                $scr = $link1 . $link2 . $Link3;
            @endphp;
        </script>
        <script src="{{ $scr }}" async defer></script>
    @endif

    <script>
        $('#pills-setting-tab').on('click', function() {
            setTimeout(() => {
                leaflet_map.invalidateSize(true);
            }, 200);
        })
    </script>
@endsection
