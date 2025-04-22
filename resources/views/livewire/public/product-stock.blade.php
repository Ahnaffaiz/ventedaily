<div class="mt-6 overflow-x-auto bg-white border border-gray-200 rounded-md shadow-md dark:bg-gray-800 sm:rounded-lg">
    <div class="flex items-center justify-between p-4">
        <div class="flex">
            <h4 class="text-xl card-title">Daftar Product</h4>
        </div>
        <div class="flex items-center justify-between">
            <div class="relative ms-auto">
                <input type="search" class="relative border-none form-input bg-black/5 ps-8" wire:model.live="query"
                    placeholder="Search...">
                <span class="absolute z-10 text-base -translate-y-1/2 ri-search-line start-2 top-1/2"></span>
            </div>
        </div>
    </div>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        @if ($productStocks?->count() > 0)
            <thead>
                <tr>
                    <th scope="col" class="px-4 py-2 text-sm font-bold text-center text-gray-800 dark:text-gray-200">No</th>
                    <th scope="col" class="px-4 py-2 text-sm font-bold text-gray-800 dark:text-gray-200 text-start">Nama</th>
                    <th scope="col" class="px-4 py-2 text-sm font-bold text-center text-gray-800 dark:text-gray-200">Stock</th>
                    <th scope="col" class="px-4 py-2 text-sm font-bold text-center text-gray-800 dark:text-gray-200">Harga Jual</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($productStocks as $productStock)
                    <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                        <th class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                            {{($productStocks->currentpage() - 1) * $productStocks->perpage() + $loop->index + 1}}
                        </th>
                        <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                            {{ ucwords($productStock->product->name) }} {{ ucwords($productStock->color->name) }} {{ strtoupper($productStock->size->name) }}
                        </td>
                        <td class="px-4 py-2 text-sm text-center text-gray-700 whitespace-nowrap dark:text-gray-200">
                            @if ($productStock->all_stock > 10)
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-primary text-white">Aman</span>
                            @elseif ($productStock->all_stock <= 10 && $productStock->all_stock > 0)
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-warning text-white">Ready</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-danger text-white">Habis</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-center text-gray-700 whitespace-nowrap dark:text-gray-200">
                            Rp. {{ number_format($productStock->selling_price, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @else
            <div class="text-center">
                <i class="text-4xl ri-file-warning-line"></i>
                <p class="my-5 text-base">No Product Found</p>
            </div>
        @endif
    </table>
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
            <x-pagination :paginator="$productStocks" pageName="page" />
        </div>
</div>
