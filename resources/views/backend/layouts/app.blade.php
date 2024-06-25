@if ($setting->default_layout)
    @include('backend.layouts.left-nav')
@else
    @include('backend.layouts.top-nav')
@endif
@include('backend.layouts.partials.preloader')
<input type="hidden" value="{{ current_country_code() }}" id="current_country_code">
