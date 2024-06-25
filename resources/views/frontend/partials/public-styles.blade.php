<link rel="icon" type="image/png" href="{{ asset($setting->favicon_image) }}">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="preload"
    as="style">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('frontend/assets/css/free-guide.css') }}">

@stack('frontend_links')
@yield('frontend_links')

@vite(['resources/frontend/sass/app.scss', 'resources/frontend/public.css'])

@php
    $sessionPrimaryColor = session('primaryColor');
    $primaryColor = $sessionPrimaryColor ? $sessionPrimaryColor : $setting->frontend_primary_color;
@endphp

<!-- PWA Meta Theme color and link Start  -->
@if ($setting->pwa_enable)
    <meta name="theme-color" content="{{ $primaryColor }}" />
    <link rel="apple-touch-icon" href="{{ $setting->favicon_image_url }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
@endif
<!-- PWA Meta Theme color and link End -->

<style>
    :root {
        --primary-500: {{ $primaryColor }} !important;
        --primary-600: {{ adjustBrightness($primaryColor, -0.2) }} !important;
        --primary-200: {{ adjustBrightness($primaryColor, 0.7) }} !important;
        --primary-100: {{ adjustBrightness($primaryColor, 0.8) }} !important;
        --primary-50: {{ adjustBrightness($primaryColor, 0.93) }} !important;
        --gray-20: {{ adjustBrightness($primaryColor, 0.98) }} !important;
    }
</style>
