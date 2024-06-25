@props([
    'width' => 16,
    'height' => 20,
    'fill' => 'none',
    'stroke' => 'currentColor'
])

<svg width="{{ $width }}" height="{{ $height }}" viewBox="0 0 24 24" fill="{{ $fill }}" xmlns="http://www.w3.org/2000/svg">
<path d="M18 21L11.9993 17.25L6 21V4.5C6 4.30109 6.07902 4.11032 6.21967 3.96967C6.36032 3.82902 6.55109 3.75 6.75 3.75H17.25C17.4489 3.75 17.6397 3.82902 17.7803 3.96967C17.921 4.11032 18 4.30109 18 4.5V21Z" stroke="{{ $stroke }}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

