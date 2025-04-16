<div>
    @include('livewire.dashboard.resume')
    @include('livewire.dashboard.chart-sales')
    <div class="grid grid-cols-1 gap-4">
        <div class="mt-6 overflow-auto rounded-lg card">
            <div>
                <div class="sticky top-0 z-10 flex items-center justify-between card-header dark:bg-gray-800">
                    <h4 class="card-title">Detail Cash</h4>
                </div>
                <table class="min-w-full divide-y dark:divide-gray-500">
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr class="dark:bg-gray-800">
                            <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-200 ps-6">
                                Cash
                            </td>
                            <td class="px-2 py-2 text-sm font-medium whitespace-nowrap dark:text-gray-200">
                                Rp. {{ number_format($total_cash, '0' , ',', '.') }}
                            </td>
                        </tr>
                        @foreach ($total_transfers as $totalTransfer)
                        <tr class="dark:bg-gray-800">
                            <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-200 ps-6">
                                {{ $totalTransfer['bank'] }}
                            </td>
                            <td class="px-2 py-2 text-sm font-medium whitespace-nowrap dark:text-gray-200">
                                Rp. {{ number_format($totalTransfer['total'], '0' , ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function formatLabel(label, filter) {
            const date = new Date(label);
            const options = {
                'week': { weekday: 'long' },
                'month': { day: 'numeric', month: 'short' , year: 'numeric'},
                'year': { year: 'numeric' }
            };

            // Untuk label seperti '2025' langsung return
            if (filter === 'year' && label.length === 4) return label;

            return date.toLocaleDateString('id-ID', options[filter]);
        }

    </script>
@endpush
@script
<script>
    let salesChart;
    let salesItemChart;

    Livewire.on('update-chart-sales', ([payload]) => {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const ctr = document.getElementById('salesItemChart').getContext('2d');

        if (salesChart) {
            salesChart.destroy();
        }
        if (salesItemChart) {
            salesItemChart.destroy();
        }
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: payload.salesLabel.map(label => formatLabel(label, payload.filter)),
                datasets: [{
                    label: 'Total Sales',
                    data: payload.salesData,
                    backgroundColor: 'rgba(244, 63, 94, 0.7)',
                    borderColor: 'rgba(244, 63, 94, 0.7)',
                    borderWidth: 1
                    },
                    {
                        label: 'reseller Sales',
                        data: payload.resellerData,
                        backgroundColor: 'rgba(22 167 233)',
                        borderColor: 'rgba(22 167 233)',
                        borderWidth: 1
                    },
                    {
                        label: 'Shopee',
                        data: payload.shopeeData,
                        backgroundColor: 'rgba(255, 168, 0, 0.7)',
                        borderColor: 'rgba(255, 168, 0, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'WhatsApp',
                        data: payload.venteData,
                        backgroundColor: 'rgba(37, 211, 102, 0.7)',
                        borderColor: 'rgba(37, 211, 102, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'TikTok',
                        data: payload.tiktokData,
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        borderColor: 'rgba(0, 0, 0, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        salesItemChart = new Chart(ctr, {
            type: 'line',
            data: {
                labels: payload.salesLabel.map(label => formatLabel(label, payload.filter)),
                datasets: [{
                    label: 'Total Sales',
                    data: payload.salesDataItem,
                    backgroundColor: 'rgba(244, 63, 94, 0.7)',
                    borderColor: 'rgba(244, 63, 94, 0.7)',
                    borderWidth: 1
                    },
                    {
                        label: 'reseller Sales',
                        data: payload.resellerDataItem,
                        backgroundColor: 'rgba(22 167 233)',
                        borderColor: 'rgba(22 167 233)',
                        borderWidth: 1
                    },
                    {
                        label: 'Shopee',
                        data: payload.shopeeDataItem,
                        backgroundColor: 'rgba(255, 168, 0, 0.7)',
                        borderColor: 'rgba(255, 168, 0, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'WhatsApp',
                        data: payload.venteDataItem,
                        backgroundColor: 'rgba(37, 211, 102, 0.7)',
                        borderColor: 'rgba(37, 211, 102, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'TikTok',
                        data: payload.tiktokDataItem,
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        borderColor: 'rgba(0, 0, 0, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

    });
</script>
@endscript
