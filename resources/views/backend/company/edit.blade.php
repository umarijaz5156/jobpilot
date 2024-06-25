@extends('backend.layouts.app')
@section('title')
    {{ __('update') }} {{ __('employer') }}
@endsection
@section('content')
    <div class="container-fluid">
        <form class="form-horizontal" action="{{ route('company.update', $company->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title line-height-36">{{ __('update') }} {{ __('employer') }}</h4>
                    <button type="submit"
                        class="btn bg-success float-right d-flex align-items-center justify-content-center">
                        <i class="fas fa-sync mr-1"></i>
                        {{ __('save') }}
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            {{ __('account_details') }}
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <x-forms.label name="employer_name" :required="true" />
                                <x-forms.input type="text" name="name" data-show-errors="true" placeholder="name"
                                    value="{{ old('name', $user->name) }}" />
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <x-forms.label name="username" :required="false" />
                                    <x-forms.input type="text" name="username" placeholder="username"
                                        value="{{ old('username', $user->username) }}" />
                                </div>
                                <div class="form-group col-sm-6">
                                    <x-forms.label name="email" />
                                    <x-forms.input type="email" name="email" placeholder="email"
                                        value="{{ old('email', $user->email) }}" />
                                </div>

                            </div>
                            <div class="form-group">
                                <x-forms.label name="change_password" />
                                <x-forms.input type="password" name="password" placeholder="password" />
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        @if (config('templatecookie.map_show'))
                            <div class="card-header">
                                {{ __('location') }}
                                <span class="text-red font-weight-bold">*</span>
                                <small class="h6">
                                    ({{ __('click_to_add_a_pointer') }})
                                </small>
                            </div>
                            <div class="card-body">
                                <x-website.map.map-warning />

                                @php
                                    $map = $setting->default_map;
                                @endphp
                                <div id="google-map-div" class="{{ $map == 'google-map' ? '' : 'd-none' }}">
                                    <input id="searchInput" class="mapClass" type="text" placeholder="Enter a location">
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
                                    'selectedCountryId' => $company->country,
                                    'selectedStateId' => $company->region,
                                    'selectedCityId' => $company->district,
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
                    <div class="card">
                        <div class="card-header">
                            {{ __('contact') }}
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <x-forms.label name="phone" />
                                    <x-forms.input type="text" name="contact_phone" placeholder="phone"
                                        value="{{ old('contact_phone', $user->contactInfo->phone) }}" />
                                </div>
                                <div class="form-group col-sm-6">
                                    <x-forms.label name="email" />
                                    <x-forms.input type="email" name="contact_email" placeholder="email"
                                        value="{{ old('contact_email', $user->contactInfo->email) }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            {{ __('images') }}
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-xl-4">
                                    <x-forms.label name="logo" :required="false" />
                                    <input name="logo" type="file" data-show-errors="true" data-width="50%"
                                        class="dropify" data-default-file="{{ $company->logo_url }}">
                                    <p class="tw-text-gray-500 tw-text-xs tw-text-left mt-2 recommended-img-note mb-0">
                                        Recommended Image Size: 68x68</p>
                                </div>
                                <div class="form-group col-xl-8">
                                    <x-forms.label name="banner" :required="false" />
                                    <input name="image" type="file" data-show-errors="true" data-width="100%"
                                        data-default-file="{{ $company->banner_url }}" class="dropify">
                                    <p class="tw-text-gray-500 tw-text-xs tw-text-left mt-2 recommended-img-note mb-0">
                                        Recommended Image Size: 1920x312</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            {{ __('social_details') }}
                        </div>
                        <div class="card-body">
                            <div id="multiple_feature_part">
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-4">
                                        <select
                                            class="form-control select2bs4 @error('social_media') border-danger @enderror"
                                            name="social_media[]">
                                            <option value="" class="d-none" disabled>{{ __('select_one') }}
                                            </option>
                                            <option {{ old('social_media') == 'facebook' ? 'selected' : '' }}
                                                value="facebook">{{ __('facebook') }}</option>
                                            <option {{ old('social_media') == 'twitter' ? 'selected' : '' }}
                                                value="twitter">{{ __('twitter') }}</option>
                                            <option {{ old('social_media') == 'instagram' ? 'selected' : '' }}
                                                value="instagram">{{ __('instagram') }}
                                            </option>
                                            <option {{ old('social_media') == 'youtube' ? 'selected' : '' }}
                                                value="youtube">{{ __('youtube') }}</option>
                                            <option {{ old('social_media') == 'linkedin' ? 'selected' : '' }}
                                                value="linkedin">{{ __('linkedin') }}</option>
                                            <option {{ old('social_media') == 'pinterest' ? 'selected' : '' }}
                                                value="pinterest">{{ __('pinterest') }}
                                            </option>
                                            <option {{ old('social_media') == 'reddit' ? 'selected' : '' }}
                                                value="reddit">{{ __('reddit') }}</option>
                                            <option {{ old('social_media') == 'github' ? 'selected' : '' }}
                                                value="github">{{ __('github') }}</option>
                                            <option {{ old('social_media') == 'other' ? 'selected' : '' }} value="other">
                                                {{ __('other') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="url" name="url[]" class="form-control">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <a role="button" onclick="add_features_field()"
                                            class="btn bg-primary text-light"><i class="fas fa-plus"></i></a>
                                    </div>
                                </div>
                                @forelse($socials as $social)
                                    <div class="row justify-content-center">
                                        <div class="form-group col-md-4">
                                            <select class="form-control @error('social_media') border-danger @enderror"
                                                name="social_media[]">
                                                <option value="" class="d-none" disabled>{{ __('select_one') }}
                                                </option>
                                                <option {{ $social->social_media == 'facebook' ? 'selected' : '' }}
                                                    value="facebook">{{ __('facebook') }}</option>
                                                <option {{ $social->social_media == 'twitter' ? 'selected' : '' }}
                                                    value="twitter">{{ __('twitter') }}</option>
                                                <option {{ $social->social_media == 'instagram' ? 'selected' : '' }}
                                                    value="instagram">{{ __('instagram') }}
                                                </option>
                                                <option {{ $social->social_media == 'youtube' ? 'selected' : '' }}
                                                    value="youtube">{{ __('youtube') }}</option>
                                                <option {{ $social->social_media == 'linkedin' ? 'selected' : '' }}
                                                    value="linkedin">{{ __('linkedin') }}</option>
                                                <option {{ $social->social_media == 'pinterest' ? 'selected' : '' }}
                                                    value="pinterest">{{ __('pinterest') }}
                                                </option>
                                                <option {{ $social->social_media == 'reddit' ? 'selected' : '' }}
                                                    value="reddit">{{ __('reddit') }}</option>
                                                <option {{ $social->social_media == 'github' ? 'selected' : '' }}
                                                    value="github">{{ __('github') }}</option>
                                                <option {{ $social->social_media == 'other' ? 'selected' : '' }}
                                                    value="other">
                                                    {{ __('other') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <input type="url" name="url[]" class="form-control"
                                                value="{{ $social->url }}" placeholder="{{ __('url') }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <a role="button" id="remove_item" class="btn bg-danger text-light"><i
                                                    class="fas fa-times"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            {{ __('profile_details') }}
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <x-forms.label name="organization_type" />
                                    <select name="organization_type_id"
                                        class="form-control select2bs4 {{ error('organization_type_id') }}"
                                        id="organization_type_id">
                                        @foreach ($organization_types as $type)
                                            <option
                                                {{ $type->id == old('organization_type_id', $company->organization_type_id) ? 'selected' : '' }}
                                                value="{{ $type->id }}">
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-forms.error name="organization_type_id" />
                                </div>
                                <div class="form-group col-md-6">
                                    <x-forms.label name="industry_type" />
                                    <select name="industry_type_id"
                                        class="form-control select2bs4 {{ error('industry_type_id') }}"
                                        id="organization_type_id">
                                        <option value="" class="d-none">
                                            {{ __('select_one') }}
                                        </option>
                                        @foreach ($industry_types as $type)
                                            <option
                                                {{ $type->id == old('industry_type_id', $company->industry_type_id) ? 'selected' : '' }}
                                                value="{{ $type->id }}">
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-forms.error name="industry_type_id" />
                                </div>
                                <div class="form-group col-md-6">
                                    <x-forms.label name="team_size" :required="false" />
                                    <select name="team_size_id" class="form-control {{ error('team_size_id') }}"
                                        id="organization_type_id">
                                        <option value="" class="d-none">
                                            {{ __('select_one') }}
                                        </option>
                                        @foreach ($team_sizes as $size)
                                            <option
                                                {{ $size->id == old('team_size_id', $company->team_size_id) ? 'selected' : '' }}
                                                value="{{ $size->id }}">
                                                {{ $size->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-forms.error name="team_size_id" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group datepicker col-md-6">
                                    <x-forms.label name="website" :required="false" />
                                    <x-forms.input type="text" name="website" placeholder="website"
                                        value="{{ old('website', $company->website) }}" />
                                    <x-forms.error name="establishment_date" />
                                </div>
                                <div class="form-group datepicker col-md-6">
                                    <x-forms.label name="establishment_date" :required="false" />
                                    <x-forms.input type="text" name="establishment_date" placeholder="select_one"
                                        id="establishment_date"
                                        value="{{ old('establishment_date', formatTime($company->establishment_date, 'd-m-Y')) }}" />
                                    <x-forms.error name="establishment_date" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <x-forms.label name="bio" :required="false" />
                                    <textarea id="image_ckeditor" rows="8" name="bio" placeholder="{{ __('bio') }}"
                                        class="form-control">{{ old('bio', $company->bio) }}</textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <x-forms.label name="vision" :required="false" />
                                    <textarea id="image_ckeditor_2" rows="8" name="vision" placeholder="{{ __('vision') }}"
                                        class="form-control">{{ old('vision', $company->vision) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/bootstrap-datepicker.min.css">

    <style>
        .ck-editor__editable_inline {
            min-height: 400px;
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
    <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('backend') }}/plugins/dropify/js/dropify.min.js"></script>
    <script src="{{ asset('frontend') }}/assets/js/axios.min.js"></script>
    @if (app()->getLocale() == 'ar')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.ar.min.js
                    "></script>
    @endif
    <script>
        $('.dropify').dropify();

        //init datepicker
        $(document).ready(function() {
            $('#establishment_date').datepicker({
                format: 'dd-mm-yyyy',
                isRTL: "{{ app()->getLocale() == 'ar' ? true : false }}",
                language: "{{ app()->getLocale() }}",
            });
        });

        $(document).on("click", "#remove_item", function() {
            $(this).parent().parent('div').remove();
        });

        function add_features_field() {
            $("#multiple_feature_part").append(`
            <div class="row justify-content-center">
                <div class="form-group col-md-4">
                    <select class="form-control @error('social_media') border-danger @enderror"
                        name="social_media[]">
                        <option value="" class="d-none" disabled>{{ __('select_one') }}
                        </option>
                        <option {{ old('social_media') == 'facebook' ? 'selected' : '' }}
                            value="facebook">{{ __('facebook') }}</option>
                        <option {{ old('social_media') == 'twitter' ? 'selected' : '' }}
                            value="twitter">{{ __('twitter') }}</option>
                        <option {{ old('social_media') == 'instagram' ? 'selected' : '' }}
                            value="instagram">{{ __('instagram') }}
                        </option>
                        <option {{ old('social_media') == 'youtube' ? 'selected' : '' }}
                            value="youtube">{{ __('youtube') }}</option>
                        <option {{ old('social_media') == 'linkedin' ? 'selected' : '' }}
                            value="linkedin">{{ __('linkedin') }}</option>
                        <option {{ old('social_media') == 'pinterest' ? 'selected' : '' }}
                            value="pinterest">{{ __('pinterest') }}
                        </option>
                        <option {{ old('social_media') == 'reddit' ? 'selected' : '' }}
                            value="reddit">{{ __('reddit') }}</option>
                        <option {{ old('social_media') == 'github' ? 'selected' : '' }}
                            value="github">{{ __('github') }}</option>
                        <option {{ old('social_media') == 'other' ? 'selected' : '' }} value="other">
                            {{ __('other') }}</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <input type="url" name="url[]" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <a role="button" id="remove_item"
                        class="btn bg-danger text-light"><i class="fas fa-times"></i></a>
                </div>
            </div>
            `);
        }
    </script>

    {{-- Leaflet  --}}
    @include('map::set-edit-leafletmap', ['lat' => $company->lat, 'long' => $company->long])

    <!-- ============== google map ========= -->
    <x-website.map.google-map-check />
    <script>
        function initMap() {
            var token = "{{ $setting->google_map_key }}";
            var oldlat = {!! $company->lat ? $company->lat : $setting->default_lat !!};
            var oldlng = {!! $company->long ? $company->long : $setting->default_long !!};

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
@endsection
