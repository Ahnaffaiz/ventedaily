<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/light_logo.png') }}" type="image/x-icon">
    <title>{{ isset($title) && $title ? $title . ' - Ventedaily' : 'Page Title - Ventedaily' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.layouts.styles')
    @livewireStyles
</head>

<body>
    <!-- Preloader Component -->
    @include('components.layouts.partials.preloader')

    <div class="flex wrapper">
        <div class="page-content">
            <main class="p-6">
                @include('components.layouts.partials.page-title', ["subtitle" => $subtitle ?? '', "title" => $title ?? ''])
                {{ $slot }}
            </main>
            @include('components.layouts.partials.footer')
        </div>
    </div>

    @livewireScripts
    <!-- Load preloader script first -->
    <script src="{{ asset('assets/js/preloader.js') }}"></script>
    @include('components.layouts.partials.customizer')
    @include('components.layouts.partials.footer-scripts')
    @stack('scripts')
    @include('components.layouts.scripts')
</body>

</html>
