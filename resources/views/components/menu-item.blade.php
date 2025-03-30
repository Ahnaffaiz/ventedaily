@props([
    'activeRoute' => '#',
    'text' => '',
    'iconClass' => null,
])
@php
    $isActive = request()->routeIs($activeRoute);
@endphp

<li class="menu-item">
    <a href="{{ is_array($activeRoute) ? route($activeRoute[0], $activeRoute[1]) : route($activeRoute) }}" class="menu-link">
        @if ($iconClass)
            <span class="menu-icon {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}">
                <i class="{{ $iconClass }}"></i>
            </span>
        @endif
        <span class="menu-text {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}">{{ $text }}</span>
    </a>
</li>
