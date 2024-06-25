@extends('backend.layouts.app')
@section('title')
    {{ __('job_list') }}
@endsection
@section('content')
    @php
        $userr = auth()->user();
    @endphp
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="ll-card">
                    <div class="ll-card-header d-flex justify-content-between align-items-center">
                        <h3 class="ll-card-title line-height-36">{{ $job->title }}</h3>
                        <div>
                            <a href="{{ route('job.edit', $job->id) }}">
                                <x-svg.table-edit />
                            </a>
                            <form action="{{ route('job.destroy', $job->id) }}" method="POST" class="d-inline">
                                @method('DELETE')
                                @csrf
                                <button onclick="return confirm('{{ __('are_you_sure_you_want_to_delete_this_item') }}');"
                                    class="border-0 bg-transparent">
                                    <x-svg.table-delete />
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="ll-card-body table-responsive">
                        <div class="ll-flex my-2">
                            <div class="ll-flex-item">
                                <div class="candidate-details__left">
                                    <div class="candidate-logo">
                                        @if ($job->company)
                                            <img src="{{ asset($job->company->logo_url) }}" alt="company logo">
                                        @else
                                            <p>Admin Posted Job</p>
                                        @endif
                                    </div>
                                    <div>
                                        <h3>{{ $job->title }}</h3>
                                        <p>
                                            <span>{{ $job->company && $job->company->user ? $job->company->user->name : '-' }}</span>
                                            <span>·</span>
                                            <span>{{ $job->job_type->name ?? '' }}</span>
                                            @if ($job->is_remote)
                                                <span>·</span>
                                                <span>{{ __('remote') }}</span>
                                            @endif
                                        </p>
                                        <div class="category">
                                            <x-svg.table-money />
                                            <div>
                                                @if ($job->salary_mode == 'range')
                                                    <h3>{{ getFormattedNumber($job->min_salary) }} -
                                                        {{ getFormattedNumber($job->max_salary) }} {{ currentCurrencyCode() }}
                                                    </h3>
                                                @else
                                                    <h3>{{ $job->custom_salary }}</h3>
                                                @endif
                                                <p>{{ $job->salary_type->name }} </p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center" style="gap: 16px;">
                                            @if ($job->status == 'pending')
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <form action="{{ route('admin.job.status.change', $job->id) }}"
                                                        method="POST" id="job_status_pending_form_{{ $job->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input
                                                            onclick="$('#job_status_pending_form_{{ $job->id }}').submit()"
                                                            type="radio" id="status_input_pending_{{ $job->id }}"
                                                            name="status" class="plan_type_selection custom-control-input"
                                                            value="pending" {{ $job->status == 'pending' ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="status_input_pending_{{ $job->id }}">{{ __('pending') }}</label>
                                                    </form>
                                                </div>
                                            @endif
                                            @if ($job->status == 'active' || $job->status == 'pending')
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <form action="{{ route('admin.job.status.change', $job->id) }}"
                                                        method="POST" id="job_status_publish_form_{{ $job->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input
                                                            onclick="$('#job_status_publish_form_{{ $job->id }}').submit()"
                                                            type="radio" id="status_input_publish_{{ $job->id }}"
                                                            name="status" class="plan_type_selection custom-control-input"
                                                            value="active" {{ $job->status == 'active' ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="status_input_publish_{{ $job->id }}">{{ __('publish') }}</label>
                                                    </form>
                                                </div>
                                            @endif
                                            @if ($job->status == 'active' || $job->status == 'expired')
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <form action="{{ route('admin.job.status.change', $job->id) }}"
                                                        method="POST" id="job_status_unpublish_form_{{ $job->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input disabled
                                                            onclick="$('#job_status_unpublish_form_{{ $job->id }}').submit()"
                                                            type="radio" id="status_input_unpublish_{{ $job->id }}"
                                                            name="status" class="plan_type_selection custom-control-input"
                                                            value="expired" {{ $job->status == 'expired' ? 'checked' : '' }}>
                                                        <label
                                                            class="custom-control-label {{ $job->status == 'expired' ? 'expired_radio' : '' }}"
                                                            data-toggle="tooltip"
                                                            title="{{ __('expired_status_depend_on_deadline') }}"
                                                            for="status_input_unpublish_{{ $job->id }}">{{ __('expired') }}</label>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ll-flex-item">
                                <div class="candidate-details__right">
                                    <div class="one">
                                        <div class="mb-4">
                                            <x-svg.details-experience />
                                            <p>{{ __('category') }}</p>
                                            <h4>{{ $job->category ? $job->category->name : '' }}</h4>
                                        </div>
                                        <div>
                                            <x-svg.details-profession />
                                            <p>{{ __('job_role') }}</p>
                                            <h4>{{ $job->role ? $job->role->name : '' }}</h4>
                                        </div>
                                    </div>
                                    <div class="two">
                                        <div class="mb-4">
                                            <x-svg.details-package />
                                            <p>{{ __('experience') }}</p>
                                            <h4>{{ $job->experience ? $job->experience->name : '' }}</h4>
                                        </div>
                                        <div>
                                            <x-svg.details-education />
                                            <p>{{ __('education') }}</p>
                                            <h4>{{ $job->education ? $job->education->name : '' }}</h4>
                                        </div>
                                    </div>
                                    <div class="three">
                                        <div class="mb-4">
                                            <x-svg.details-leyers />
                                            <p>{{ __('gender') }}</p>
                                            <h4>{{ ucfirst($job->gender) }}</h4>
                                        </div>
                                        <div>
                                            <x-svg.details-calendar-blank />
                                            <p>{{ __('deadline') }}</p>
                                            <h4>{{ date('D, d M Y', strtotime($job->deadline)) }}</h4>
                                        </div>
                                    </div>
                                    @if ($job->company)
                                        <div class="four">
                                            <div class="mb-4 d-flex">
                                                <x-svg.details-globe-simple />

                                                <div>
                                                    <p>{{ __('website') }}</p>

                                                    <a href="{{ $job->company->website }}">{{ $job->company->website }}</a>

                                                </div>

                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="desc-skills-wrapper">
                        <div class="desc-wrap">
                            <div class="text-bold">
                                {{ __('description') }}
                            </div>
                            <p>
                                {!! $job->description !!}
                            </p>
                        </div>
                        <div class="skills-wrap">
                            <div class="ll-mb-4">
                                <h4 class="text-bold">
                                    {{ __('tags') }}
                                </h4>
                                <p>
                                    @foreach ($job->tags as $skill)
                                        <span class="skill-badge">{{ $skill->name }}</span>
                                    @endforeach
                                </p>
                            </div>
                            <div class="ll-mb-4">
                                <h4 class="text-bold">
                                    {{ __('benefits') }}
                                </h4>
                                <p>
                                    @foreach ($job->benefits as $language)
                                        <span class="language-badge">{{ $language->name }}</span>
                                    @endforeach
                                </p>
                            </div>
                            <div class="ll-mb-4 mt-2">
                                <h4 class="text-bold">
                                    {{ __('skills') }}
                                </h4>
                                <p>
                                    @foreach ($job->skills as $skill)
                                        <span class="skill-badge">{{ $skill->name }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 p-4">
                            <label for="description">{{ __('location') }}</label>
                            <div class="p-half rounded">
                                <x-website.map.map-warning />
                                @php
                                    $map = $setting->default_map;
                                @endphp

                                @if ($map == 'google-map')
                                    <div class="map mymap" id="google-map"></div>
                                @else
                                    <div id="leaflet-map"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 35px;
            height: 19px;
        }

        /* Hide default HTML checkbox */
        .switch input {
            display: none;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 15px;
            width: 15px;
            left: 3px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input.success:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(15px);
            -ms-transform: translateX(15px);
            transform: translateX(15px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
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

        /* Style  radio button */
        .expired_radio::after {
            content: "";
            display: inline-block;
            border-radius: 50%;
            margin-right: 8px;
            background-color: red;
        }
    </style>
    <!-- >=>Leaflet Map<=< -->
    <x-map.leaflet.map_links />
    @include('map::links')
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#tags').select2({
                theme: 'bootstrap4',
                tags: true,
                tokenSeparators: [',', ' ']
            });
        });

        ClassicEditor.create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    </script>
    {{-- Leaflet  --}}
    <x-map.leaflet.map_scripts />
    <script>
        var oldlat = {!! $job->lat ? $job->lat : $setting->default_lat !!};
        var oldlng = {!! $job->long ? $job->long : $setting->default_long !!};

        // Map preview
        var element = document.getElementById('leaflet-map');

        // Height has to be set. You can do this in CSS too.
        element.style = 'height:300px;';

        // Create Leaflet map on map element.
        var leaflet_map = L.map(element);

        // Add OSM tile layer to the Leaflet map.
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(leaflet_map);

        // Target's GPS coordinates.
        var target = L.latLng(oldlat, oldlng);

        // Set map's center to target with zoom 14.
        const zoom = 14;
        leaflet_map.setView(target, zoom);

        // Place a marker on the same location.
        L.marker(target).addTo(leaflet_map);
    </script>

    <!-- ================ google map ============== -->
    <x-website.map.google-map-check />
    <script>
        function initMap() {
            var token = "{{ $setting->google_map_key }}";

            var oldlat = {!! $job->lat ? $job->lat : $setting->default_lat !!};
            var oldlng = {!! $job->long ? $job->long : $setting->default_long !!};

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

                draggable: false,
                position: {
                    lat: oldlat,
                    lng: oldlng
                },
                map,
                // icon: image
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
    <!-- ================ google map ============== -->
    <script>
        $('.status-switch').on('change', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('candidate.status.change') }}',
                data: {
                    'status': status,
                    'id': id
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });
        });

        $('.email-verification-switch').on('change', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('userid');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('company.verify.change') }}',
                data: {
                    'status': status,
                    'id': id
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });
        });
    </script>
@endsection
