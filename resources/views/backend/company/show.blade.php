@extends('backend.layouts.app')
@section('title')
    {{ __($company->user->name) }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="ll-card">
                    <div class="ll-card-header d-flex flex-wrap justify-content-between align-items-center">
                        <h3 class="card-title line-height-36">{{ $company->user->name }}'s
                            {{ __('details') }}</h3>
                        <div>
                            <a href="{{ route('company.edit', $company->id) }}">
                                <x-svg.table-edit />
                            </a>
                            <form action="{{ route('company.destroy', $company->id) }}"
                                method="POST" class="d-inline">
                                @method('DELETE')
                                @csrf
                                <button onclick="return confirm('{{ __('are_you_sure_you_want_to_delete_this_item') }}');" class="border-0 bg-transparent">
                                    <x-svg.table-delete/>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="ll-card-body table-responsive">
                        <div class="ll-flex">
                            <div class="ll-flex-item">
                                <div class="company-details__left">
                                    <div class="company-logo">
                                        <img src="{{ asset($company->logo_url) }}" alt="Company Logo">
                                    </div>
                                    <div>
                                        <h3>{{ $company->user->name }}
                                            @if($company->is_profile_verified)
                                                <svg
                                                    style="width: 24px ; height: 24px ; color: green"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @else
                                                <svg
                                                    style="width: 24px ; height: 24px ; color: red"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>

                                            @endif
                                        </h3>
                                        <p>{{ $company->user->email }}</p>
                                        @if ($company->user->socialInfo && $company->user->socialInfo->count() > 0)
                                            <div class="d-flex flex-wrap">
                                                @foreach ($company->user->socialInfo as $contact)
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
                                                    <input data-id="{{ $company->user_id }}" type="checkbox"
                                                        class="success status-switch"
                                                        {{ $company->user->status == 1 ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                                <p class="{{ $company->user->status == 1 ? 'active' : '' }}" id="status_{{ $company->user_id }}">
                                                    {{ $company->user->status == 1 ? __('activated') : __('deactivated') }}</p>
                                            </a>
                                        </div>
                                        <div>
                                            <a href="javascript:void(0)" class="active-status">
                                                <label class="switch ">
                                                    <input data-userid="{{ $company->user_id }}" type="checkbox"
                                                        class="success email-verification-switch"
                                                        {{ $company->user->email_verified_at ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                                <p class="{{ $company->user->email_verified_at ? 'active' : '' }}" id="verification_status_{{ $company->user_id }}">
                                                    {{ $company->user->email_verified_at ? __('verified') : __('unverified') }}
                                                </p>
                                            </a>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ll-flex-item">
                                <div class="company-details__right">
                                    <div class="one">
                                        <div class="d-flex">
                                            <x-svg.details-calendar-blank />
                                            <div>
                                                <p>{{ __('establishment_date') }}</p>
                                                <h4>{{ $company->establishment_date ? date('j F, Y', strtotime($company->establishment_date)) : '' }}</h4>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <x-svg.details-users />
                                            <div>
                                                <p>{{ __('team_size') }}</p>
                                                <h4>{{ $company->team_size ? $company->team_size->name : '' }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="two">
                                        <div class="d-flex">
                                            <x-svg.details-leyers />
                                            <div>
                                                <p>{{ __('industry_type') }}</p>
                                                <h4>{{ $company->industry ? $company->industry->name : '' }}</h4>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <x-svg.details-globe-simple />
                                            <div>
                                                <p>{{ __('country') }}</p>
                                                <h4>{{ $company->country ? $company->country : '' }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="three">
                                        @if ($company->website)
                                        <div class="d-flex">
                                            <x-svg.details-globe-simple/>
                                            <div>
                                                <p>{{ __('website') }}</p>
                                            <a href="{{ $company->website }}" target="_blank">{{ $company->website ? $company->website : '' }}</a>
                                            </div>
                                        </div>
                                        @endif
                                        @if ($company->user->contactInfo->phone)
                                        <div class="d-flex">
                                            <x-svg.details-phone-call />
                                            <div>
                                                <p>{{ __('phone') }}</p>
                                                <a href="tel: {{ $company->user->contactInfo->phone }}">{{ $company->user->contactInfo->phone }}</a>
                                            </div>
                                        </div>
                                        @endif
                                        @if ($company->user->contactInfo->email )
                                        <div class="d-flex">
                                            <x-svg.details-envelop stroke="#0A65CC" fill="#0A65CC"/>
                                            <div>
                                                <p>{{ __('contact_email') }}</p>
                                                <a href="mailto: {{ $company->user->contactInfo->email }}">{{ $company->user->contactInfo->email }}</a>
                                            </div>
                                        </div>
                                        @endif
                                            <div class="d-flex">
                                                <svg
                                                    width="24" height="25"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#0A65CC" fill="#0A65CC" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                </svg>

                                                <div>
                                                    <p>{{ __('documents') }}</p>
                                                    <a href="{{ route('admin.company.documents',$company)  }}">{{ __('verification_documents') }}</a>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="desc-bio-wrapper">
                        <div class="desc-wrap">
                            <div class="text-bold">
                                {{ __('description') }}
                            </div>
                            <p>
                                {!! nl2br($company->bio) !!}
                            </p>
                        </div>
                        <div class="bio-wrap">
                            <div class="text-bold">
                                {{ __('vision') }}
                            </div>
                            <p>
                                {!! nl2br($company->vision) !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="ll-card">
                    <div class="ll-card-header">
                        <h3 class="ll-card-title">{{ __('location') }}</h3>
                    </div>
                    <div class="ll-card-body">
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
    @if ($company->jobs->count() > 0)
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ __('company_joblist') }}</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="ll-table table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('job') }}</th>
                                            <th>{{ __('category') }}/{{ __('role') }}</th>
                                            <th>{{ __('salary') }}</th>
                                            <th>{{ __('deadline') }}</th>
                                            <th>{{ __('status') }}</th>
                                            @if (userCan('job.update') || userCan('job.delete'))
                                                <th>{{ __('action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($company->jobs as $job)
                                            <tr>
                                                <td tabindex="0">
                                                    <a href="{{ route('job.show', $job->id) }}"  class="company">
                                                        <img src="{{ asset($job->company->logo_url) }}" alt="image">
                                                        <div>
                                                            <h2>{{ $job->title }}</h2>
                                                            <p>
                                                                <span>{{ $job->title }}</span>
                                                                @if ($job->is_remote)
                                                                <span>·</span>
                                                                <span>{{ __('remote') }}</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </a>
                                                </td>
                                                <td tabindex="0">
                                                    <div class="category">
                                                        <x-svg.table-layer />
                                                        <div>
                                                            <h3>{{ $job->category->name }}</h3>
                                                            <p>{{ $job->role->name }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td tabindex="0">
                                                    <div class="category">
                                                        <x-svg.table-money />
                                                        <div>
                                                            @if ($job->salary_mode == 'range')
                                                            <h3>{{ getFormattedNumber($job->min_salary) }} - {{ getFormattedNumber($job->max_salary) }} {{ currentCurrencyCode() }}</h3>
                                                            @else
                                                            <h3>{{ $job->custom_salary }}</h3>
                                                            @endif
                                                            <p>{{ $job->salary_type->name }} </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td tabindex="0">
                                                    {{ date('j F, Y', strtotime($job->deadline)) }}
                                                </td>
                                                @if (userCan('job.update'))
                                                    <td tabindex="0">
                                                        <div class="d-flex">
                                                            @if ($job->status == 'pending')
                                                            <form action="{{ route('admin.job.status.change', $job->id) }}" method="POST" id="job_status_pending_form_{{ $job->id }}">
                                                                <div class="custom-control custom-radio custom-control-inline">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input onclick="$('#job_status_pending_form_{{ $job->id }}').submit()" type="radio" id="status_input_pending_{{ $job->id }}" name="status"
                                                                            class="plan_type_selection custom-control-input" value="pending"
                                                                            {{ $job->status == 'pending' ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="status_input_pending_{{ $job->id }}">{{__('pending')}}</label>
                                                                    </div>
                                                                </form>
                                                            @endif
                                                            <form action="{{ route('admin.job.status.change', $job->id) }}" method="POST" id="job_status_publish_form_{{ $job->id }}">
                                                            <div class="custom-control custom-radio custom-control-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input onclick="$('#job_status_publish_form_{{ $job->id }}').submit()" type="radio" id="status_input_publish_{{ $job->id }}" name="status"
                                                                        class="plan_type_selection custom-control-input" value="active"
                                                                        {{ $job->status == 'active' ? 'checked' : '' }}>
                                                                    <label class="custom-control-label" for="status_input_publish_{{ $job->id }}">{{__('publish')}}</label>
                                                                </div>
                                                            </form>
                                                            @if ($job->status == 'active' || $job->status == 'expired')
                                                            <form action="{{ route('admin.job.status.change', $job->id) }}" method="POST" id="job_status_unpublish_form_{{ $job->id }}">
                                                                <div class="custom-control custom-radio custom-control-inline">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input onclick="$('#job_status_unpublish_form_{{ $job->id }}').submit()" type="radio" id="status_input_unpublish_{{ $job->id }}" name="status"
                                                                            class="plan_type_selection custom-control-input" value="expired"
                                                                            {{ $job->status == 'expired' ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="status_input_unpublish_{{ $job->id }}">{{__('unpublish')}}</label>
                                                                    </div>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                @endif
                                                <td>
                                                    <a data-toggle="tooltip" data-placement="top"
                                                        title="{{ __('details') }}"
                                                        href="{{ route('job.show', $job->id) }}"
                                                        class="btn ll-p-0">{{ __('view_details') }} <x-svg.table-btn-arrow />
                                                    </a>
                                                    <a data-toggle="tooltip" data-placement="top"
                                                        title="{{ __('clone') }}"
                                                        href="{{ route('admin.job.clone', $job->slug) }}"
                                                        class="btn ll-p-0"><x-svg.table-clone /> {{ __('clone') }}
                                                    </a>

                                                    <a data-toggle="tooltip" data-placement="top"
                                                        title="{{ __('view_frontend') }}"
                                                        href="{{ route('website.job.details', $job->slug) }}"
                                                        class="btn ll-p-0"><x-svg.table-link />
                                                    </a>
                                                    @if (userCan('job.update'))
                                                        <a data-toggle="tooltip" data-placement="top"
                                                            title="{{ __('edit') }}"
                                                            href="{{ route('job.edit', $job->id) }}"
                                                            class="btn ll-p-0"><x-svg.table-edit />
                                                        </a>
                                                    @endif
                                                    @if (userCan('job.delete'))
                                                        <form action="{{ route('job.destroy', $job->id) }}"
                                                            method="POST" class="d-inline">
                                                            @method('DELETE')
                                                            @csrf
                                                            <button data-toggle="tooltip" data-placement="top"
                                                                title="{{ __('delete') }}"
                                                                onclick="return confirm('{{ __('are_you_sure_you_want_to_delete_this_item') }}');"
                                                                class="btn ll-p-0"><x-svg.table-delete />
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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
    <script>
        $(document).ready(function() {
            validate();
            $('#title').keyup(validate);
        });

        function validate() {
            if (
                $('#title').val().length > 0) {
                $('#crossB').removeClass('d-none');
            } else {
                $('#crossB').addClass('d-none');
            }
        }

        function RemoveFilter(id) {
            $('#' + id).val('');
            $('#formSubmit').submit();
        }
    </script>
    {{-- Leaflet  --}}
    <x-map.leaflet.map_scripts />
    <script>
        var oldlat = {!! $company->lat ? $company->lat : $setting->default_lat !!};
        var oldlng = {!! $company->long ? $company->long : $setting->default_long !!};

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

            var oldlat = {!! $company->lat ? $company->lat : $setting->default_lat !!};
            var oldlng = {!! $company->long ? $company->long : $setting->default_long !!};

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

            if (status == 1) {
                $(`#status_${id}`).text("{{ __('activated') }}")
            }else{
                $(`#status_${id}`).text("{{ __('deactivated') }}")
            }
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

            if (status == 1) {
                $(`#verification_status_${id}`).text("{{ __('verified') }}")
            }else{
                $(`#verification_status_${id}`).text("{{ __('unverified') }}")
            }
        });
    </script>
    <!-- ================ google map ============== -->
@endsection
