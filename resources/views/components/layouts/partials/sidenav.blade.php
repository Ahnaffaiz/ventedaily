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
            <x-menu-item activeRoute="dashboard" text="Dashboard" iconClass="ri-home-3-line" />

            {{-- Sale --}}
            @if (auth()->user()->hasAnyRole(['Admin', 'Sales', 'Accounting']))
                <li class="menu-title">Sales</li>
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
                        @if (auth()->user()->canAny(['Create Sale', 'Update Sale']))
                            <x-menu-item activeRoute="create-sale" text="Cashier" />
                        @endif
                        <x-menu-item activeRoute="sale" text="Sale List" />
                    </ul>
                </li>
            @endif
            {{-- end of Sale --}}

            {{-- Keep Booking --}}
            @if (auth()->user()->hasAnyRole(['Admin', 'Sales', 'Accounting', 'User']))
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
                    @if (auth()->user()->canAny(['Create Keep', 'Update Keep']))
                        <x-menu-item activeRoute="create-keep" text="Create Keep" />
                    @endif
                    <x-menu-item activeRoute="keep" text="Keep List" />
                </ul>
            </li>
            @endif
            {{-- end of Keep Booking --}}

            {{-- Pre Order --}}
            @if (auth()->user()->hasAnyRole(['Admin', 'Sales', 'Accounting', 'User']))
            <li class="menu-item">
                @php
                    $activeRoutes = ['create-pre-order', 'pre-order'];
                    $isActive = in_array(request()->route()->getName(), $activeRoutes);
                @endphp
                <a href="javascript:void(0)" data-fc-type="collapse" class="menu-link">
                    <span class="menu-icon">
                        <i class="ri-gift-line {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"></i>
                    </span>
                    <span class="menu-text {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"> Pre Order </span>
                    <span class="menu-arrow"></span>
                </a>

                <ul class="hidden sub-menu">
                    @if (auth()->user()->canAny(['Create Pre Order', 'Update Pre Order']))
                        <x-menu-item activeRoute="create-pre-order" text="Create Pre Order" />
                    @endif
                    <x-menu-item activeRoute="pre-order" text="Pre Order List" />
                </ul>
            </li>
            @endif
            {{-- end of Pre Order --}}

            {{-- Ventedaily --}}
            @if (auth()->user()->hasAnyRole(['Admin', 'Sales', 'Accounting']))
            <li class="menu-item">
                @php
                    $activeRoutes = ['shipping', 'withdrawal', 'online-sales'];
                    $isActive = in_array(request()->route()->getName(), $activeRoutes);
                @endphp
                <a href="javascript:void(0)" data-fc-type="collapse" class="menu-link">
                    <span class="menu-icon">
                        <i class="ri-emotion-line {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"></i>
                    </span>
                    <span class="menu-text {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"> Ventedaily </span>
                    <span class="menu-arrow"></span>
                </a>

                <ul class="hidden sub-menu">
                    <x-menu-item activeRoute="online-sales" text="Online Sales" />
                    <x-menu-item activeRoute="shipping" text="Shipping" />
                    <x-menu-item activeRoute="withdrawal" text="Withdrawal" />
                </ul>
            </li>
            @endif
            {{-- end of Ventedaily --}}

            {{-- customer --}}
            @if (auth()->user()->hasAnyRole(['Admin', 'Sales', 'Accounting']))
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
            @endif
            {{-- end of customer --}}
            @if (auth()->user()->hasAnyRole(['Admin', 'Sales', 'Accounting']))
                <x-menu-item activeRoute="discount" text="Discount" iconClass="ri-price-tag-3-line" />
            @endif

            <li class="menu-title">Inventory</li>

            {{-- product --}}
            <li class="menu-item">
                @php
                    $activeRoutes = ['product', 'category', 'color', 'size', 'create-product', 'transfer-stock', 'create-transfer-stock', 'stock-in', 'create-stock-in'];
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
                    @if (auth()->user()->hasAnyRole(['Admin', 'User', 'Warehouse']))
                        <x-menu-item activeRoute="product" text="Product" />
                    @endif
                    @if (auth()->user()->canAny(['Create Product Stock', 'Update Product Stock', 'Delete Product Stock']))
                        <x-menu-item activeRoute="transfer-stock" text="Transfer Stock" />
                        <x-menu-item activeRoute="stock-in" text="Stock In" />
                    @endif
                    @if (auth()->user()->hasRole('Admin'))
                        <x-menu-item activeRoute="category" text="Category" />
                        <x-menu-item activeRoute="color" text="Color" />
                        <x-menu-item activeRoute="size" text="Size" />
                    @endif
                    <li class="menu-item">
                        <a target="_blank" href="{{ route('product-stock') }}" class="menu-link">
                            <span class="text-sm text-secondary">Product Stock</span>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- end of product --}}

            {{-- Purchase --}}
            @if (auth()->user()->hasAnyRole(['Admin', 'Accounting']))
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
                    @if (auth()->user()->canAny(['Create Purchase', 'Update Purchase']))
                        <x-menu-item activeRoute="create-purchase" text="Create Purchase" />
                    @endif
                    <x-menu-item activeRoute="purchase" text="Purchase" />
                </ul>
            </li>
            @endif

            {{-- Retur --}}
            @if (auth()->user()->hasAnyRole(['Admin', 'Accounting', 'Warehouse', 'Sales']))
            <li class="menu-item">
                @php
                    $activeRoutes = ['retur', 'create-retur'];
                    $isActive = in_array(request()->route()->getName(), $activeRoutes);
                @endphp
                <a href="javascript:void(0)" data-fc-type="collapse" class="menu-link">
                    <span class="menu-icon">
                        <i class="ri-text-wrap {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"></i>
                    </span>
                    <span class="menu-text {{ $isActive ? 'text-white font-bold' : 'text-gray-300' }}"> Retur </span>
                    <span class="menu-arrow"></span>
                </a>

                <ul class="hidden sub-menu">
                    @if (auth()->user()->canAny(['Create Retur', 'Update Retur']))
                        <x-menu-item activeRoute="create-retur" text="Create Retur" />
                    @endif
                    <x-menu-item activeRoute="retur" text="Retur" />
                </ul>
            </li>
            @endif
            {{-- end of Retur --}}
            @if (auth()->user()->hasAnyRole(['Admin', 'Warehouse' ,'Accounting']))
                <x-menu-item activeRoute="supplier" text="Supplier" iconClass="ri-user-line" />
            @endif

            @if (auth()->user()->hasAnyRole(['Admin', 'Sales' ,'Accounting']))
                <li class="menu-title">Finance</li>
                <x-menu-item activeRoute="cost" text="Cost" iconClass="ri-money-dollar-box-line" />
                <x-menu-item activeRoute="expense" text="Expense" iconClass="ri-bank-line" />
            @endif

            @if (auth()->user()->hasRole('Admin'))
                <x-menu-item activeRoute="settings" text="Settings" iconClass="ri-settings-line" />
                <li class="menu-title">User and Role</li>
                <x-menu-item activeRoute="user" text="User" iconClass="ri-group-line" />
                <x-menu-item activeRoute="role" text="Role" iconClass="ri-briefcase-line" />
                <div class="mb-10"></div>
            @endif

        </ul>

    </div>
</div>
<!-- Sidenav Menu End  -->
