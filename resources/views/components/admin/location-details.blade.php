@php
$location = session()->get('location');
@endphp
<div class="card-footer location_footer {{ !$location ? 'd-none':'' }}">
    <span>
        <img src="{{ asset('frontend/assets/images/loader.gif') }}" alt="loading" width="50px" height="50px" class="loader_position d-none">
    </span>
    <div class="location_secion">
        {{ __('country') }}: <span class="location_country">{{ $location && array_key_exists("country", $location) ? $location['country'] : '-' }}</span> <br>
        {{ __('full_address') }}: <span class="location_full_address">{{ $location && array_key_exists("exact_location", $location) ? $location['exact_location'] : '-' }}</span>
    </div>
</div>
