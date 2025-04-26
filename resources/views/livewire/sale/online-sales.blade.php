<div>
    <x-modal wire:model="isOpen" title="Detail Sale" closeButton="closeModal" large="true">
        @include('livewire.sale.detail-sale')
    </x-modal>
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
            </div>
            <div class="flex justify-end mb-4">
                <div class="relative mr-4 ms-auto">
                    <input type="search" class="relative border-none form-input bg-black/5 ps-8" wire:model.live="query"
                        placeholder="Masukkan No Sale">
                    <span class="absolute z-10 text-base -translate-y-1/2 ri-search-line start-2 top-1/2"></span>
                </div>
                <div class="relative ms-auto">
                    <button data-fc-type="dropdown" data-fc-placement="bottom-end" type="button"
                        class="flex items-center py-2 pl-3 pr-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-500 dark:bg-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        id="menu-button" aria-expanded="true" aria-haspopup="true">
                        <i class="mr-2 ri-filter-line"></i>
                    </button>
                    <div class="absolute right-0 z-10 hidden w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                        onclick="event.stopPropagation()">
                        <div class="max-h-[300px] h-56 overflow-auto mt-2">
                            <div class="py-1" role="none">
                                <span class="m-2">Column</span>
                                @foreach ($showColumns as $column => $isVisible)
                                    <div class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-900 hover:text-gray-900"
                                        role="menuitem" tabindex="-1" id="menu-item-0">
                                        <input type="checkbox" class="w-4 h-4 text-indigo-600 form-checkbox"
                                            wire:model.live="showColumns.{{ $column }}">
                                        <label class="block ml-3 text-sm font-medium text-gray-700" for="comments">
                                            {{ ucfirst(str_replace('_', ' ', $column)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                @if ($onlineSales->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                            <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                wire:click="sortByColumn('no_sale')">
                                No Sale
                                @if ($sortBy === 'no_sale')
                                    @if ($sortDirection === 'asc')
                                        <i class="ri-arrow-up-s-line"></i>
                                    @else
                                        <i class="ri-arrow-down-s-line"></i>
                                    @endif
                                @else
                                    <i class="ri-expand-up-down-line"></i>
                                @endif
                            </th>
                            <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                wire:click="sortByColumn('customer_name')">
                                Customer
                                @if ($sortBy === 'customer_name')
                                    @if ($sortDirection === 'asc')
                                        <i class="ri-arrow-up-s-line"></i>
                                    @else
                                        <i class="ri-arrow-down-s-line"></i>
                                    @endif
                                @else
                                    <i class="ri-expand-up-down-line"></i>
                                @endif
                            </th>
                            @if ($showColumns['marketplace'])
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('marketplace_name')">
                                    Marketplace
                                    @if ($sortBy === 'marketplace_name')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['order_id_marketplace'])
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start">
                                    Order ID
                                </th>
                            @endif
                            @if ($showColumns['total_items'])
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('total_items')">
                                    Total Items
                                    @if ($sortBy === 'total_items')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['total_price'])
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('total_price')">
                                    Total Sale
                                    @if ($sortBy === 'total_price')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['ship_status'])
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('ship_status')">
                                    Ship Status
                                    @if ($sortBy === 'ship_status')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['ship_cost'])
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('ship_cost')">
                                    Ship Cost
                                    @if ($sortBy === 'ship_cost')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['withdrawal_status'])
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('withdrawal_status')">
                                    WD Status
                                    @if ($sortBy === 'withdrawal_status')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['withdrawal_amount'])
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('withdrawal_amount')">
                                    WD Amount
                                    @if ($sortBy === 'withdrawal_amount')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['created_at'])
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('created_at')">
                                    Created at
                                    @if ($sortBy === 'created_at')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['updated_at'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('updated_at')">
                                    Updated at
                                    @if ($sortBy === 'updated_at')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            <th scope="col" class="justify-end w-1/12 px-4 py-4 pr-3 text-sm font-medium text-gray-500">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($onlineSales as $sale)
                            <tr>
                                <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{($onlineSales->currentpage() - 1) * $onlineSales->perpage() + $loop->index + 1}}
                                </th>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $sale->no_sale }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $sale->customer_name }}
                                </td>
                                @if ($showColumns['marketplace'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $sale->marketplace_name }}
                                    </td>
                                @endif
                                @if ($showColumns['order_id_marketplace'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200 text-start">
                                        {{ $sale->order_id }}
                                    </td>
                                @endif
                                @if ($showColumns['total_items'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200 text-start">
                                        {{ $sale->total_items }}
                                    </td>
                                @endif
                                @if ($showColumns['total_price'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200 text-start">
                                        Rp. {{ number_format($sale->total_price, 0, ',', '.') }}
                                    </td>
                                @endif
                                @if ($showColumns['ship_status'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        @if ($sale->ship_status)
                                            @if ($sale->ship_status == strtolower(App\Enums\ShippingStatus::SIAPKIRIM))
                                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-info/10 text-info">
                                                    {{ ucfirst($sale->ship_status) }}
                                                </span>
                                            @elseif ($sale->ship_status == strtolower(App\Enums\ShippingStatus::EKSPEDISI))
                                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-warning/10 text-warning">
                                                    {{ ucfirst($sale->ship_status) }}
                                                </span>
                                            @elseif ($sale->ship_status == strtolower(App\Enums\ShippingStatus::SELESAI))
                                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-success/10 text-success">
                                                    {{ ucfirst($sale->ship_status) }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-secondary/10 text-secondary">
                                                Cashier
                                            </span>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['ship_cost'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        Rp {{ number_format($sale->ship_cost ? $sale->ship_cost : 0, '0', ',', '.') }}
                                    </td>
                                @endif
                                @if ($showColumns['withdrawal_status'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        @if ($sale->withdrawal_amount != null)
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-success/10 text-success">
                                                Cair
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-secondary/10 text-secondary">
                                                Belum Cair
                                            </span>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['withdrawal_amount'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        Rp {{ number_format($sale->withdrawal_amount ?  $sale->withdrawal_amount : 0, '0', ',', '.') }}
                                    </td>
                                @endif
                                @if ($showColumns['created_at'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $sale->created_at->format('d/m/Y') }}
                                    </td>
                                @endif
                                @if ($showColumns['updated_at'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $sale->updated_at->format('d/m/Y') }}
                                    </td>
                                @endif
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center pr-4 space-x-3">
                                        <button wire:click="show({{ $sale->id }})" class="text-primary">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button wire:click="toggleRow({{ $sale->id }})" type="button"
                                            class="inline-flex transition-all duration-300">
                                            <i class="text-xl transition-all text-warning ri-arrow-down-s-line
                                                {{ in_array($sale->id, $openRows) ? 'rotate-180' : '' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @if (in_array($sale->id, $openRows))
                                <tr class="w-full overflow-hidden transition-[height] duration-300">
                                    <td colspan="12" class="py-2">
                                        <table class="min-w-full divide-gray-200 divide-b dark:divide-gray-700">
                                            <tbody>
                                                <tr>
                                                    <td class="w-1/12 px-4"></td>
                                                    <th colspan="2" class="w-3/12 px-4 text-sm text-start">Name</th>
                                                    <th class="w-1/12 px-4 text-start">Price</th>
                                                    <th class="w-1/12 px-4 text-start">Total Items</th>
                                                    <th class="w-1/12 px-4 text-start">Total Price</th>
                                                    <td colspan="5" class="w-5/12 px-4"></td>
                                                </tr>
                                                @foreach ($sale->saleItems as $saleItem)
                                                <tr class="bg-gray-100 border-gray-200 dark:bg-gray-900 border-y dark:border-gray-700">
                                                    <td class="w-1/12 px-4"></td>
                                                    <td colspan="2" class="w-3/12 px-4 py-2 text-sm text-gray-800 text-start whitespace-nowrap dark:text-gray-200">{{ ucwords($saleItem->productStock->product->name) }} {{ ucwords($saleItem->productStock->color->name) }} {{ ucwords($saleItem->productStock->size->name) }}</td>
                                                    <td class="w-1/12 px-4 py-2 text-sm text-gray-800 text-start whitespace-nowrap dark:text-gray-200">
                                                        Rp. {{ number_format($saleItem->price, 0, ',', '.') }}
                                                    </td>
                                                    <td class="w-1/12 px-4 py-2 text-sm text-gray-800 text-start whitespace-nowrap dark:text-gray-200">
                                                        {{ $saleItem->total_items }}
                                                    </td>
                                                    <td class="w-1/12 px-4 py-2 text-sm text-gray-800 text-start whitespace-nowrap dark:text-gray-200">
                                                        Rp. {{ number_format($saleItem->total_price, 0, ',', '.') }}
                                                    </td>
                                                    <td colspan="5" class="w-5/12"></td>
                                                    @if ($showColumns['created_at'])
                                                        <td class="px-4 py-2 text-sm text-gray-800 text-start whitespace-nowrap dark:text-gray-200"></td>
                                                    @endif
                                                    @if ($showColumns['updated_at'])
                                                        <td class="px-4 py-2 text-sm text-gray-800 text-start whitespace-nowrap dark:text-gray-200"></td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
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
            <div class="flex flex-col items-center md:flex-row md:justify-between gap-4">
                <div class="flex flex-col items-center md:items-start">
                    <div class="mt-2 text-sm text-center md:text-left text-gray-600">
                        Showing {{ $onlineSales->firstItem() ?? 0 }} to {{ $onlineSales->lastItem() ?? 0 }} of {{ $onlineSales->total() }} entries
                    </div>
                </div>
                <div class="mt-2 md:mt-0">
                    <x-pagination :paginator="$onlineSales" pageName="listOnlineSales" />
                </div>
            </div>
        </div>
    </div>
</div>
