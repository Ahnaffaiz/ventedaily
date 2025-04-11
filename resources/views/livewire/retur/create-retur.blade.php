<div>
    <x-modal wire:model="isOpen" title="Change Retur Status"
        saveButton="saveReturStatus" closeButton="closeModal">
        <x-input-select id="item_status" name="item_status" title="Item Status" placeholder="Select Status"
        :options="App\Enums\ItemReturStatus::asSelectArray()" />
    </x-modal>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">No Retur :
                <span class="font-bold text-success">{{ $no_retur }}</span>
            </h4>
        </div>
        <div class="p-6">
            <div class="section">
                <div class="grid gap-3 lg:grid-cols-3 md:grid-cols-2">
                    <x-input-select-search id="sale_id" name="sale_id" title="Sale Code" placeholder="Type Sale Code" :options="$sales" searchFunction="searchSales" />
                    <x-input-select id="status" name="status" title="Status" :options="App\Enums\ReturStatus::asSelectArray()" />
                    <x-input-select id="reason" name="reason" title="Reason" :options="App\Enums\ReturReason::asSelectArray()" />
                </div>
                @if ($sale || $retur)
                    <div class="grid gap-2 mt-4 lg:grid-cols-2 md:grid-cols-2">
                        <table class="min-w-full">
                            <tbody>
                                <tr>
                                    <td class="font-normal text-md text-start">Customer :</td>
                                    <td class="font-normal text-md text-start">Group :</td>
                                </tr>
                                <tr>
                                    <td class="font-bold text-md text-start">{{ $customer_name }}</td>
                                    <td class="font-bold text-md text-start">{{ $group_name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="mt-4">
                    <x-textarea-input id="desc" name="desc" title="Retur Reason" />
                </div>
            </div>
            <div class="mt-4 overflow-x-auto border border-gray-200 rounded-md section">
                @if ($saleItems != null)
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Product Name
                                </th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Color</th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Size</th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Price (@)</th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Qty</th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Total</th>
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Status</th>
                                <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($saleItems as $saleItem)
                                <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $saleItem['product'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $saleItem['color'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $saleItem['size'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ number_format($saleItem['price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            {{ $saleItem['total_items'] }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        @if (array_key_exists($saleItem['id'], $returItems))
                                            Rp. {{ number_format($returItems[$saleItem['id']]['total_price'], 0, ',', '.') }}
                                        @else
                                            Rp. {{ number_format($saleItem['total_price'], 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                        @if (array_key_exists($saleItem['id'], $returItems))
                                            <button wire:click="changeItemStatus({{ $saleItem['id'] }})">
                                                @if ($returItems[$saleItem['id']]['item_status'] == strtolower(App\Enums\ItemReturStatus::VERMAK))
                                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-info/10 text-info">
                                                        {{ ucwords($returItems[$saleItem['id']]['item_status']) }}
                                                    </span>
                                                @elseif ($returItems[$saleItem['id']]['item_status'] == strtolower(App\Enums\ItemReturStatus::GRADE_B))
                                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-danger/10 text-danger">
                                                        {{ ucwords($returItems[$saleItem['id']]['item_status']) }}
                                                    </span>
                                                @endif
                                            </button>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-secondary/10 text-secondary">
                                                Not Returned
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                        @if (array_key_exists($saleItem['id'], $returItems))
                                            <div class="flex items-center justify-center gap-2">
                                                <button wire:click="removeReturnItem({{ $saleItem['id'] }})"
                                                    class="h-8 px-4 py-1 text-sm rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                    type="button">
                                                    -
                                                </button>

                                                <input type="number"
                                                    wire:model.lazy="returItems.{{ $saleItem['id'] }}.{{ 'total_items' }}"
                                                    class="w-16 h-8 text-sm text-center text-gray-900 border border-gray-200 rounded-md no-arrow font-base"
                                                    disabled>

                                                <button wire:click="addReturnItem({{ $saleItem['id'] }})"
                                                    class="h-8 px-4 py-1 text-sm rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                    type="button">
                                                    +
                                                </button>
                                            </div>
                                        @else
                                            <button class="h-8 px-4 py-1 text-sm rounded-md bg-danger/25 text-danger hover:bg-danger hover:text-white font-md"
                                            wire:click="returProduct({{ $saleItem['id'] }})">Return</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if ($returItems)
                                <tr class="border-none">
                                    <td colspan="7" class="py-4 text-end"></td>
                                    <td class="py-4 text-xl font-bold text-end">Total Price:</td>
                                    <td class="py-4 text-xl font-bold pe-6 text-end"> Rp.
                                        {{ number_format($total_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="flex justify-end gap-3 mt-6">
                @if ($isEdit)
                    <button class="gap-1 text-white btn bg-primary" wire:click="update">
                        <i class="ri-save-line"></i>
                        Update</button>
                @else
                    <button class="gap-1 btn bg-danger/20 text-danger" wire:click="resetRetur()">
                        <i class="ri-refresh-line"></i>
                        Reset
                    </button>
                    <button class="gap-1 text-white btn bg-primary" wire:click="save">
                        <i class="ri-save-line"></i>
                        Save Retur</button>
                @endif
            </div>
        </div>
    </div>
</div>
