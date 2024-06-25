@props(['height' => 21, 'width' => 21, 'fill' => '#2ecc71'])
<svg xmlns="http://www.w3.org/2000/svg" width="{{ $height }}" height="{{ $width }}" fill="{{ $fill }}"
    viewBox="0 0 256 256">
    <rect width="256" height="256" fill="none"></rect>
    <path d="M128,56C48,56,16,128,16,128s32,72,112,72,112-72,112-72S208,56,128,56Z" fill="none"
        stroke="{{ $fill }}" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path>
    <circle cx="128" cy="128" r="40" fill="none" stroke="{{ $fill }}"
        stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></circle>
</svg>
