<div>
    <div class="relative p-4 mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex-mt-4">
            <x-input-text id="month" name="month" type="month" title="Month"></x-input-text>
        </div>
        <div class="flex items-center justify-end gap-2 mt-4">
            <button wire:click="generateReport" class="inline gap-2 text-white transition-all btn bg-primary" wire:target="generateReport" wire:loading.attr="disabled">
                <div class="flex gap-2" wire:loading.remove wire:target="generateReport">
                    <i class="ri-file-chart-line"></i>
                    Generate Report
                </div>
                <div class="flex gap-2" wire:loading wire:target="generateReport">
                    <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-light rounded-full"></div>
                </div>
            </button>
        </div>
    </div>
    <div class="relative p-4 mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg {{ !$salesCategories ? 'hidden' : 'block' }} w-full" id="print-report">
        @if ($salesCategories)
            <div class="flex justify-between border-b">
                <div class="">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $setting->name }}</h1>
                    <h1 class="text-base font-normal text-gray-900">{{ $setting->address }}</h1>
                    <a type="button" class="inline my-2 text-white btn bg-danger gaps-2" type="button" target="_blank" href="{{ route('monthly-report-print', $month) }}">
                        <i class="ri-file-download-line"></i>
                        Cetak
                    </a>
                </div>
                <div class="">
                    <p class="text-base font-normal text-gray-800">Monthly Report</p>
                    <h1 class="text-base font-bold text-gray-900">{{ \Carbon\Carbon::parse($month)->format('M Y') }}</h1>
                </div>
            </div>
            <div class="mt-4">
                <p class="mb-2 text-base font-normal text-gray-800">Resume</p>
                <div class="grid gap-6 md:grid-cols-2">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr class="bg-gray-100 dark:bg-gray-900">
                                <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-200">
                                    Omzet Penjualan
                                </td>
                                <td class="px-2 py-2 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-200">
                                    Rp. {{ number_format($report['omzet'], '0' , ',', '.') }}
                                </td>
                            </tr>
                            <tr class="bg-gray-100 dark:bg-gray-900">
                                <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-200">
                                    HPP Barang
                                </td>
                                <td class="px-2 py-2 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-200">
                                    Rp. {{ number_format($report['omzet'] - $report['net_profit'], '0' , ',', '.') }}
                                </td>
                            </tr>
                            <tr class="bg-gray-100 dark:bg-gray-900">
                                <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-200">
                                    Profit
                                </td>
                                <td class="px-2 py-2 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-200">
                                    Rp. {{ number_format($report['net_profit'], '0' , ',', '.') }}
                                </td>
                            </tr>
                            <tr class="bg-gray-100 dark:bg-gray-900">
                                <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-200">
                                    Beban Bulanan
                                </td>
                                <td class="px-2 py-2 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-200">
                                    Rp. {{ number_format($report['monthly_cost'], '0' , ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-900">
                                <th scope="col" class="px-2 py-2 text-sm font-medium text-gray-500 border text-start">Category</th>
                                <th scope="col" class="w-40 px-2 py-2 text-sm font-medium text-center text-gray-500 border">Pcs</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr class="bg-gray-100 dark:bg-gray-900">
                                <td class="px-2 py-2 text-sm text-gray-500 border dark:text-gray-200">
                                    Jumlah Penjualan (Pcs)
                                </td>
                                <td class="px-2 py-2 text-sm font-medium text-center text-gray-900 border whitespace-nowrap dark:text-gray-200">
                                    {{ $report['total_sales'] }}
                                </td>
                            </tr>
                            <tr class="bg-gray-100 dark:bg-gray-900">
                                <td class="px-2 py-2 text-sm text-gray-500 border dark:text-gray-200">
                                    Reseller
                                </td>
                                <td class="px-2 py-2 text-sm font-medium text-center text-gray-900 border whitespace-nowrap dark:text-gray-200">
                                    {{ $report['total_sales_reseller'] }}
                                </td>
                            </tr>
                            <tr class="bg-gray-100 dark:bg-gray-900">
                                <td class="px-2 py-2 text-sm text-gray-500 border dark:text-gray-200">
                                    Shopee
                                </td>
                                <td class="px-2 py-2 text-sm font-medium text-center text-gray-900 border whitespace-nowrap dark:text-gray-200">
                                    {{ $report['total_sales_shopee'] }}
                                </td>
                            </tr>
                            <tr class="bg-gray-100 dark:bg-gray-900">
                                <td class="px-2 py-2 text-sm text-gray-500 border dark:text-gray-200">
                                    Tiktok
                                </td>
                                <td class="px-2 py-2 text-sm font-medium text-center text-gray-900 border whitespace-nowrap dark:text-gray-200">
                                    {{ $report['total_sales_tiktok'] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                <p class="mb-2 text-base font-normal text-gray-800">Detail Kategori Produk</p>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th scope="col" class="w-20 px-2 py-2 text-sm font-medium text-center text-gray-500 border">No</th>
                            <th scope="col" class="px-2 py-2 text-sm font-medium text-gray-500 border text-start">Category</th>
                            <th scope="col" class="w-40 px-2 py-2 text-sm font-medium text-center text-gray-500 border">Pcs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($salesCategories as $category)
                            <tr class="border-x {{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                <td class="w-20 px-2 py-2 text-sm text-center text-gray-900 border whitespace-nowrap dark:text-gray-200">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-2 py-2 text-sm text-gray-900 border whitespace-nowrap dark:text-gray-200">
                                    {{ $category->name }}
                                </td>
                                <td class="w-40 px-2 py-2 text-sm text-center text-gray-900 border whitespace-nowrap dark:text-gray-200">
                                    {{ $category->total_items }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div class="mt-4">
            <p class="mb-2 text-base font-normal text-gray-800">Detail Kategori Produk</p>
            <div class="grid gap-6 md:grid-cols-2">
                <div class="mb-4 h-96">
                    <canvas id="categorySoldChart" class="w-full h-full"></canvas>
                    <span>Penjualan Produk Per Kategori</span>
                </div>
                <div class="mb-4 h-96">
                    <canvas id="productSoldChart" class="w-full h-full"></canvas>
                    <span>Penjualan Produk</span>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function getRandomColor() {
            const r = Math.floor(Math.random() * 256);
            const g = Math.floor(Math.random() * 256);
            const b = Math.floor(Math.random() * 256);
            return `rgb(${r}, ${g}, ${b})`;
        }
    </script>
@endpush
@script
<script>

    Livewire.on('categories-sold-loaded', ([payload]) => {
        const categoryColors = payload.categoryData.map(() => getRandomColor());
        const ctx = document.getElementById('categorySoldChart');

        new Chart(ctx, {
            type: 'polarArea',
            data: {
                labels: payload.categoryLabels,
                datasets: [{
                    label: 'Penjualan Per Kategori Produk',
                    data: payload.categoryData,
                    backgroundColor: categoryColors,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // product chart
        const productColors = payload.productData.map(() => getRandomColor());

        const ctr = document.getElementById('productSoldChart');
        new Chart(ctr, {
            type: 'polarArea',
            data: {
                labels: payload.productLabels,
                datasets: [{
                    label: 'Penjualan Per Kategori Produk',
                    data: payload.productData,
                    backgroundColor: productColors,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endscript
