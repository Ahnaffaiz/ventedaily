@extends('layouts.print')
@section('title', 'Stock History')
@section('content')
<div class="relative overflow-hidden bg-white {{ !$salesCategories ? 'hidden' : 'block' }} w-full" id="print-report">
    @if ($salesCategories)
        <div class="flex justify-between border-b">
            <div class="">
                <h1 class="text-2xl font-bold text-gray-900">{{ $setting->name }}</h1>
                <h1 class="text-base font-normal text-gray-900">{{ $setting->address }}</h1>
            </div>
            <div class="">
                <p class="text-base font-normal text-gray-800">Monthly Report</p>
                <h1 class="text-base font-bold text-gray-900">{{ \Carbon\Carbon::parse($monthYear)->format('M Y') }}</h1>
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
            <div class="mb-4">
                <canvas id="categorySoldChart" class="w-full h-full"></canvas>
                <span>Penjualan Produk Per Kategori</span>
            </div>
            <div class="mb-4">
                <canvas id="productSoldChart" class="w-full h-full"></canvas>
                <span>Penjualan Produk</span>
            </div>
        </div>

    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function getRandomColor() {
        const r = Math.floor(Math.random() * 256);
        const g = Math.floor(Math.random() * 256);
        const b = Math.floor(Math.random() * 256);
        return `rgb(${r}, ${g}, ${b})`;
    }
    const categoryLabels = @json($salesCategories->pluck('name')->values()->all());
    const categoryData = @json($salesCategories->pluck('total_items')->values()->all());
    const productLabels = @json($salesProducts->pluck('name')->values()->all());
    const productData = @json($salesProducts->pluck('total_items')->values()->all());
    const categoryColors = categoryData.map(() => getRandomColor());
    const productColors = productData.map(() => getRandomColor());

    const ctx = document.getElementById('categorySoldChart');
    new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Penjualan Per Kategori Produk',
                data: categoryData,
                backgroundColor: categoryColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctr = document.getElementById('productSoldChart');
    new Chart(ctr, {
        type: 'polarArea',
        data: {
            labels: productLabels,
            datasets: [{
                label: 'Penjualan Per Kategori Produk',
                data: productData,
                backgroundColor: productColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<script>
    window.onload = function () {
        window.print();
    }
</script>
@endpush

@push('styles')
<style>
    @media print {
        @page {
            size: A4 vertical;
            margin: 1cm;
        }

        body {
            zoom: 100%;
        }
    }
</style>
@endpush
