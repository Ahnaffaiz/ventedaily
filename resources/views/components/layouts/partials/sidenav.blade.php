<div class="app-menu">

    @include('components.layouts.partials.logo')

    <!-- Sidenav Menu Toggle Button -->
    <button id="button-hover-toggle" class="absolute top-5 end-2 rounded-full p-1.5 z-50">
        <span class="sr-only">Menu Toggle Button</span>
        <i class="text-xl ri-checkbox-blank-circle-line"></i>
    </button>

    <!--- Menu -->
    <div class="scrollbar" data-simplebar>
        <ul class="menu" data-fc-type="accordion">
            <li class="menu-title">Navigation</li>

            <li class="menu-item">
                <a href="javascript:void(0)" data-fc-type="collapse" class="menu-link">
                    <span class="menu-icon">
                        <i class="ri-home-4-line"></i>
                    </span>
                    <span class="menu-text"> Dashboard </span>
                    <span class="rounded-full badge bg-success">2</span>
                </a>

                <ul class="hidden sub-menu">
                    <li class="menu-item">
                        <a href="dashboard-analytics.html" class="menu-link">
                            <span class="menu-text">Analytics</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="index.html" class="menu-link">
                            <span class="menu-text">Ecommerce</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-title">Inventory</li>

            {{-- product --}}
            <li class="menu-item">
                @php
                    $activeRoutes = ['product', 'category', 'color', 'size', 'create-product'];
                    $isActive = in_array(request()->route()->getName(), $activeRoutes);
                @endphp
                <a href="javascript:void(0)" data-fc-type="collapse" class="menu-link">
                    <span class="menu-icon">
                        <i class="ri-shirt-line {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"></i>
                    </span>
                    <span class="menu-text {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"> Product </span>
                    <span class="menu-arrow"></span>
                </a>

                <ul class="hidden sub-menu">
                    <x-menu-item activeRoute="product" text="Product" />
                    <x-menu-item activeRoute="category" text="Category" />
                    <x-menu-item activeRoute="color" text="Color" />
                    <x-menu-item activeRoute="size" text="Size" />
                </ul>
            </li>
            {{-- end of product --}}

            {{-- stock management --}}
            <x-menu-item activeRoute="stock-management" text="Stock Management" iconClass="ri-archive-line" />
            {{-- end of stock management --}}

            {{-- Purchase --}}
            <li class="menu-item">
                @php
                    $activeRoutes = ['create-purchase', 'purchase'];
                    $isActive = in_array(request()->route()->getName(), $activeRoutes);
                @endphp
                <a href="javascript:void(0)" data-fc-type="collapse" class="menu-link">
                    <span class="menu-icon">
                        <i
                            class="ri-shopping-cart-2-line {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"></i>
                    </span>
                    <span class="menu-text {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"> Purchase </span>
                    <span class="menu-arrow"></span>
                </a>

                <ul class="hidden sub-menu">
                    <x-menu-item activeRoute="create-purchase" text="Create Purchase" />
                    <x-menu-item activeRoute="purchase" text="Purchase" />
                </ul>
            </li>
            <x-menu-item activeRoute="supplier" text="Supplier" iconClass="ri-user-line" />

            <li class="menu-title">Sales</li>

            {{-- Keep Booking --}}
            <li class="menu-item">
                @php
                    $activeRoutes = ['create-keep', 'keep'];
                    $isActive = in_array(request()->route()->getName(), $activeRoutes);
                @endphp
                <a href="javascript:void(0)" data-fc-type="collapse" class="menu-link">
                    <span class="menu-icon">
                        <i class="ri-shopping-bag-line {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"></i>
                    </span>
                    <span class="menu-text {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"> Keep </span>
                    <span class="menu-arrow"></span>
                </a>

                <ul class="hidden sub-menu">
                    <x-menu-item activeRoute="create-keep" text="Create Keep" />
                    <x-menu-item activeRoute="keep" text="Keep List" />
                </ul>
            </li>
            {{-- end of Keep Booking --}}

            {{-- Sale --}}
            <li class="menu-item">
                @php
                    $activeRoutes = ['create-sale', 'sale'];
                    $isActive = in_array(request()->route()->getName(), $activeRoutes);
                @endphp
                <a href="javascript:void(0)" data-fc-type="collapse" class="menu-link">
                    <span class="menu-icon">
                        <i class="ri-calculator-line {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"></i>
                    </span>
                    <span class="menu-text {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"> Sale </span>
                    <span class="menu-arrow"></span>
                </a>

                <ul class="hidden sub-menu">
                    <x-menu-item activeRoute="create-sale" text="Cashier" />
                    <x-menu-item activeRoute="sale" text="Sale List" />
                </ul>
            </li>
            {{-- end of Sale --}}

            {{-- customer --}}
            <li class="menu-item">
                @php
                    $activeRoutes = ['customer', 'group'];
                    $isActive = in_array(request()->route()->getName(), $activeRoutes);
                @endphp
                <a href="javascript:void(0)" data-fc-type="collapse" class="menu-link">
                    <span class="menu-icon">
                        <i class="ri-shield-user-line {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"></i>
                    </span>
                    <span class="menu-text {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"> Customer </span>
                    <span class="menu-arrow"></span>
                </a>

                <ul class="hidden sub-menu">
                    <x-menu-item activeRoute="customer" text="Customer" />
                    <x-menu-item activeRoute="group" text="Group" />
                </ul>
            </li>
            {{-- end of customer --}}
            <x-menu-item activeRoute="discount" text="Discount" iconClass="ri-price-tag-3-line" />

            <x-menu-item activeRoute="settings" text="Settings" iconClass="ri-settings-line" />
        </ul>

    </div>
</div>
<!-- Sidenav Menu End  -->
