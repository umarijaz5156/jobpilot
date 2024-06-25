@props(['user'])

<form action="{{ route('company.profile.complete', auth()->user()->id) }}" method="post">
    @method('PUT')
    @csrf
    <input type="hidden" name="field" value="contact">
    <fieldset>
        <div class="form-card mb-4">
            <div class="dashboard-account-setting-item pb-0">
                @if (config('templatecookie.map_show'))
                    <h6>{{ __('company_location') }}
                        <span class="text-danger">*</span>
                        <small class="h6">
                            ({{ __('click_to_add_a_pointer') }})
                        </small>
                    </h6>
                    <div class="row">
                        <x-website.map.map-warning />
                        @php
                            $map = $setting->default_map;
                        @endphp
                        <div class="{{ $map == 'leaflet' ? '' : 'd-none' }}">
                            <input type="text" autocomplete="off" id="leaflet_search"
                                placeholder="{{ __('enter_city_name') }}" class="form-control"
                                value="{{ $user->company->exact_location ? $user->company->exact_location : $user->company->full_address }}" />
                            <br>
                            <div id="leaflet-map"></div>
                        </div>
                        <div id="google-map-div" class="{{ $map == 'google-map' ? '' : 'd-none' }}">
                            <input id="searchInput" class="mapClass" type="text" placeholder="Enter a location">
                            <div class="map mymap" id="google-map"></div>
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
                            <img src="{{ asset('frontend/assets/images/loader.gif') }}" alt="loading" width="50px"
                                height="50px" class="loader_position d-none">
                        </span>
                        <div class="location_secion">
                            {{ __('country') }}: <span
                                class="location_country">{{ $company_country ?: $session_country }}</span> <br>
                            {{ __('full_address') }}: <span
                                class="location_full_address">{{ $company_exact_location ?: $session_exact_location }}</span>
                        </div>
                    </div>
                @else
                    <x-forms.label name="location" :required="true" class="tw-text-sm tw-mb-2" />
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
            <div class="dashboard-account-setting-item">
                <h6>{{ __('phone_email') }}</h6>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="pointer body-font-4 d-block text-gray-900 rt-mb-8">
                            {{ __('phone') }}
                            <x-forms.required />
                        </label>
                        <input class="phonecode @error('phone') is-invalid border-danger @enderror" name="phone"
                            type="text" value="{{ old('phone', $user->contactInfo->phone) }}"
                            placeholder="{{ __('phone') }}" />
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ __($message) }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="pointer body-font-4 d-block text-gray-900 rt-mb-8">
                            {{ __('email') }}
                            <x-forms.required />
                        </label>
                        <div class="fromGroup has-icon2">
                            <div class="form-control-icon">
                                <input class="form-control @error('email') is-invalid @enderror" name="email"
                                    type="text" placeholder="{{ __('email_address') }}"
                                    value="{{ old('email', $user->contactInfo->email) }}">
                                <div class="icon-badge-2">
                                    <x-svg.envelope-icon width="24" height="24" />
                                </div>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback  d-block" role="alert">
                                <strong>{{ __($message) }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

            </div>
        </div>
        <a href="{{ url('company/account-progress?social') }}">
            <button type="button" class="btn previous bg-gray-50 rt-mr-8">
                {{ __('previous') }}
            </button>
        </a>
        <button type="submit" class="btn next btn-primary hide-menu-btn">
            <span class="button-content-wrapper ">
                <span class="button-icon align-icon-right">
                    <i class="ph-arrow-right"></i>
                </span>
                <span class="button-text">
                    {{ __('save_next') }}
                </span>
            </span>
        </button>
    </fieldset>
</form>
