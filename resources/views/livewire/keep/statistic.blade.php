<div class="grid gap-6 mb-6 2xl:grid-cols-3 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols">
    <div class="2xl:col-span-1 lg:col-span-2">
        <div class="card">
            <div class="p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 font-normal text-gray-400 text-base/3" title="Number of Customers">All Keeps</h5>
                        <h3 class="my-6 text-2xl">{{ $keeps?->where('status', App\Enums\KeepStatus::ACTIVE)->count() }}</h3>
                        <p class="text-gray-400 truncate">
                            <span class="bg-success rounded-md text-xs px-1.5 py-0.5 text-white me-1"><i class="ri-arrow-up-line"></i> 2,541</span>
                            <span>Since last month</span>
                        </p>
                    </div>
                    <div class="shrink">
                        <div id="widget-customers" class="apex-charts" data-colors="#47ad77,#e3e9ee"></div>
                    </div>
                </div>
            </div> <!-- end p-6-->
        </div> <!-- end card-->
    </div>

    <div class="2xl:col-span-1 lg:col-span-2">
        <div class="card">
            <div class="p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 font-normal text-gray-400 text-base/3" title="Number of Orders">Online Keeps</h5>
                        <h3 class="my-6 text-2xl">{{ $keeps?->where('status', App\Enums\KeepStatus::ACTIVE)->count() }}</h3>
                        <p class="text-gray-400 truncate">
                            <span class="bg-danger rounded-md text-xs px-1.5 py-0.5 text-white me-1"><i class="ri-arrow-down-line"></i> 1.08%</span>
                            <span>Since last month</span>
                        </p>
                    </div>
                    <div id="widget-orders" class="apex-charts" data-colors="#3e60d5,#e3e9ee"></div>
                </div>
            </div> <!-- end p-6-->
        </div> <!-- end card-->
    </div>

    <div class="2xl:col-span-1 lg:col-span-2">
        <div class="card">
            <div class="p-6">
                <div class="flex justify-between">
                    <div class="overflow-hidden grow">
                        <h5 class="mt-0 font-normal text-gray-400 text-base/3" title="Average Revenue">Revenue</h5>
                        <h3 class="my-6 text-2xl">$9,254</h3>
                        <p class="text-gray-400 truncate">
                            <span class="bg-danger rounded-md text-xs px-1.5 py-0.5 text-white me-1"><i class="ri-arrow-down-line"></i> 7.00%</span>
                            <span>Since last month</span>
                        </p>
                    </div>
                    <div id="widget-revenue" class="apex-charts" data-colors="#16a7e9,#e3e9ee"></div>
                </div>

            </div> <!-- end p-6-->
        </div> <!-- end card-->
    </div>
</div>
