<div class="grid gap-6 mb-6 2xl:grid-cols-4 lg:grid-cols-4 md:grid-cols-2 sm:grid-cols">
    <div class="2xl:col-span-1 lg:col-span-2">
        <div class="card">
            <div class="p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 text-base font-normal text-gray-600" title="Number of Customers">Total Keeps Today</h5>
                        <h3 class="my-6 text-2xl">Rp. {{ number_format($keep_prices, 0, ',', '.') }}</h3>
                        <p class="text-gray-400 truncate">
                            <span class="bg-primary rounded-md text-md px-1.5 py-0.5 text-white me-1">{{ $keep_items }}</span>
                            <span>Product</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="2xl:col-span-1 lg:col-span-2">
        <div class="card">
            <div class="p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 text-base font-normal text-gray-600" title="Number of Customers">Total Sales Today</h5>
                        <h3 class="my-6 text-2xl">Rp. {{ number_format($sale_total_prices, 0, ',', '.') }}</h3>
                        <p class="text-gray-400 truncate">
                            <span class="bg-warning rounded-md text-md px-1.5 py-0.5 text-white me-1">{{ $sale_total_items }}</span>
                            <span>Product</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="2xl:col-span-1 lg:col-span-2">
        <div class="card">
            <div class="p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 text-base font-normal text-gray-600" title="Number of Customers">Cost</h5>
                        <h3 class="my-6 text-2xl">Rp. {{ number_format($cost, 0, ',', '.') }}</h3>
                        <a href="{{ route('expense') }}" class="text-gray-400 truncate">
                            <span class="bg-danger rounded-md text-md px-1.5 py-0.5 text-white me-1"></span>
                            <span>All Cost Today</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="2xl:col-span-1 lg:col-span-2">
        <div class="card">
            <div class="p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 text-base font-normal text-gray-600" title="Number of Customers">Discount</h5>
                        <h3 class="my-6 text-2xl">Rp. {{ number_format($discount, 0, ',', '.') }}</h3>
                        <a href="{{ route('expense') }}" class="text-gray-400 truncate">
                            <span class="bg-info rounded-md text-md px-1.5 py-0.5 text-white me-1"></span>
                            <span>All Discount Today</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="2xl:col-span-1 lg:col-span-2">
        <div class="card">
            <div class="p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 text-base font-normal text-gray-600" title="Number of Customers">Reseller Keeps</h5>
                        <h3 class="my-6 text-2xl">Rp. {{ number_format($reseller_keep_price, 0, ',', '.') }}</h3>
                        <p class="text-gray-400 truncate">
                            <span class="bg-info rounded-md text-md px-1.5 py-0.5 text-white me-1">{{ $reseller_keep_products }}</span>
                            <span>Product</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>
