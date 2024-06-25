@extends('backend.layouts.app')
@section('title')
    {{ __('edit_candidate') }}
@endsection
@section('content')
    @if (userCan('candidate.create'))
        <div class="container-fluid">
            <form action="{{ route('candidate.update', $candidate->id) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title line-height-36">{{ __('edit_candidate') }}</h4>
                        <button type="submit"
                            class="btn bg-primary float-right d-flex align-items-center justify-content-center">
                            <i class="fas fa-sync"></i>&nbsp;
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
                            <div class="card-body row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-forms.label name="name" />
                                        <x-forms.input type="text" name="name" placeholder="name"
                                            value="{{ old('name', $user->name) }}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-forms.label name="email" />
                                        <x-forms.input type="email" value="{{ old('email', $user->email) }}"
                                            name="email" placeholder="email" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <x-forms.label name="password" :required="false" />
                                            <x-forms.input type="password" name="password" placeholder="password" />
                                        </div>
                                    </div>
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
                                        'selectedCountryId' => $candidate->country,
                                        'selectedStateId' => $candidate->region,
                                        'selectedCityId' => $candidate->district,
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
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                {{ __('image') }}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <x-forms.label name="image" />
                                        <input name="image" type="file" data-show-errors="true" data-width="100%"
                                            data-default-file="{{ asset($candidate->photo) }}" class="dropify">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                {{ __('files') }}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-forms.label name="cv" />
                                            <div class="custom-file">
                                                <input name="cv" type="file"
                                                    class="custom-file-input @error('cv') is-invalid @enderror">
                                                <label class="custom-file-label"
                                                    for="cvInputFile">{{ __('choose_cv') }}</label>
                                                @error('cv')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-forms.label name="profession" />
                                            <select name="profession_id" id="profession"
                                                class="select2bs4 form-control @error('profession_id') is-invalid @enderror">
                                                @foreach ($professions as $profession)
                                                    <option
                                                        {{ $profession->id == old('profession_id', $candidate->profession_id) ? 'selected' : '' }}
                                                        value="{{ $profession->id }}">
                                                        {{ $profession->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('profession_id')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-forms.label name="experience" />
                                            <select name="experience" id="experience"
                                                class="form-control select2bs4 @error('experience') is-invalid @enderror">
                                                @foreach ($experiences as $experience)
                                                    <option
                                                        {{ old('experience', $candidate->experience_id) == $experience->id ? 'selected' : '' }}
                                                        value="{{ $experience->id }}">{{ $experience->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('experience')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-forms.label name="job_role" />
                                            <select name="role_id"
                                                class="form-control select2bs4 @error('role_id') is-invalid @enderror"
                                                id="role_id">
                                                <option value=""> {{ __('select_one') }}</option>
                                                @foreach ($job_roles as $role)
                                                    <option
                                                        {{ old('role_id', $candidate->role_id) == $role->id ? 'selected' : '' }}
                                                        value="{{ $role->id }}"> {{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('role_id')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-forms.label name="education" />
                                            <select name="education" id="education"
                                                class="form-control select2bs4 @error('education') is-invalid @enderror">
                                                @foreach ($educations as $education)
                                                    <option
                                                        {{ $education->id == old('education_id', $candidate->education_id) ? 'selected' : '' }}
                                                        value="{{ $education->id }}"> {{ $education->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('education')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-forms.label name="gender" />
                                            <select name="gender" id="gender"
                                                class="form-control @error('gender') is-invalid @enderror">
                                                <option value="male"
                                                    {{ $candidate->gender == 'male' ? 'selected' : '' }}>
                                                    {{ __('male') }}</option>
                                                <option value="female"
                                                    {{ $candidate->gender == 'female' ? 'selected' : '' }}>
                                                    {{ __('female') }}</option>
                                                <option value="other"
                                                    {{ $candidate->gender == 'other' ? 'selected' : '' }}>
                                                    {{ __('other') }}</option>
                                            </select>
                                            @error('gender')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-forms.label name="website" />
                                            <input type="text" id="website" name="website"
                                                value="{{ old('website', $candidate->website) }}"
                                                class="form-control @error('website') is-invalid @enderror"
                                                placeholder="{{ __('website') }}">
                                            @error('website')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-forms.label name="birth_date" />
                                            <input type="text"
                                                value="{{ date('d-m-Y', strtotime($candidate->birth_date)) }}"
                                                class="form-control @error('birth_date') is-invalid @enderror"
                                                name="birth_date" id="birth_date" placeholder="{{ __('birth_date') }}">
                                            @error('birth_date')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-forms.label name="marital_status" />
                                            <select id="marital_status" name="marital_status"
                                                class="form-control @error('marital_status') is-invalid @enderror">
                                                <option>{{ __('marital_status') }}</option>
                                                <option value="married" @if ($candidate->marital_status == 'married') selected @endif>
                                                    {{ __('married') }}</option>
                                                <option value="single" @if ($candidate->marital_status == 'single') selected @endif>
                                                    {{ __('single') }}</option>
                                            </select>
                                            @error('marital_status')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <x-forms.label name="skills" :required="false" />
                                            <select id="skills" name="skills[]"
                                                class="select2-taggable form-control @error('skills') is-invalid @enderror"
                                                multiple>
                                                @foreach ($skills as $skill)
                                                    <option
                                                        {{ $candidate->skills ? (in_array($skill->id, $candidate->skills->pluck('id')->toArray()) ? 'selected' : '') : '' }}
                                                        value="{{ $skill->id }}">{{ $skill->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('skills')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <x-forms.label name="languages" :required="false" />
                                            <select id="languages" name="languages[]" multiple
                                                class="select2bs4 form-control @error('languages') is-invalid @enderror">
                                                @foreach ($candidate_languages as $language)
                                                    <option
                                                        {{ $candidate->languages ? (in_array($language->id, $candidate->languages->pluck('id')->toArray()) ? 'selected' : '') : '' }}
                                                        value="{{ $language->id }}">{{ $language->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('languages')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <x-forms.label name="bio" :required="false" />
                                            <textarea name="bio" id="image_ckeditor" placeholder="{{ __('bio') }}" value="{{ old('bio') }}"
                                                class="form-control @error('bio') is-invalid @enderror" id="bio" cols="1" rows="4">{!! $candidate->bio !!}</textarea>
                                            @error('bio')
                                                <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/bootstrap-datepicker.min.css">
    <style>
        .ck-editor__editable_inline {
            min-height: 300px;
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
    <script src="{{ asset('frontend/assets/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('frontend') }}/assets/js/axios.min.js"></script>
    <script src="{{ asset('backend') }}/plugins/dropify/js/dropify.min.js"></script>
    @if (app()->getLocale() == 'ar')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.ar.min.js
                                            "></script>
    @endif
    <script>
        $('#customFile').on('change', function(event) {
            $('#defaulthide').addClass('d-block')
            $('#defaulthide').removeClass('d-none')
        });
        // dropify image
        $('.dropify').dropify();
        //init datepicker
        $(document).ready(function() {
            $('#birth_date').datepicker({
                format: 'dd-mm-yyyy',
                isRTL: "{{ app()->getLocale() == 'ar' ? true : false }}",
                language: "{{ app()->getLocale() }}",
            });
        });
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
        $('.select2-taggable').select2({
            theme: 'bootstrap4',
            tags: true
        })
    </script>
    {{-- Leaflet  --}}
    @include('map::set-edit-leafletmap', ['lat' => $lat, 'long' => $long])

    <!-- ============== google map ========= -->
    <x-website.map.google-map-check />
    <script>
        function initMap() {
            var token = "{{ $setting->google_map_key }}";
            var oldlat = {!! $lat !!};
            var oldlng = {!! $long !!};

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
