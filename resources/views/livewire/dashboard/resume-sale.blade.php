<div>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
            <div class="flex items-center justify-between p-4">
                <h1 class="text-base text-bold">Top 20 Product</h1>
            </div>
            <div class="items-center p-4 mb-4">
                <canvas id="SaleProductChart" class="w-full h-full"></canvas>
                <span class="mt-20">Top Penjualan Produk Today</span>
            </div>
        </div>
        <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg lg:col-span-2">
            <div class="flex items-center justify-between p-4">
                <h1 class="text-base text-bold">Resume Sale</h1>
                <div class="w-32">
                    <x-input-select id="group_id" title="" name="group_id" placeholder="All Group " :options="$groups"/>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" wire.poll.5s>
                    @if ($saleItems->count() > 0)
                        <thead>
                            <tr>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Name</th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Color</th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Size</th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Items</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($saleItems as $saleItem)
                                <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                    <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{($saleItems->currentpage() - 1) * $saleItems->perpage() + $loop->index + 1}}
                                    </th>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $saleItem->name }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $saleItem->color }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $saleItem->size }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $saleItem->items }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @else
                        <div class="text-center">
                            <i class="text-4xl ri-file-warning-line"></i>
                            <p class="my-5 text-base">No Sale Found</p>
                        </div>
                    @endif
                </table>
            </div>

            <div class="px-3 py-4">
                <div class="flex justify-between">
                    <div class="flex flex-col">
                        <div class="flex items-center">
                            <label class="w-32 text-sm font-medium text-gray-900">Per Page</label>
                            <select
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                wire:model.change="perPage">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            Showing {{ $saleItems->firstItem() ?? 0 }} to {{ $saleItems->lastItem() ?? 0 }} of {{ $saleItems->total() }} entries
                        </div>
                    </div>
                    <x-pagination :paginator="$saleItems" pageName="resumeSale" />
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
    let SaleProductChart;

    Livewire.on('update-chart-product', ([payload]) => {
        const productColors = payload.productData.map(() => getRandomColor());

        const ctx = document.getElementById('SaleProductChart').getContext('2d');

        if (SaleProductChart) {
            SaleProductChart.data.labels = payload.productLabel;
            SaleProductChart.data.datasets[0].data = payload.productData;
            SaleProductChart.data.datasets[0].backgroundColor = productColors;
            SaleProductChart.update();
        } else {
            SaleProductChart = new Chart(ctx, {
                type: 'polarArea',
                data: {
                    labels: payload.productLabel,
                    datasets: [{
                        label: 'Sale Product',
                        data: payload.productData,
                        backgroundColor: productColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }
    });

    function getRandomColor() {
        const r = Math.floor(Math.random() * 256);
        const g = Math.floor(Math.random() * 256);
        const b = Math.floor(Math.random() * 256);
        return `rgba(${r}, ${g}, ${b}, 0.6)`;
    }
</script>

@endscript
