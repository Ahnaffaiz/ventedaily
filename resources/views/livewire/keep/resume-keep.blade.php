<div>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
            <div class="flex items-center justify-between p-4">
                <h1 class="text-base text-bold">Keep Statistic</h1>
            </div>
            <div class="p-4 mb-4">
                <canvas id="keepProductChart" class="w-full h-full"></canvas>
                <span>Penjualan Produk Per Kategori</span>
            </div>
        </div>
        <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg lg:col-span-2">
            <div class="flex items-center justify-between p-4">
                <h1 class="text-base text-bold">Resume Keep</h1>
                <div class="w-32">
                    <x-input-select id="group_id" title="" name="group_id" placeholder="All Group " :options="$groups"/>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" wire.poll.5s>
                    @if ($keepProducts->count() > 0)
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
                            @foreach ($keepProducts as $keepProduct)
                                <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                    <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{($keepProducts->currentpage() - 1) * $keepProducts->perpage() + $loop->index + 1}}
                                    </th>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $keepProduct->name }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $keepProduct->color }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $keepProduct->size }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $keepProduct->items }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @else
                        <div class="text-center">
                            <i class="text-4xl ri-file-warning-line"></i>
                            <p class="my-5 text-base">No Keep Found</p>
                        </div>
                    @endif
                </table>
            </div>

            <div class="px-3 py-4">
                <div class="flex justify-between">
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
                    {{ $keepProducts->links(data: ['scrollTo' => false]) }}
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

        function addData(chart, label, newData) {
            chart.data.labels.push(label);
            chart.data.datasets.forEach((dataset) => {
                dataset.data.push(newData);
            });
            chart.update();
        }

        function removeData(chart) {
            chart.data.labels.pop();
            chart.data.datasets.forEach((dataset) => {
                dataset.data.pop();
            });
            chart.update();
        }

    </script>
    <script>

    </script>
@endpush

@script
<script>
    const labels = {!! json_encode($productLabel) !!}
    const datas = {!! json_encode($productData) !!}
    const productColors = datas.map(() => getRandomColor());

    const ctr = document.getElementById('keepProductChart');
    new Chart(ctr, {
        type: 'polarArea',
        data: {
            labels: labels,
            datasets: [{
                label: 'Produk Keep',
                data: datas,
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
</script>
<script>
    Livewire.on('update-chart-product', ([payload]) => {
        console.log(payload.productData);
        console.log('heloo')
        const updatedColors = updatedDatas.map(() => getRandomColor());
        console.log(updatedLabels);

        removeData(ctr);
        addData(ctr, updatedLabels, updatedDatas, updatedColors);
    })
</script>
@endscript
