<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description')">
    <meta property="og:image" content="@yield('og:image')">
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" /> --}}
    <title>@yield('title') - {{ config('app.name') }}</title>

    @yield('ld-data')

    {{-- Style --}}
    @include('frontend.partials.styles')
    {{-- @include('frontend.partials.preloader') --}}
    @yield('css')

    {{-- Custome css and js  --}}
    {!! $setting->header_css !!}
    {!! $setting->header_script !!}
</head>

<body dir="{{ langDirection() }}">
    <input type="hidden" value="{{ current_country_code() }}" id="current_country_code">
    <input type="hidden" id="auth_user" value="{{ auth()->check() ? 1 : 0 }}">
    <input type="hidden" id="auth_user_id" value="{{ auth()->check() ? auth()->id() : 0 }}">

    <x-admin.app-mode-alert />
    {{-- Header --}}
    @include('frontend.partials.header')

    {{-- Main --}}
    @yield('main')

    {{-- footer --}}
    @if (!Route::is('candidate.*') && !Route::is('company.*'))
        @include('frontend.partials.footer')
    @endif

    <!-- scripts -->
    @include('frontend.partials.scripts')

    <!-- Custom js -->
    {!! $setting->body_script !!}

    <x-frontend.cookies-allowance :cookies="$cookies" />

    <script>
        // Hide the preloader when loaded
        var el = document.querySelector(".preloader");
        el && window.addEventListener("load", () => el.style.display = "none");
    </script>

    @include('frontend.partials.analytics')

    <!-- PWA Script -->
    @if ($setting->pwa_enable)
        <button class="pwa-install-btn bg-white position-fixed d-none" id="installApp">
            <img src="{{ asset('pwa-btn.png') }}" alt="Install App" loading="lazy">
        </button>
        <script src="{{ asset('/sw.js') }}"></script>
        <script>
            if (!navigator.serviceWorker) {
                navigator.serviceWorker.register("/sw.js").then(function(reg) {
                    console.log("Service worker has been registered for scope: " + reg);
                });
            }

            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                $('#installApp').removeClass('d-none');
                deferredPrompt = e;
            });

            const installApp = document.getElementById('installApp');
            installApp.addEventListener('click', async () => {
                if (deferredPrompt !== null) {
                    deferredPrompt.prompt();
                    const {
                        outcome
                    } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        deferredPrompt = null;
                    }
                }
            });
        </script>
    @endif
</body>

</html>
