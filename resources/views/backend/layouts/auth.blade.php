<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('sign_in') }} | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" /> --}}
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/fontawesome-free/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $setting->favicon_image_url }}">
    @vite('resources/backend/app.css')

    <!-- For PWA Theme Color as it is Frontend Start  -->
    @php
        $sessionPrimaryColor = session('primaryColor');
        $primaryColor = $sessionPrimaryColor ? $sessionPrimaryColor : $setting->frontend_primary_color;
    @endphp
    <!-- For PWA Theme Color as it is Frontend End  -->

    <!-- PWA Meta Theme color and link Start  -->
    @if($setting->pwa_enable)
        <meta name="theme-color" content="{{ $primaryColor }}"/>
        <link rel="apple-touch-icon" href="{{ $setting->favicon_image_url }}">
        <link rel="manifest" href="{{ asset('/manifest.json') }}">
    @endif
    <!-- PWA Meta Theme color and link End -->

    <style>
    :root {
        /* For PWA Theme Color as it is Frontend  */
        --primary-500: {{ $primaryColor }} !important;
    }
    </style>

    <style>
        .system-logo {
            max-width: 200px !important;
        }

        @media (min-width: 768px) {
            .login-card-body {
                width: 380px !important;
                max-width: 380px !important;
            }
        }

        .login-card-body .input-group input.form-control,
        .login-card-body button.btn {
            padding: 12px 20px;
            height: unset !important;
        }

        .quote {
            max-width: 380px;
            margin: 0 auto;
        }

        .background-view {
            background-image: url('https://source.unsplash.com/random/1920x1280/?nature,landscape,mountains'), url('/backend/image/river.jpeg');
            background-size: cover;
        }
    </style>

    {{-- ==================================================== --}}
    {{-- ================ DO NOT REMOVE THIS ================ --}}
    {{-- ==================================================== --}}


    {{-- ==================================================== --}}
    {{-- ================ DO NOT REMOVE THIS ================ --}}
    {{-- ==================================================== --}}

    @yield('backend_auth_link')
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-5">
                <div class="d-flex flex-column justify-content-between align-items-center py-5 px-4 min-vh-100">
                    <a href="{{ route('admin.login') }}" class="d-block">
                        <div class="system-logo d-flex justify-content-center">
                            <img src="{{ $setting->dark_logo_url }}" alt="{{ __('logo') }}" class="img-fluid">
                        </div>
                    </a>
                    <div class="login-card-body p-0">
                        @yield('content')
                    </div>
                    <div class="text-center text-secondary quote">
                        {{ inspireMe() }}
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-7 col d-lg-block d-none">
                <div class="h-100 min-vh-100 background-view">
                </div>
            </div>
        </div>
    </div>

    <!-- PWA Button Start -->
    <button class="pwa-install-btn bg-white position-fixed d-none" id="installApp">
        <img src="{{ asset('pwa-btn.png') }}" alt="Install App">
    </button>
    <!-- PWA Button End -->

    <script src="{{ asset('backend/plugins/jquery/jquery.min.js') }}"></script>

    @yield('backend_auth_script')

    <!-- PWA Script Start -->
    @if($setting->pwa_enable)
        <script src="{{ asset('/sw.js') }}"></script>
        <script>
            if (!navigator.serviceWorker) {
                navigator.serviceWorker.register("/sw.js").then(function (reg) {
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
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        deferredPrompt = null;
                    }
                }
            });
        </script>
    @endif
    <!-- PWA Script End -->

</body>

</html>
