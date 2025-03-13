<div>
    <x-modal wire:model="isOpen" title="{{ $withdrawal ? 'Edit ' . $withdrawal?->sale->no_sale : 'Create Withdrawal' }}"
        saveButton="{{ $withdrawal ? 'update' : 'save' }}" closeButton="closeModal">
        <form>
            @if (!$withdrawal)
                <x-input-select-search id="sale_id" name="sale_id" title="No Sale" placeholder="Type No Sale"
                :options="$sales" searchFunction="searchSale" />
            @endif
            <x-input-text type="datetime-local" name="date" id="date" title="Date"/>
            <div class="grid grid-cols-2 gap-2">
                <x-input-text type="number" name="amount" id="amount" title="Withdrawal Amount" placeholder="Withdrawal Amount" prepend="Rp." />
                <x-input-text type="number" name="marketplace_price" id="marketplace_price" title="Marketplace Price" placeholder="Marketplace Price" prepend="Rp." />
            </div>
        </form>
    </x-modal>
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <div class="relative w-full">
                    <button class="text-white btn bg-primary" wire:click="openModal" type="button"> Create Withdrawal </button>
                </div>
            </div>
            <div class="flex justify-end mb-4">
                <div class="relative mr-4 ms-auto">
                    <input type="search" class="relative w-48 border-none form-input bg-black/5 ps-8" wire:model.live="query"
                        placeholder="Order Id atau No Resi">
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
                @if ($withdrawals->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
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
                            @if ($showColumns['sale_date'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('sale_date')">
                                    Sale Date
                                    @if ($sortBy === 'sale_date')
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
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                wire:click="sortByColumn('customer_id')">
                                Customer
                                @if ($sortBy === 'customer_id')
                                    @if ($sortDirection === 'asc')
                                        <i class="ri-arrow-up-s-line"></i>
                                    @else
                                        <i class="ri-arrow-down-s-line"></i>
                                    @endif
                                @else
                                    <i class="ri-expand-up-down-line"></i>
                                @endif
                            </th>
                            @if ($showColumns['total_items'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
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
                            @if ($showColumns['total_sale'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
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
                            @if ($showColumns['no_resi'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('no_resi')">
                                    No Resi
                                    @if ($sortBy === 'no_resi')
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
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('order_id_marketplace')">
                                    Order ID
                                    @if ($sortBy === 'order_id_marketplace')
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
                            @if ($showColumns['marketplace_id'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('marketplace_id')">
                                    Marketplace
                                    @if ($sortBy === 'marketplace_id')
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
                            @if ($showColumns['withdrawal_date'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('sale_withdrawals.date')">
                                    Withdrawal Date
                                    @if ($sortBy === 'sale_withdrawals.date')
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
                            @if ($showColumns['amount'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('amount')">
                                    WD Amount
                                    @if ($sortBy === 'amount')
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
                            @if ($showColumns['marketplace_price'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('marketplace_price')">
                                    Marketplace Price
                                    @if ($sortBy === 'marketplace_price')
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
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
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
                            <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($withdrawals as $withdrawal)
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{($withdrawals->currentpage() - 1) * $withdrawals->perpage() + $loop->index + 1}}
                                </th>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $withdrawal->no_sale }}
                                </td>
                                @if ($showColumns['sale_date'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $withdrawal->sale_date }}
                                    </td>
                                @endif
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $withdrawal->customer_name }}
                                </td>
                                @if ($showColumns['total_items'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $withdrawal->total_items }}
                                    </td>
                                @endif
                                @if ($showColumns['total_sale'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        Rp. {{ number_format($withdrawal->total_price, 0, ',', '.') }}
                                    </td>
                                @endif
                                @if ($showColumns['no_resi'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $withdrawal->no_resi }}
                                    </td>
                                @endif
                                @if ($showColumns['order_id_marketplace'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $withdrawal->order_id_marketplace }}
                                    </td>
                                @endif
                                @if ($showColumns['marketplace_id'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $withdrawal->marketplace_name }}
                                    </td>
                                @endif
                                @if ($showColumns['withdrawal_date'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $withdrawal->date }}
                                    </td>
                                @endif
                                @if ($showColumns['amount'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ 'Rp.' . number_format($withdrawal->amount, 0, ',', '.') }}
                                    </td>
                                @endif
                                @if ($showColumns['marketplace_price'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ 'Rp.' . number_format($withdrawal->marketplace_price, 0, ',', '.') }}
                                    </td>
                                @endif
                                @if ($showColumns['created_at'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $withdrawal->created_at }}
                                    </td>
                                @endif
                                @if ($showColumns['updated_at'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $withdrawal->updated_at }}
                                    </td>
                                @endif
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center pr-4 space-x-3">
                                        <button wire:click="edit({{ $withdrawal->id }})" class="text-info">
                                            <i class="ri-edit-circle-line"></i>
                                        </button>
                                        <button wire:click="deleteAlert({{ $withdrawal->id }})" class="text-danger">
                                            <i class="text-base ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <div class="text-center">
                        <i class="text-4xl ri-file-warning-line"></i>
                        <p class="my-5 text-base">No Shipping Found</p>
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
                {{ $withdrawals->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>
</div>
