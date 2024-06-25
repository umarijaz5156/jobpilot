@extends('backend.layouts.app')
@section('title')
    {{ $user->name }} {{ __('details') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ll-card">
                <div class="ll-card-header d-flex justify-content-between align-items-center">
                    <h3 class="ll-card-title line-height-36">{{ $candidate->user->name }}'s
                        {{ __('details') }}</h3>
                    <div>
                        <a href="{{ route('candidate.edit', $candidate->id) }}">
                            <x-svg.table-edit />
                        </a>
                        <form action="{{ route('candidate.destroy', $candidate->id) }}" method="POST" class="d-inline">
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
                                    <img src="{{ $candidate->photo }}" alt="candidate_profile">
                                </div>
                                <div>
                                    <h3>{{ $candidate->user->name }}</h3>
                                    <p>{{ $candidate->user->email }}</p>
                                    @if ($user->socialInfo && $candidate->user->socialInfo->count() > 0)
                                        <div class="d-flex">
                                            @foreach ($user->socialInfo as $contact)
                                                <a class="social-media" target="__blank" href="{{ $contact->url }}">
                                                    @switch($contact)
                                                        @case($contact->social_media === 'facebook')
                                                            <x-svg.facebook-icon />
                                                        @break

                                                        @case($contact->social_media === 'twitter')
                                                            <x-svg.twitter-icon />
                                                        @break

                                                        @case($contact->social_media === 'instagram')
                                                            <x-svg.instagram-icon />
                                                        @break

                                                        @case($contact->social_media === 'youtube')
                                                            <x-svg.youtube-icon />
                                                        @break

                                                        @case($contact->social_media === 'linkedin')
                                                            <x-svg.linkedin-icon />
                                                        @break

                                                        @case($contact->social_media === 'pinterest')
                                                            <x-svg.pinterest-icon />
                                                        @break

                                                        @case($contact->social_media === 'reddit')
                                                            <x-svg.reddit-icon />
                                                        @break

                                                        @case($contact->social_media === 'github')
                                                            <x-svg.github-icon />
                                                        @break

                                                        @case($contact->social_media === 'other')
                                                            <x-svg.link-icon />
                                                        @break

                                                        @default
                                                    @endswitch
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center" style="gap: 16px;">
                                        <div>
                                            <a href="javascript:void(0)" class="active-status">
                                                <label class="switch ">
                                                    <input data-id="{{ $user->id }}" type="checkbox"
                                                        class="success status-switch"
                                                        {{ $user->status == 1 ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                                <p class="{{ $user->status == 1 ? 'active' : '' }}"
                                                    id="status_{{ $candidate->user_id }}">
                                                    {{ $user->status == 1 ? __('activated') : __('deactivated') }}</p>
                                            </a>
                                        </div>
                                        <div>
                                            <a href="javascript:void(0)" class="active-status">
                                                <label class="switch ">
                                                    <input data-userid="{{ $user->id }}" type="checkbox"
                                                        class="success email-verification-switch"
                                                        {{ $user->email_verified_at ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                                <p class="{{ $user->email_verified_at ? 'active' : '' }}"
                                                    id="verification_status_{{ $candidate->user_id }}">
                                                    {{ $user->email_verified_at ? __('verified') : __('unverified') }}
                                                </p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ll-flex-item">
                            <div class="candidate-details__right">
                                <div class="one">
                                    <div class="mb-4 d-flex">
                                        <x-svg.details-profession />
                                        <div>
                                            <p>{{ __('profession') }}</p>
                                            <h4>{{ $candidate->profession ? $candidate->profession->name : '-' }}</h4>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <x-svg.details-experience />
                                        <div>
                                            <p>{{ __('experience') }}</p>
                                            <h4>{{ $candidate->experience ? $candidate->experience->name : '' }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="two">
                                    <div class="mb-4 d-flex">
                                        <x-svg.details-package />
                                        <div>
                                            <p>{{ __('marital_status') }}</p>
                                            <h4>{{ __($candidate->marital_status) }}</h4>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <x-svg.details-education />
                                        <div>
                                            <p>{{ __('education') }}</p>
                                            <h4>{{ $candidate->education ? $candidate->education->name : '' }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="three">
                                    <div class="mb-4 d-flex">
                                        <x-svg.details-leyers />
                                        <div>
                                            <p>{{ __('gender') }}</p>
                                            <h4>{{ ucfirst($candidate->gender) }}</h4>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <x-svg.details-calendar-blank />
                                        <div>
                                            <p>{{ __('birth_date') }}</p>
                                            <h4>{{ date('D, d M Y', strtotime($candidate->birth_date)) }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="four">
                                    <div class="mb-4 d-flex">
                                        <x-svg.details-globe-simple />
                                        <div>
                                            <p>{{ __('website') }}</p>
                                            <a href="{{ $candidate->website }}">{{ $candidate->website }}</a>
                                        </div>
                                    </div>
                                    @if ($user->contactInfo && $user->contactInfo->phone)
                                        <div class="mb-4 d-flex">
                                            <x-svg.details-phone-call />
                                            <div>
                                                <p>{{ __('phone') }}</p>
                                                <a
                                                    href="tel: {{ $user->contactInfo->phone }}">{{ $user->contactInfo->phone }}</a>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($user->contactInfo && $user->contactInfo->email)
                                        <div class="d-flex">
                                            <x-svg.details-envelop/>
                                            <div>
                                                <p>{{ __('contact_email') }}</p>
                                                <a
                                                    href="mailto: {{ $user->contactInfo->email }}">{{ $user->contactInfo->email }}</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
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
                            {!! nl2br($candidate->bio) !!}
                        </p>
                    </div>
                    <div class="skills-wrap">
                        <div class="ll-mb-6">
                            <h4 class="text-bold">
                                {{ __('skills') }}
                            </h4>
                            <p>
                                @foreach ($candidate->skills as $skill)
                                    <span class="skill-badge">{{ $skill->name }}</span>
                                @endforeach
                            </p>
                        </div>
                        <div class="ll-mb-6">
                            <h4 class="text-bold">
                                {{ __('languages') }}
                            </h4>
                            <p>
                                @foreach ($candidate->languages as $language)
                                    <span class="language-badge">{{ $language->name }}</span>
                                @endforeach
                            </p>
                        </div>
                        <div>
                            <h4 class="text-bold"> {{ __('location') }} </h4>
                            <p>{{ $candidate->exact_location ? $candidate->exact_location: $candidate->full_address }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <x-admin.candidate.card-component title="{{ __('applied_jobs') }}" :jobs="$appliedJobs"
                link="website.job.apply" />
            <x-admin.candidate.card-component title="{{ __('bookmark_jobs') }}" :jobs="$bookmarkJobs"
                link="website.job.bookmark" />
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
    <!-- >=>Leaflet Map<=< -->
    <x-map.leaflet.map_links />

    @include('map::links')
@endsection
@section('script')
    {{-- Leaflet  --}}
    <script src="{{ asset('frontend') }}/assets/js/axios.min.js"></script>
    <x-map.leaflet.map_scripts />
    <script>
        var oldlat = {!! $candidate->lat ? $candidate->lat : $setting->default_lat !!};
        var oldlng = {!! $candidate->long ? $candidate->long : $setting->default_long !!};

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

            var oldlat = {!! $candidate->lat ? $candidate->lat : $setting->default_lat !!};
            var oldlng = {!! $candidate->long ? $candidate->long : $setting->default_long !!};

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
@endsection
