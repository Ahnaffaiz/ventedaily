@props([
    'activeRoute' => '#',
    'text' => '',
    'iconClass' => null,
])
@php
    $isActive = request()->routeIs($activeRoute);
@endphp

<li class="menu-item ">
    <a wire:navigate href="{{ route($activeRoute) }}" class="menu-link {{ $isActive ? 'text-white font-semibold' : 'text-gray-200' }}">
        @if ($iconClass)
            <span class="menu-icon">
                <i class="{{ $iconClass }}"></i>
            </span>
        @endif
        <span class="menu-text">{{ $text }}</span>
    </a>
</li>
