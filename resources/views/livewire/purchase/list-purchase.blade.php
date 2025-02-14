<div>
    <x-modal wire:model="isOpen" title="Purchase Payment" closeButton="closeModal" large="true">
        @if ($isPayment)
            @livewire('purchase.purchase-payment', ['purchase' => $purchase], key($purchase->id))
        @else

        @endif
    </x-modal>
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <div class="relative w-full">
                    <a class="text-white btn bg-primary" wire:navigate href="{{ route('create-purchase') }}"
                        type="button">
                        Create </a>
                </div>
            </div>
            <div class="flex justify-end mb-4">
                <div class="relative mr-4 ms-auto">
                    <input type="search" class="relative border-none form-input bg-black/5 ps-8" wire:model.live="query"
                        placeholder="Search...">
                    <span class="absolute z-10 text-base -translate-y-1/2 ri-search-line start-2 top-1/2"></span>
                </div>
                <div class="relative ms-auto">
                    <button data-fc-type="dropdown" data-fc-placement="bottom-end" type="button"
                        class="flex items-center py-2 pl-3 pr-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-500 dark:bg-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        id="menu-button" aria-expanded="true" aria-haspopup="true">
                        <i class="mr-2 ri-filter-line"></i>
                    </button>
                    <div class="absolute right-0 z-10 hidden w-56 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg dark:divide-gray-600 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="py-1" role="none">
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
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                @if ($purchases->count() > 0)
                            <thead>
                                <tr>
                                    <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                        wire:click="sortByColumn('supplier_id')">
                                        Supplier
                                        @if ($sortBy === 'supplier_id')
                                            @if ($sortDirection === 'asc')
                                                <i class="ri-arrow-up-s-line"></i>
                                            @else
                                                <i class="ri-arrow-down-s-line"></i>
                                            @endif
                                        @else
                                            <i class="ri-expand-up-down-line"></i>
                                        @endif
                                    </th>
                                    @if ($showColumns['term_of_payment_id'])
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                            wire:click="sortByColumn('term_of_payment_id')">
                                            Term
                                            @if ($sortBy === 'term_of_payment_id')
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
                                    @if ($showColumns['sub_total'])
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                            wire:click="sortByColumn('sub_total')">
                                            Amount
                                            @if ($sortBy === 'sub_total')
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
                                    @if ($showColumns['discount'])
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                            wire:click="sortByColumn('discount')">
                                            Discount
                                            @if ($sortBy === 'discount')
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
                                    @if ($showColumns['tax'])
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                            wire:click="sortByColumn('tax')">
                                            Tax
                                            @if ($sortBy === 'tax')
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
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                            wire:click="sortByColumn('total_price')">
                                            Total Purchase
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
                                    @if ($showColumns['outstanding_balance'])
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                            wire:click="sortByColumn('outstanding_balance')">
                                            Outs Balance
                                            @if ($sortBy === 'outstanding_balance')
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
                                @foreach ($purchases as $purchase)
                                                <tr class="bg-gray-50 dark:bg-gray-900">
                                                    <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                        {{($purchases->currentpage() - 1) * $purchases->perpage() + $loop->index + 1}}
                                                    </th>
                                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                        {{ $purchase->supplier->name }}
                                                    </td>
                                                    @if ($showColumns['term_of_payment_id'])
                                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                            {{ $purchase->termOfPayment->name }}
                                                        </td>
                                                    @endif
                                                    @if ($showColumns['sub_total'])
                                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                            {{ 'Rp.' . number_format($purchase->sub_total, 0, ',', '.') }}
                                                        </td>
                                                    @endif
                                                    @php
                                                        $discount = $purchase->discount_type === App\Enums\DiscountType::PERSEN ? $purchase->sub_total * (int) $purchase->discount / 100 : $purchase->discount;
                                                    @endphp
                                                    @if ($showColumns['discount'])
                                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                            Rp. {{ number_format($discount, 0, ',', '.')}}
                                                        </td>
                                                    @endif
                                                    @if ($showColumns['tax'])
                                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                            Rp. {{ $purchase->tax / 100 * ($purchase->sub_total - $discount)}}
                                                        </td>
                                                    @endif
                                                    @if ($showColumns['total_price'])
                                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                            Rp. {{ number_format($purchase->total_price, 0, ',', '.') }}
                                                        </td>
                                                    @endif
                                                    @if ($showColumns['outstanding_balance'])
                                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                            Rp. {{ number_format($purchase->outstanding_balance, 0, ',', '.') }}
                                                        </td>
                                                    @endif
                                                    @if ($showColumns['created_at'])
                                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                            {{ $purchase->created_at }}
                                                        </td>
                                                    @endif
                                                    @if ($showColumns['updated_at'])
                                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                            {{ $purchase->updated_at }}
                                                        </td>
                                                    @endif
                                                    <td class="px-4 py-4">
                                                        <div class="flex items-center justify-center pr-4 space-x-3">
                                                            <button wire:click="addPayment({{ $purchase->id }})" class="text-primary">
                                                                <i class="ri-bank-card-2-line"></i>
                                                            </button>
                                                            <a wire:navigate href="{{ route('create-purchase', ['purchase' => $purchase->id]) }}"
                                                                class="text-info">
                                                                <i class="ri-edit-circle-line"></i>
                                                            </a>
                                                            <button wire:click="deleteAlert({{ $purchase->id }})" class="text-danger">
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
                        <p class="my-5 text-base">No Purchase Found</p>
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
                {{ $purchases->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>
</div>
