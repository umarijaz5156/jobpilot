@props(['linkActive', 'route', 'icon', 'path', 'plus_icon'])

<li class="nav-item hover-icon">
    <a href="{{ route($route) }}" class="nav-link {{ $linkActive ? 'active' : '' }}">
        <i class="nav-icon {{ $icon }}"></i>
        <p>{{ $slot }}</p>
    </a>
    <a href="{{ route($path) }}">
        <i class="{{ $plus_icon }} right ico" aria-hidden="true"></i>
    </a>
</li>
