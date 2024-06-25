<footer class="main-footer d-flex justify-content-between">
    <div>
        <strong> &copy; <a href="{{ config('app.url') }}">{{ config('app.name') }}</a> {{ date('Y') }} </strong>.
        {{ __('all_rights_reserved') }}.
    </div>
    <div class="float-right d-none d-sm-inline-block pr-5">
        <b>{{ __('version') }}</b> {{ config('app.version') }}
    </div>
</footer>
