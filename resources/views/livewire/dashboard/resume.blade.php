<div class="grid grid-cols-1 gap-4 mb-6 md:gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 2xl:grid-cols-5">
    <div class="sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-span-1">
        <div class="h-full card">
            <div class="p-4 md:p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 text-sm font-normal text-gray-600 md:text-base" title="Number of Customers">Total Keeps Today</h5>
                        <h3 class="my-3 text-xl md:my-6 md:text-2xl">Rp. {{ number_format($keep_prices, 0, ',', '.') }}</h3>
                        <p class="text-sm text-gray-400 truncate md:text-base">
                            <span class="bg-primary rounded-md text-xs md:text-md px-1 md:px-1.5 py-0.5 text-white me-1">{{ $keep_items }}</span>
                            <span>Product</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-span-1">
        <div class="h-full card">
            <div class="p-4 md:p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 text-sm font-normal text-gray-600 md:text-base" title="Number of Customers">Total Sales Today</h5>
                        <h3 class="my-3 text-xl md:my-6 md:text-2xl">Rp. {{ number_format($sale_today['price'], 0, ',', '.') }}</h3>
                        <p class="text-sm text-gray-400 truncate md:text-base">
                            <span class="bg-warning rounded-md text-xs md:text-md px-1 md:px-1.5 py-0.5 text-white me-1">{{ $sale_today['items'] }}</span>
                            <span>Product</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-span-1">
        <div class="h-full card">
            <div class="p-4 md:p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 text-sm font-normal text-gray-600 md:text-base" title="Number of Customers">Cost</h5>
                        <h3 class="my-3 text-xl md:my-6 md:text-2xl">Rp. {{ number_format($cost, 0, ',', '.') }}</h3>
                        <a href="{{ route('expense') }}" class="text-sm text-gray-400 truncate md:text-base">
                            <span class="bg-danger rounded-md text-xs md:text-md px-1 md:px-1.5 py-0.5 text-white me-1"></span>
                            <span>All Cost Today</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-span-1">
        <div class="h-full card">
            <div class="p-4 md:p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 text-sm font-normal text-gray-600 md:text-base" title="Number of Customers">Discount</h5>
                        <h3 class="my-3 text-xl md:my-6 md:text-2xl">Rp. {{ number_format($discount, 0, ',', '.') }}</h3>
                        <a href="{{ route('expense') }}" class="text-sm text-gray-400 truncate md:text-base">
                            <span class="bg-info rounded-md text-xs md:text-md px-1 md:px-1.5 py-0.5 text-white me-1"></span>
                            <span>All Discount Today</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-span-1">
        <div class="h-full card">
            <div class="p-4 md:p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 text-sm font-normal text-gray-600 md:text-base" title="Number of Customers">Profit</h5>
                        <h3 class="my-3 text-xl md:my-6 md:text-2xl">Rp. {{ number_format($profit, 0, ',', '.') }}</h3>
                        <a href="{{ route('expense') }}" class="text-sm text-gray-400 truncate md:text-base">
                            <span class="bg-success rounded-md text-xs md:text-md px-1 md:px-1.5 py-0.5 text-white me-1"></span>
                            <span>Profit Today</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
