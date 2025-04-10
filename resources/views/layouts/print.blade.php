<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/light_logo.png') }}" type="image/x-icon">
    <title>@yield('title') </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.layouts.styles')
    @livewireStyles
</head>

<body class="bg-white font-['Times_New_Roman',_serif] text-black">
    <div class="flex wrapper">
        <div class="page-content">

            <main class="p-6">
                @yield('content')
            </main>
        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    @livewireScripts
    @include('components.layouts.partials.customizer')

    @include('components.layouts.partials.footer-scripts')

    @stack('scripts')
    @include('components.layouts.scripts')
</body>

</html>
