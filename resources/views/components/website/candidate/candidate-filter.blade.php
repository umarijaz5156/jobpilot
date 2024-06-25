@props(['professions', 'experiences', 'educations', 'skills'])

<form id="candidate_search_form" action="{{ route('website.candidate') }}" method="GET">
    <div class="breadcrumbs style-two">
        <div class="container">
            <div class="row align-items-center ">
                <div class="col-12 position-relative">
                    <div class="breadcrumb-menu">
                        <h6 class="f-size-18 m-0">{{ __('find_candidates') }}</h6>
                        <ul>
                            <li><a href="{{ route('website.home') }}">{{ __('home') }}</a></li>
                            <li>/</li>
                            <li>{{ __('candidates') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="container">
            <div class="jobsearchBox tw-my-6 bg-gray-10 input-transparent height-auto-lg">
                <div class="top-content d-flex flex-column flex-lg-row align-items-center leaflet-map-results">
                    <div class="flex-grow-1 flex-grow-1 fromGroup has-icon banner-select">
                        <select class="rt-selectactive candidate-profession" name="profession">
                            <option value="" class="d-none">{{ __('select_profession') }}</option>
                            @foreach ($professions as $profession)
                                <option {{ $profession->id == request('profession') ? 'selected' : '' }}
                                    value="{{ $profession->id }}">
                                    {{ $profession->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="icon-badge category-icon">
                            <x-svg.layer-icon stroke="var(--primary-500)" width="24" height="24" />
                        </div>
                    </div>

                    <input type="hidden" name="lat" id="lat" value="">
                    <input type="hidden" name="long" id="long" value="">
                    @php
                        $oldLocation = request('location');
                        $map = $setting->default_map;
                    @endphp

                    @if ($map == 'google-map')
                        <div class="inputbox_2 flex-grow-1 fromGroup has-icon">
                            <input type="text" id="searchInput" placeholder="Enter a location..." name="location"
                                value="{{ $oldLocation }}" />
                            <div id="google-map" class="d-none"></div>
                            <div class="icon-badge">
                                <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}" width="24"
                                    height="24" />
                            </div>
                        </div>
                    @else
                        <div class="inputbox_2 flex-grow-1 fromGroup has-icon">
                            <input name="long" class="leaf_lon" type="hidden">
                            <input name="lat" class="leaf_lat" type="hidden">
                            <input type="text" id="leaflet_search" placeholder="{{ __('enter_location') }}"
                                name="location" value="{{ request('location') }}" class="tw-border-0"
                                autocomplete="off" />

                            <div class="icon-badge">
                                <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}" width="24"
                                    height="24" />
                            </div>
                        </div>
                    @endif
                    <div class="tw-flex tw-flex-wrap tw-gap-3 tw-items-center">
                        <div>
                            <button type="button"
                                class="btn tw-inline-flex gap-3 tw-items-center hover:tw-bg-[#F1F2F4] tw-bg-[#F1F2F4] hover:tw-text-[#18191C] tw-text-[#18191C] tw-border-0"
                                data-bs-toggle="modal" data-bs-target="#candidateFiltersModal">
                                <span class="">
                                    <x-svg.filters-icon />
                                </span>
                                <span>{{ __('filter') }}</span>
                            </button>
                        </div>
                        <div class="flex-grow-0">
                            <button
                                class="btn btn-primary d-block d-md-inline-block ">{{ __('search_candidates') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2 tw-mb-6 tw-mx-1.5 sm:tw-mx-0">
                        <p class="tw-text-[#767F8C] tw-text-sm tw-mb-0">{{ __('Popular Profession') }}:</p>
                        <ul class="tw-popular-search tw-flex-wrap">
                            @if (is_string(request('profession')))
                                <input type="hidden" value="{{ request('profession') }}" name="profession">
                            @endif
                            @foreach ($professions->take(10) as $profession)
                                <li onclick="professionFilter('{{ $profession->name }}')" class="tw-text-bold">
                                    <a href="#">{{ $profession->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-12">

                    <div class="tw-flex tw-justify-between tw-items-center tw-py-3 tw-mb-6"
                        style="border-top: 1px solid #E4E5E8; border-bottom: 1px solid #E4E5E8;">
                        <div class="tw-flex tw-gap-2 tw-items-center">
                            @if (request('keyword') ||
                                    request('country') ||
                                    request('sortby') ||
                                    request('profession') ||
                                    request('experience') ||
                                    request('skills.0') ||
                                    request('education') | request('gender'))
                                <h2 class="tw-text-sm tw-text-[#767F8C] tw-whitespace-nowrap tw-mb-0">
                                    {{ __('active_filter') }}:</h2>
                                <div class="d-flex w-100-p">
                                    @if (Request::get('keyword'))
                                        <div class="rt-mr-2 icon-badge">
                                            <x-website.candidate.filter-data-component title="{{ __('keyword') }}"
                                                filter="{{ request('keyword') }}" />
                                        </div>
                                    @endif
                                    @if (Request::get('country'))
                                        <div class="rt-mr-2 icon-badge">
                                            <x-website.candidate.filter-data-component title="{{ __('country') }}"
                                                filter="{{ request('country') }}" />
                                        </div>
                                    @endif
                                    @if (Request::get('sortby') && Request::get('sortby') != 'latest')
                                        <div class="rt-mr-2 icon-badge">
                                            <x-website.candidate.filter-data-component title="{{ __('sortby') }}"
                                                filter="{{ request('sortby') }}" />
                                        </div>
                                    @endif
                                    @if (Request::get('profession') && Request::get('profession') != null)
                                        <div class="rt-mr-2 icon-badge">
                                            <x-website.candidate.filter-data-component title="{{ __('profession') }}"
                                                filter="{{ $professions->where('id', request('profession'))->first()->name ?? '-' }}" />
                                        </div>
                                    @endif
                                    @if (Request::get('experience') && Request::get('experience') != 'all')
                                        <div class="rt-mr-2 icon-badge">
                                            <x-website.candidate.filter-data-component title="{{ __('experience') }}"
                                                filter="{{ request('experience') }}" />
                                        </div>
                                    @endif
                                    @if (Request::has('skills') && Request::input('skills') != 'all')
                                        <div class="rt-mr-2 icon-badge">
                                            <x-website.candidate.filter-data-component title="{{ __('skills') }}"
                                                filter="{{ implode(', ', Request::input('skills')) }}" />
                                        </div>
                                        aaa
                                    @endif
                                    @if (Request::get('gender') && Request::get('gender') != 'all')
                                        <div class="rt-mr-2 icon-badge">
                                            <x-website.candidate.filter-data-component title="{{ __('gender') }}"
                                                filter="{{ request('gender') }}" />
                                        </div>
                                    @endif
                                    @if (Request::get('education') && Request::get('education') != 'all')
                                        <div class="rt-mr-2 icon-badge">
                                            <x-website.candidate.filter-data-component title="{{ __('education') }}"
                                                filter="{{ request('education') }}" />
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="tw-flex tw-justify-end tw-items-center">
                            <div class="joblist-fliter-gorup !tw-min-w-max">
                                <div class="right-content !tw-mt-0">
                                    <nav>
                                        <div class="nav" id="nav-tab" role="tablist">
                                            <button onclick="styleSwitch('box')" class="nav-link active "
                                                id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                                                type="button" role="tab" aria-controls="nav-home"
                                                aria-selected="true">
                                                <x-svg.box-icon />
                                            </button>
                                            <button onclick="styleSwitch('list')" class="nav-link"
                                                id="nav-profile-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-profile" type="button" role="tab"
                                                aria-controls="nav-profile" aria-selected="false">
                                                <x-svg.list-icon />
                                            </button>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="candidate-content">
        <div class="container">
            <!-- ============ Filter Old Data ==========  -->
            {{-- <div class="d-flex w-100-p">
                @if (Request::get('keyword'))
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('keyword') }}"
                            filter="{{ request('keyword') }}" />
                    </div>
                @endif
                @if (Request::get('country'))
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('country') }}"
                            filter="{{ request('country') }}" />
                    </div>
                @endif
                @if (Request::get('sortby') && Request::get('sortby') != 'latest')
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('sortby') }}"
                            filter="{{ request('sortby') }}" />
                    </div>
                @endif
                @if (Request::get('profession') && Request::get('profession') != null)
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('profession') }}"
                            filter="{{ $professions->where('id', request('profession'))->first()->name ?? '-' }}" />
                    </div>
                @endif
                @if (Request::get('experience') && Request::get('experience') != 'all')
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('experience') }}"
                            filter="{{ request('experience') }}" />
                    </div>
                @endif
                @if (Request::get('gender') && Request::get('gender') != 'all')
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('gender') }}"
                            filter="{{ request('gender') }}" />
                    </div>
                @endif
                @if (Request::get('education') && Request::get('education') != 'all')
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('education') }}"
                            filter="{{ request('education') }}" />
                    </div>
                @endif
            </div> --}}
            <!-- ============ Filter Old Data End ==========  -->
            {{-- <div class="row">
                <div class="col-lg-12 rt-mb-24">
                    <div class="joblist-left-content2">
                        <div class="tw-flex tw-justify-end tw-items-center">
                            <div class="joblist-fliter-gorup !tw-min-w-max">
                                <div class="right-content !tw-mt-0">
                                    <nav>
                                        <div class="nav" id="nav-tab" role="tablist">
                                            <button onclick="styleSwitch('box')" class="nav-link active "
                                                id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                                                type="button" role="tab" aria-controls="nav-home"
                                                aria-selected="true">
                                                <x-svg.box-icon />
                                            </button>
                                            <button onclick="styleSwitch('list')" class="nav-link"
                                                id="nav-profile-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-profile" type="button" role="tab"
                                                aria-controls="nav-profile" aria-selected="false">
                                                <x-svg.list-icon />
                                            </button>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="rt-spacer-10"></div>
                    </div>
                </div>
            </div> --}}

            <div class="row">
                {{-- <div class="col-lg-4  @if (request('education') || request('gender') || request('experience') || request('skills')) @else d-none @endif rt-mb-lg-30"
                    id="toggoleSidebar">
                    <div class="togglesidebr_widget">
                        <div class="sidetbar-widget !tw-overflow-x-auto">
                            <ul>
                                <li class="d-block has-children open">
                                    <div class="jobwidget_tiitle">{{ __('skills') }}</div>
                                    <ul class="sub-catagory">
                                        <li class="d-block">
                                            <div class="benefits-tags">
                                                @foreach ($skills as $skill)
                                                    <label for="{{ $skill->name }}" class="py-1">
                                                        <input onclick="Filter()"
                                                            {{ request('skills') ? (in_array($skill->id, request('skills')) ? 'checked' : '') : '' }}
                                                            type="checkbox" id="{{ $skill->name }}"
                                                            value="{{ $skill->id }}" name="skills[]">
                                                        <span>{{ $skill->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li class="d-block has-children open">
                                    <div class="jobwidget_tiitle">{{ __('experience') }}</div>
                                    <ul class="sub-catagory">
                                        <li class="d-block tw-py-1">
                                            <div class="form-check from-radio-custom tw-flex tw-items-center">
                                                <input id="experienceall" class="form-check-input"
                                                    {{ request('experience') == 'all' ? 'checked' : '' }}
                                                    type="radio" name="experience" value="all">
                                                <label class="form-check-label pointer text-gray-700 f-size-14 tw-mt-1"
                                                    for="experienceall">
                                                    {{ __('all') }}
                                                </label>
                                            </div>
                                        </li>
                                        @foreach ($experiences as $experience)
                                            <li class="d-block tw-py-1">
                                                <div class="form-check from-radio-custom tw-flex tw-items-center">
                                                    <input class="form-check-input"
                                                        {{ request('experience') == $experience->name ? 'checked' : '' }}
                                                        type="radio" name="experience"
                                                        value="{{ $experience->name }}"
                                                        id="{{ $experience->slug }}">
                                                    <label
                                                        class="form-check-label pointer text-gray-700 f-size-14 tw-mt-1"
                                                        for="{{ $experience->slug }}">
                                                        {{ $experience->name }}
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                                <li class="d-block has-children open">
                                    <div class="jobwidget_tiitle">{{ __('education') }}</div>
                                    <ul class="sub-catagory">
                                        <li class="d-block">
                                            <div class="form-check from-radio-custom tw-flex tw-items-center">
                                                <input {{ request('education') == 'all' ? 'checked' : '' }}
                                                    name="education" class="form-check-input" type="radio"
                                                    value="all" id="educationall">
                                                <label class="form-check-label pointer text-gray-700 f-size-14 tw-mt-1"
                                                    for="educationall">
                                                    {{ __('all') }}
                                                </label>
                                            </div>
                                        </li>
                                        @foreach ($educations as $education)
                                            <li class="d-block tw-py-1">
                                                <div class="form-check from-radio-custom tw-flex tw-items-center">
                                                    <input
                                                        {{ request('education') == $education->name ? 'checked' : '' }}
                                                        name="education" class="form-check-input" type="radio"
                                                        value="{{ $education->name }}" id="{{ $education->slug }}">
                                                    <label
                                                        class="form-check-label pointer text-gray-700 f-size-14 tw-mt-1"
                                                        for="{{ $education->slug }}">
                                                        {{ $education->name }}
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> --}}
</form>

@section('frontend_links')
    @include('map::links')
    <x-map.leaflet.autocomplete_links />
    <style>
        .candidate-profession+.select2-container--default .select2-selection--single {
            border: none !important;
        }
    </style>
@endsection

@section('frontend_scripts')
    <x-map.leaflet.autocomplete_scripts />

    <script>
        function professionFilter(profession) {
            console.log(profession);
            $('input[name=profession]').val(profession)
            $('#candidate_search_form').submit();
        }
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // $('input[type=radio]').on('change', function() {
        //     $('#form').submit();
        // });
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
                    const text = region + ',' + country;
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
@endsection
