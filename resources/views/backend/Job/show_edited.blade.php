@extends('backend.layouts.app')
@section('title')
    {{ $job->title }}
@endsection
@section('content')
    @php
        $userr = auth()->user();
    @endphp
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title line-height-36">{{ __('compare_changes') }}</h3>
                <div class="float-right d-flex align-items-center justify-content-center">
                    <form action="{{ route('admin.job.edited.approved', $job->slug) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" onclick="return confirm('{{ __('are_you_sure_item') }}');"
                            class="btn btn-success">
                            {{ __('approved_changes') }}
                        </button>
                    </form>
                    <a href="{{ route('admin.job.edited.index') }}" class="btn bg-primary ml-2"><i
                            class="fas fa-arrow-left mr-1"></i>{{ __('back') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">
                            {{ __('edited_job_content') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="pt-1 pb-4">
                            <div class="row form-group">
                                <div class="col-12">
                                    <label for="title">
                                        {{ __('title') }}
                                    </label>
                                    <input id="title" class="form-control" value="{{ $job->title }}" disabled>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label for="company">
                                        {{ __('company') }}
                                    </label>
                                    <select class="form-control" id="company" disabled>
                                        <option> {{ $job->company->user->name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="category">
                                        {{ __('category') }}
                                    </label>
                                    <select class="form-control" id="category" disabled>
                                        <option>{{ $job->category->name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="job_role">
                                        {{ __('job_role') }}
                                    </label>
                                    <select class="form-control" id="job_role" disabled>
                                        <option>{{ $job->role->name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-6">
                                    <label for="experience">
                                        {{ __('experience') }}
                                    </label>
                                    <select class="form-control" id="experience" disabled>
                                        <option>{{ $job->experience->name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="gender">
                                        {{ __('gender') }}
                                    </label>
                                    <select name="gender" class="form-control @error('gender') is-invalid @enderror"
                                        id="gender" disabled>
                                        <option> {{ __($job->gender) }}</option>
                                    </select>
                                    @error('gender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ __($message) }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-6">
                                    <label for="education">
                                        {{ __('education') }}
                                    </label>
                                    <input id="education" class="form-control" value="{{ $job->education->name }}"
                                        disabled>
                                </div>
                                <div class="col-6">
                                    <label for="job_type">
                                        {{ __('job_type') }}
                                    </label>
                                    <input id="job_type" class="form-control"
                                        value="{{ $job->job_type ? $job->job_type->name : '' }}" disabled>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-6">
                                    <label for="tags">
                                        {{ __('tags') }}
                                    </label>
                                    <br>
                                    @foreach ($job->tags as $tag)
                                        <button type="button" class="c-btn btn btn-xs btn-outline-primary">
                                            {{ $tag->name }}
                                        </button>
                                    @endforeach
                                </div>
                                <div class="col-6">
                                    <label for="deadline">
                                        {{ __('deadline') }}
                                    </label>
                                    <input id="deadline" class="form-control"
                                        value="{{ date('Y-m-d', strtotime($job->deadline)) }}" disabled>
                                </div>
                            </div>
                            <div class="row form-group">
                                @if ($job->salary_mode == 'range')
                                <div class="col-md-4 col-sm-3">
                                    <label for="min_salary">
                                        {{ __('min_salary') }}
                                    </label>
                                    <input id="min_salary" class="form-control" value="{{ $job->min_salary }}" disabled>
                                </div>
                                <div class="col-md-4 col-sm-3">
                                    <label for="max_salary">
                                        {{ __('max_salary') }}
                                    </label>
                                    <input id="max_salary" class="form-control" value="{{ $job->max_salary }}" disabled>
                                </div>
                                @else
                                <div class="col-md-4 col-sm-3">
                                    <label for="max_salary">
                                        {{ __('salary') }}
                                    </label>
                                    <input class="form-control" value="{{ $job->custom_salary }}" disabled>
                                </div>
                                @endif
                                <div class="col-md-4 col-sm-3">
                                    <label for="salary_type">
                                        {{ __('salary_type') }}
                                    </label>
                                    <input id="salary_type" class="form-control" value="{{ __($job->salary_type->name) }}"
                                        disabled>
                                </div>
                            </div>
                            <div class="row p-4">
                                <div class="col-md-3 form-check">
                                    <div class="icheck-success d-inline">
                                        <input value="1" name="featured" type="checkbox" class="form-check-input"
                                            id="featured" @if ($job->featured === 1) checked @endif disabled>
                                        <label class="form-check-label mr-5" for="featured">{{ __('featured') }}</label>
                                    </div>
                                    @error('featured')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-3 form-check">
                                    <div class="icheck-success d-inline">
                                        <input value="1" name="highlight" type="checkbox" class="form-check-input"
                                            id="highlight" @if ($job->highlight === 1) checked @endif disabled>
                                        <label class="form-check-label mr-5"
                                            for="highlight">{{ __('highlight') }}</label>
                                    </div>
                                    @error('highlight')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ __($message) }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-3 form-check">
                                    <div class="icheck-success d-inline">
                                        <input value="1" name="is_remote" type="checkbox" class="form-check-input"
                                            id="is_remote" @if ($job->is_remote === 1) checked @endif disabled>
                                        <label class="form-check-label mr-5" for="is_remote">{{ __('remote') }}</label>
                                    </div>
                                    @error('is_remote')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label for="description">{{ __('description') }}</label>
                                    <p>
                                        {!! $job->description !!}
                                    </p>
                                </div>
                            </div>
                            {{-- Location --}}
                            <div class="form-group row">
                                <div class="col-md-12">
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">
                            {{ __('published_job_content') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="pt-1 pb-4">
                            <div class="row form-group">
                                <div class="col-12">
                                    <label for="title">
                                        {{ __('title') }}
                                    </label>
                                    <input id="title" class="form-control" value="{{ $parent_job->title }}"
                                        disabled>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label for="company">
                                        {{ __('company') }}
                                    </label>
                                    <select class="form-control" id="company" disabled>
                                        <option> {{ $parent_job->company->user->name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="category">
                                        {{ __('category') }}
                                    </label>
                                    <select class="form-control" id="category" disabled>
                                        <option>{{ $parent_job->category->name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="job_role">
                                        {{ __('job_role') }}
                                    </label>
                                    <select class="form-control" id="job_role" disabled>
                                        <option>{{ $parent_job->role->name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-6">
                                    <label for="experience">
                                        {{ __('experience') }}
                                    </label>
                                    <select class="form-control" id="experience" disabled>
                                        <option>{{ $parent_job->experience->name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="gender">
                                        {{ __('gender') }}
                                    </label>
                                    <select name="gender" class="form-control @error('gender') is-invalid @enderror"
                                        id="gender" disabled>
                                        <option> {{ __($parent_job->gender) }}</option>
                                    </select>
                                    @error('gender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ __($message) }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-6">
                                    <label for="education">
                                        {{ __('education') }}
                                    </label>
                                    <input id="education" class="form-control"
                                        value="{{ $parent_job->education->name }}" disabled>
                                </div>
                                <div class="col-6">
                                    <label for="job_type">
                                        {{ __('job_type') }}
                                    </label>
                                    <input id="job_type" class="form-control"
                                        value="{{ $parent_job->job_type ? $parent_job->job_type->name : '' }}" disabled>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-6">
                                    <label for="tags">
                                        {{ __('tags') }}
                                    </label>
                                    <br>
                                    @foreach ($job->tags as $tag)
                                        <button type="button" class="c-btn btn btn-xs btn-outline-primary">
                                            {{ $tag->name }}
                                        </button>
                                    @endforeach
                                </div>
                                <div class="col-6">
                                    <label for="deadline">
                                        {{ __('deadline') }}
                                    </label>
                                    <input id="deadline" class="form-control"
                                        value="{{ date('Y-m-d', strtotime($parent_job->deadline)) }}" disabled>
                                </div>
                            </div>
                            <div class="row form-group">
                                @if ($job->salary_mode == 'range')
                                <div class="col-md-4 col-sm-3">
                                    <label for="min_salary">
                                        {{ __('min_salary') }}
                                    </label>
                                    <input id="min_salary" class="form-control" value="{{ $parent_job->min_salary }}"
                                        disabled>
                                </div>
                                <div class="col-md-4 col-sm-3">
                                    <label for="max_salary">
                                        {{ __('max_salary') }}
                                    </label>
                                    <input id="max_salary" class="form-control" value="{{ $parent_job->max_salary }}"
                                        disabled>
                                </div>
                                @else
                                <div class="col-md-4 col-sm-3">
                                    <label for="salary">
                                        {{ __('salary') }}
                                    </label>
                                    <input id="salary" class="form-control" value="{{ $parent_job->custom_salary }}"
                                        disabled>
                                </div>
                                @endif
                                <div class="col-md-4 col-sm-3">
                                    <label for="salary_type">
                                        {{ __('salary_type') }}
                                    </label>
                                    <input id="salary_type" class="form-control"
                                        value="{{ __($parent_job->salary_type->name) }}" disabled>
                                </div>
                            </div>
                            <div class="row p-4">
                                <div class="col-md-3 form-check">
                                    <div class="icheck-success d-inline">
                                        <input value="1" name="featured" type="checkbox" class="form-check-input"
                                            id="featured" @if ($parent_job->featured === 1) checked @endif disabled>
                                        <label class="form-check-label mr-5" for="featured">{{ __('featured') }}</label>
                                    </div>
                                    @error('featured')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-3 form-check">
                                    <div class="icheck-success d-inline">
                                        <input value="1" name="highlight" type="checkbox" class="form-check-input"
                                            id="highlight" @if ($parent_job->highlight === 1) checked @endif disabled>
                                        <label class="form-check-label mr-5"
                                            for="highlight">{{ __('highlight') }}</label>
                                    </div>
                                    @error('highlight')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ __($message) }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-3 form-check">
                                    <div class="icheck-success d-inline">
                                        <input value="1" name="is_remote" type="checkbox" class="form-check-input"
                                            id="is_remote" @if ($parent_job->is_remote === 1) checked @endif disabled>
                                        <label class="form-check-label mr-5" for="is_remote">{{ __('remote') }}</label>
                                    </div>
                                    @error('is_remote')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label for="description">{{ __('description') }}</label>
                                    <p>
                                        {!! $parent_job->description !!}
                                    </p>
                                </div>
                            </div>
                            {{-- Location --}}
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label for="description">{{ __('location') }}</label>
                                    <div class="p-half rounded">
                                        <x-website.map.map-warning />

                                        @if ($map == 'google-map')
                                            <div class="map mymap" id="googlee-mapp"></div>
                                        @else
                                            <div id="leaflet-map2"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <style>
        .ck-editor__editable_inline {
            min-height: 400px;
        }

        .c-btn {
            padding-left: 22px;
            padding-right: 22px;
            border-radius: 15px;
            margin-right: 8px;
            background: #e9ecef;
        }

        .c-btn:hover {
            background: #e9ecef !important;
            color: blue !important;
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
    @include('map::links')
@endsection

@section('script')
    <script src="{{ asset('backend') }}/plugins/select2/js/select2.full.min.js"></script>
    @if ($map == 'leaflet')
        <x-map.leaflet.map_scripts />
        <!-- leaf-let map  -->
        <script>
            function leaftet(oldlat, oldlng, id) {
                // Map preview
                var element = document.getElementById(id);

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
            }

            var oldlat = {!! $job->lat ? $job->lat : $setting->default_lat !!};
            var oldlng = {!! $job->long ? $job->long : $setting->default_long !!};

            var oldlat2 = {!! $parent_job->lat ? $parent_job->lat : $setting->default_lat !!};
            var oldlng2 = {!! $parent_job->long ? $parent_job->long : $setting->default_long !!};
            var id = "leaflet-map"
            var id2 = "leaflet-map2"

            leaftet(oldlat, oldlng, id);
            leaftet(oldlat2, oldlng2, id2);
        </script>
        <!-- leaf-let map  -->
    @endif
    <script>
        $(document).ready(function() {
            $('#tags').select2({
                theme: 'bootstrap4',
                tags: true,
                tokenSeparators: [',', ' ']
            });
        });

        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    </script>

    <!-- ================ google map ============== -->
    <x-website.map.google-map-check />
    <script>
        function initMap() {
            var token = "{{ $setting->google_map_key }}";

            var latlng = new google.maps.LatLng(
                {!! $job->lat ? $job->lat : $setting->default_lat !!},
                {!! $job->long ? $job->long : $setting->default_long !!}
            );
            var latlng2 = new google.maps.LatLng(
                {!! $parent_job->lat ? $parent_job->lat : $setting->default_lat !!},
                {!! $parent_job->long ? $parent_job->long : $setting->default_long !!}
            );

            var myOptions = {
                zoom: 7,
                center: latlng,
            };

            var myOptions2 = {
                zoom: 7,
                center: latlng2,
            };

            var map = new google.maps.Map(document.getElementById("google-map"), myOptions);
            var map2 = new google.maps.Map(document.getElementById("googlee-mapp"), myOptions2);

            const image = "https://gisgeography.com/wp-content/uploads/2018/01/map-marker-3-116x200.png";
            var myMarker = new google.maps.Marker({
                draggable: false,
                position: latlng,
                map: map,
                // icon: image
            });

            var myMarker2 = new google.maps.Marker({
                draggable: false,
                position: latlng2,
                map: map2,
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
