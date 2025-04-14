@extends('layouts.print')
@section('title', 'Stock History')
@section('content')
    <div>
        <div class="text-center">
            <h1 class="text-2xl">Stock History Ventedaily</h1>
            @if ($stockHistories)
                <h2 class="text-xl">{{ ucwords($productStock->product->name . ' Warna ' .$productStock->color->name . ' Ukuran ' . $productStock->size->name) }}</h2>
            @endif
        </div>
        <div class="text-start">
            <h2 class="text-lg font-normal text-normal">Periode : </h2>
            <h2 class="text-lg font-normal text-normal">{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} sd {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</h2>
        </div>
        <div class="mt-6 overflow-x-auto bg-white border border-gray-200 dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                @if ($stockHistories->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-center text-gray-800 dark:text-gray-200">No</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200 text-start">User</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200 text-start">Date</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200 text-start">Reference</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200 text-start">Activity</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200 text-start">Status</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200 text-start">From Stock</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200 text-start">To Stock</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200">Qty</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200">All Stock</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200">Home</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200">Store</th>
                            <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200">Pre Order</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($stockHistories as $stockHistory)
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}
                                {{ $stockHistory['activity'] == 'import' ? 'font-bold' : ''}}">
                                <th class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ $loop->iteration}}
                                </th>
                                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ $stockHistory?->user?->name }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ \Carbon\Carbon::parse($stockHistory['created_at'])->format('H:i d/m/Y') }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ $stockHistory['reference'] }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ ucwords($stockHistory['stock_activity']) }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ ucwords($stockHistory['status']) }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ ucwords(str_replace('_', ' ', $stockHistory['from_stock_type'])) }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ ucwords(str_replace('_', ' ', $stockHistory['to_stock_type'])) }}
                                </td>
                                <td class="px-4 py-2 text-sm text-center text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ $stockHistory['qty'] }}
                                </td>
                                <td class="px-4 py-2 text-sm text-center text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ $stockHistory['final_all_stock'] }}
                                </td>
                                <td class="px-4 py-2 text-sm text-center text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ $stockHistory['final_home_stock'] }}
                                </td>
                                <td class="px-4 py-2 text-sm text-center text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ $stockHistory['final_store_stock'] }}
                                </td>
                                <td class="px-4 py-2 text-sm text-center text-gray-700 whitespace-nowrap dark:text-gray-200">
                                    {{ $stockHistory['final_pre_order_stock'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <div class="py-5 text-center">
                        <i class="text-4xl ri-file-warning-line"></i>
                        <p class="my-5 text-base">No Stock History Found</p>
                    </div>
                @endif
            </table>
        </div>
    </div>
@endsection
@push('scripts')
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
            size: A4 landscape;
            margin: 1cm;
        }

        body {
            zoom: 80%;
        }
    }
</style>
@endpush
