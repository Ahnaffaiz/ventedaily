<div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2">
    <div class="overflow-auto rounded-lg card">
        <div>
            <div class="sticky top-0 z-10 flex items-center justify-between card-header dark:bg-gray-800">
                <h4 class="card-title">Sales Today</h4>
                <div class="w-32">
                    <x-input-select name="filter" title="" id="filter" :options="$filters"/>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800">
                <div class="grid grid-cols-5 px-6 py-3">
                    <div class="text-center">
                        <p class="text-sm text-gray-400">
                            <i class="ri-donut-chart-line"></i>
                            Total
                        </p>
                        <h3 class="font-medium text-md" id="total_sale_reseller">Rp. {{ number_format($total_sale['price'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-400">
                            <i class="ri-donut-chart-line"></i>
                            Reseller BTC
                        </p>
                        <h3 class="font-medium text-md" id="total_sale_reseller">Rp. {{ number_format($total_btc['price'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-400">
                            <i class="ri-donut-chart-line"></i>
                            Shopee
                        </p>
                        <h3 class="font-medium text-md" id="total_sale_reseller">Rp. {{ number_format($total_shopee['price'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-400">
                            <i class="ri-donut-chart-line"></i>
                            Tiktok
                        </p>
                        <h3 class="font-medium text-md" id="total_sale_reseller">Rp. {{ number_format($total_tiktok['price'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-400">
                            <i class="ri-donut-chart-line"></i>
                            Ventedaily
                        </p>
                        <h3 class="font-medium text-md" id="total_sale_reseller">Rp. {{ number_format($total_vente['price'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <canvas id="salesChart" class="w-full h-98"></canvas>
            </div>
        </div>
    </div>
    <div class="overflow-auto rounded-lg card">
        <div>
            <div class="sticky top-0 z-10 flex items-center justify-between card-header dark:bg-gray-800">
                <h4 class="card-title">Item Sales Today</h4>
                <div class="w-32">
                    <x-input-select name="filter" title="" id="filter" :options="$filters"/>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800">
                <div class="grid grid-cols-5 px-6 py-3">
                    <div class="text-center">
                        <p class="text-sm text-gray-400">
                            <i class="ri-donut-chart-line"></i>
                            Total
                        </p>
                        <h3 class="font-medium text-md" id="total_sale_reseller">{{ $total_sale['items'] }} Pcs</h3>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-400">
                            <i class="ri-donut-chart-line"></i>
                            Reseller BTC
                        </p>
                        <h3 class="font-medium text-md" id="total_sale_reseller">{{ $total_btc['items'] }} Pcs</h3>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-400">
                            <i class="ri-donut-chart-line"></i>
                            Shopee
                        </p>
                        <h3 class="font-medium text-md" id="total_sale_reseller">{{ $total_shopee['items'] }} Pcs</h3>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-400">
                            <i class="ri-donut-chart-line"></i>
                            Tiktok
                        </p>
                        <h3 class="font-medium text-md" id="total_sale_reseller">{{ $total_tiktok['items'] }} Pcs</h3>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-400">
                            <i class="ri-donut-chart-line"></i>
                            Ventedaily
                        </p>
                        <h3 class="font-medium text-md" id="total_sale_reseller">{{ $total_vente['items'] }} Pcs</h3>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <canvas id="salesItemChart" class="w-full h-98"></canvas>
            </div>
        </div>
    </div>
</div>
