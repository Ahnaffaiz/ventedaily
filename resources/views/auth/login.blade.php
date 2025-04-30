<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ env('APP_NAME') . ' - Login' }}</title>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.layouts.styles')
    @livewireStyles
</head>

<body class="relative flex flex-col">

    <!-- Svg Background -->
    <div class="absolute inset-0 w-screen h-screen">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="100%" height="100%" preserveAspectRatio="none" viewBox="0 0 1920 1028">
            <g mask="url(&quot;#SvgjsMask1166&quot;)" fill="none">
                <use xlink:href="#SvgjsSymbol1173" x="0" y="0"></use>
                <use xlink:href="#SvgjsSymbol1173" x="0" y="720"></use>
                <use xlink:href="#SvgjsSymbol1173" x="720" y="0"></use>
                <use xlink:href="#SvgjsSymbol1173" x="720" y="720"></use>
                <use xlink:href="#SvgjsSymbol1173" x="1440" y="0"></use>
                <use xlink:href="#SvgjsSymbol1173" x="1440" y="720"></use>
            </g>
            <defs>
                <mask id="SvgjsMask1166">
                    <rect width="1920" height="1028" fill="#ffffff"></rect>
                </mask>
                <path d="M-1 0 a1 1 0 1 0 2 0 a1 1 0 1 0 -2 0z" id="SvgjsPath1171"></path>
                <path d="M-3 0 a3 3 0 1 0 6 0 a3 3 0 1 0 -6 0z" id="SvgjsPath1170"></path>
                <path d="M-5 0 a5 5 0 1 0 10 0 a5 5 0 1 0 -10 0z" id="SvgjsPath1169"></path>
                <path d="M2 -2 L-2 2z" id="SvgjsPath1168"></path>
                <path d="M6 -6 L-6 6z" id="SvgjsPath1167"></path>
                <path d="M30 -30 L-30 30z" id="SvgjsPath1172"></path>
            </defs>
            <symbol id="SvgjsSymbol1173">
                <use xlink:href="#SvgjsPath1167" x="30" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="30" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="30" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="30" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="30" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="30" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="30" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="30" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="30" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="30" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="30" y="630" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="30" y="690" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="90" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="90" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="90" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="90" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="90" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="90" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="90" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="90" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="90" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="90" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="90" y="630" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="90" y="690" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="150" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="150" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="150" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="150" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="150" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="150" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="150" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="150" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="150" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="150" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="150" y="630" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="150" y="690" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="210" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="210" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="210" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="210" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="210" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="210" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="210" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="210" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="210" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="210" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="210" y="630" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="210" y="690" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="270" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="270" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="270" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="270" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="270" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="270" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="270" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="270" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="270" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="270" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1172" x="270" y="630" class="stroke-primary/20" stroke-width="3"></use>
                <use xlink:href="#SvgjsPath1171" x="270" y="690" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="330" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="330" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="330" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="330" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="330" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="330" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="330" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="330" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="330" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="330" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="330" y="630" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="330" y="690" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="390" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="390" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="390" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="390" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="390" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="390" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="390" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="390" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="390" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="390" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="390" y="630" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="390" y="690" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="450" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="450" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="450" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="450" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="450" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="450" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="450" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="450" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="450" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="450" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1172" x="450" y="630" class="stroke-primary/20" stroke-width="3"></use>
                <use xlink:href="#SvgjsPath1168" x="450" y="690" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="510" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="510" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1172" x="510" y="150" class="stroke-primary/20" stroke-width="3"></use>
                <use xlink:href="#SvgjsPath1171" x="510" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="510" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="510" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="510" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="510" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="510" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="510" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="570" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="570" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="570" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="570" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="570" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="570" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="570" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="570" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="570" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="570" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="570" y="630" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="570" y="690" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="630" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="630" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="630" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="630" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="630" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="630" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="630" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="630" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="630" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="630" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="630" y="630" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="630" y="690" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="690" y="30" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="690" y="90" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1170" x="690" y="150" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="690" y="210" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="690" y="270" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="690" y="330" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="690" y="390" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1167" x="690" y="450" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="690" y="510" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1169" x="690" y="570" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1168" x="690" y="630" class="stroke-primary/20"></use>
                <use xlink:href="#SvgjsPath1171" x="690" y="690" class="stroke-primary/20"></use>
            </symbol>
        </svg>
    </div>

    <!-- Login Card -->
    <div class="relative flex flex-col items-center justify-center h-screen">
        <div class="flex justify-center">
            <div class="max-w-md px-4 mx-auto">
                <div class="overflow-hidden card">

                    <!-- Logo -->
                    <div class="p-2 bg-primary">
                        <a href="{{ '/' }}" class="flex justify-center">
                            <img src="{{ asset('assets/images/main_dark_logo.png') }}" alt="logo" class="block h-24 dark:hidden">
                            <img src="{{ asset('assets/images/main_light_logo.png') }}" alt="logo" class="hidden h-24 dark:block">
                        </a>
                    </div>

                    <div class="p-9">
                        <div class="w-3/4 mx-auto text-center">
                            <h4 class="mb-2 text-lg font-bold text-center text-dark/70 dark:text-light/80">Sign In</h4>
                            <p class="text-gray-400 mb-9">Enter your email address and password to access admin panel.</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mt-2">
                                <x-label for="email" value="{{ __('Email') }}" />
                                <x-input id="email" class="block w-full mb-2 text-sm form-input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email"/>
                            </div>
                            <div class="mt-4">
                                    <x-label for="password" value="{{ __('Password') }}" />
                                    <x-input id="password" class="block w-full text-sm form-input" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password"/>
                            </div>

                            <div class="block mt-4">
                                <label for="remember_me" class="flex items-center">
                                    <x-checkbox id="remember_me" name="remember" />
                                    <span class="text-sm text-gray-600 ms-2">{{ __('Remember me') }}</span>
                                </label>
                            </div>

                            <div class="mt-2 mb-6 text-center">
                                <button class="text-white btn bg-primary" type="submit"> Log In </button>
                            </div>

                        </form>
                    </div>
                </div>
                <!-- end card -->
            </div>
        </div>
    </div>

    <footer class="absolute inset-x-0 bottom-0">
        <p class="p-6 font-medium text-center">
            <script>document.write(new Date().getFullYear())</script> © Ventestore
        </p>
    </footer>

    @livewireScripts
    @include('components.layouts.partials.customizer')

    @include('components.layouts.partials.footer-scripts')

    @stack('scripts')
    @include('components.layouts.scripts')

</body>

</html>
