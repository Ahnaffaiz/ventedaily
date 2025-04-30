@if(isset($databaseError) && $databaseError)
    @include('errors.database')
    @php exit; @endphp
@endif
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ isset($title) && $title ? $title . ' - Ventedaily' : 'Page Title - Ventedaily' }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/light_logo.png') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.layouts.styles')
    @livewireStyles
</head>

<body>
    <!-- Preloader Component -->
    @include('components.layouts.partials.preloader')

    <div class="flex wrapper">
        @include('components.layouts.partials.menu')
        <div class="page-content">
            @include('components.layouts.partials.topbar')
            <main class="p-6">
                @include('components.layouts.partials.page-title', ["subtitle" => $subtitle ?? '', "title" => $title ?? ''])
                {{ $slot }}
            </main>
            @include('components.layouts.partials.footer')
        </div>
    </div>

    @livewireScripts
    @livewireChartsScripts
    <!-- Load preloader script first -->
    <script src="{{ asset('assets/js/preloader.js') }}"></script>
    @include('components.layouts.partials.customizer')
    @include('components.layouts.partials.footer-scripts')
    @stack('scripts')
    @include('components.layouts.scripts')
    <x-livewire-alert::scripts />

    <!-- Script to handle LivewireAlert dark mode with wire:navigate -->
    <script>
        document.addEventListener('livewire:navigated', function() {
            if (window.initLivewireAlertDarkMode) {
                window.initLivewireAlertDarkMode();
            }
        });
    </script>
</body>

</html>
