<!-- Topbar Start -->
<header class="app-header flex items-center px-4 gap-3.5">

    @include('components.layouts.partials.logo')

    <!-- Sidenav Menu Toggle Button -->
    <button id="button-toggle-menu" class="p-2 nav-link">
        <span class="sr-only">Menu Toggle Button</span>
        <span class="flex items-center justify-center">
            <i class="text-2xl ri-menu-2-fill"></i>
        </span>
    </button>

    <!-- Theme Setting Button -->
    <div class="relative flex ms-auto">
        <button data-fc-type="offcanvas" data-fc-target="theme-customization" type="button" class="p-2 nav-link">
            <span class="sr-only">Customization</span>
            <span class="flex items-center justify-center">
                <i class="text-2xl ri-settings-3-line"></i>
            </span>
        </button>
    </div>

    <!-- Light/Dark Toggle Button -->
    <div class="hidden lg:flex">
        <button id="light-dark-mode" type="button" class="p-2 nav-link">
            <span class="sr-only">Light/Dark Mode</span>
            <span class="flex items-center justify-center">
                <i class="block text-2xl ri-moon-line dark:hidden"></i>
                <i class="hidden text-2xl ri-sun-line dark:block"></i>
            </span>
        </button>
    </div>

    <!-- Fullscreen Toggle Button -->
    <div class="hidden md:flex">
        <button data-toggle="fullscreen" type="button" class="p-2 nav-link">
            <span class="sr-only">Fullscreen Mode</span>
            <span class="flex items-center justify-center">
                <i class="text-2xl ri-fullscreen-line"></i>
            </span>
        </button>
    </div>

    <!-- Profile Dropdown Button -->
    <div class="relative">
        <button data-fc-type="dropdown" data-fc-placement="bottom-end" type="button"
            class="nav-link flex items-center gap-2.5 px-3 bg-black/5 border-x border-black/10">
            <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="user-image" class="h-8 rounded-full">
            <span class="md:flex flex-col gap-0.5 text-start hidden">
                <h5 class="text-sm">{{ auth()->user()->name }}</h5>
            </span>
        </button>

        <div
            class="z-50 hidden py-2 transition-all duration-300 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 fc-dropdown fc-dropdown-open:opacity-100 w-44 dark:border-gray-700 dark:bg-gray-800">
            <!-- item-->
            <h6 class="flex items-center px-3 py-2 text-xs text-gray-800 dark:text-gray-400">Welcome !</h6>

            <!-- item-->
            <a href="{{ route('user') }}"
                class="flex items-center gap-2 py-1.5 px-4 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                <i class="text-lg align-middle ri-account-circle-line"></i>
                <span>User</span>
            </a>

            <!-- item-->
            <a href="{{ route('settings') }}"
                class="flex items-center gap-2 py-1.5 px-4 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                <i class="text-lg align-middle ri-settings-4-line"></i>
                <span>Settings</span>
            </a>

            <!-- item-->
            <form action="{{ route('logout') }}" method="post" class="flex items-center gap-2 py-1.5 px-4 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                @csrf
                <button class="text-sm text-gray-800">
                    <i class="text-lg align-middle ri-logout-box-line"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>
<!-- Topbar End -->
