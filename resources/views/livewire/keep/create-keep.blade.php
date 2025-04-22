<div>
    @include('livewire.keep.modal')
    {{-- product --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">No Keep :
                <span class="font-bold text-success">{{ $no_keep }}</span>
            </h4>
        </div>
        <div class="p-6">
            <div class="section">
                <div class="grid gap-4 lg:grid-cols-4 md:grid-cols-2">
                    <x-input-select id="group_id" name="group_id" title="Group" :options="$groups"
                        placeholder="Select Group" />
                    <x-input-select-search id="customer_id" name="customer_id" title="Customer" :options="$customers"
                        placeholder="Select Customer" searchFunction="searchCustomer" :selected-label="$selectedCustomerLabel" />
                    <x-input-select id="keep_type" name="keep_type" title="Keep Type"
                        :options="App\Enums\KeepType::asSelectArray()" />
                    @if (strtolower($keep_type) === App\Enums\KeepType::CUSTOM)
                        <x-input-text id="keep_time" name="keep_time" title="Keep Time" placeholder="Select Time"
                            type="datetime-local" />
                    @endif
                </div>
                <div class="mt-4">
                    <x-textarea-input id="desc" name="desc" title="Keep Note" />
                </div>
            </div>
            <div class="mt-4 overflow-x-auto border border-gray-200 rounded-md section">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Product Name
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Color</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Size</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Home Stock
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Store Stock
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Price (@)
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Status</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Qty</th>
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Total</th>
                            <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @if ($cart != null)
                            @foreach ($cart as $productStock)
                                <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $productStock['product'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $productStock['color'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $productStock['size'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $productStock['home_stock'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $productStock['store_stock'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ number_format($productStock['selling_price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                        @if ($productStock['transfer'])
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-info/10 text-info">Transfer</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($productStock['transfer'])
                                                @php
                                                    $mainStock = $group_id == 1 ? 'store_stock' : 'home_stock';
                                                @endphp
                                                @if ($productStock['transfer'] + $productStock[$mainStock] < $productStock['quantity'])
                                                    <button wire:click="removeProductStock({{ $productStock['id'] }})"
                                                        class="h-8 px-4 py-1 text-sm rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                        type="button">
                                                        -
                                                    </button>
                                                @else
                                                    <button class="h-8 px-4 py-1 text-sm rounded-md bg-danger/25 text-danger font-md"
                                                        type="button">
                                                        x
                                                    </button>

                                                @endif
                                            @else
                                                <button wire:click="removeProductStock({{ $productStock['id'] }})"
                                                    class="h-8 px-4 py-1 text-sm rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                    type="button">
                                                    -
                                                </button>
                                            @endif

                                            <input type="number"
                                                wire:model.lazy="cart.{{ $productStock['id'] }}.{{ 'quantity' }}"
                                                wire:change="addToCart({{ $productStock['id'] }})"
                                                class="w-16 h-8 text-sm text-center text-gray-900 border border-gray-200 rounded-md no-arrow font-base"
                                                disabled>

                                            <button wire:click="addProductStock({{ $productStock['id'] }})"
                                                class="h-8 px-4 py-1 text-sm rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                type="button">
                                                +
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        Rp. {{ number_format($productStock['total_price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap"></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="8" class="py-4 text-center">
                                    <button class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white"
                                        wire:click="openModal('product')" type="button">
                                        Add Product
                                    </button>
                                </td>
                            </tr>
                            <tr class="border-none">
                                <td colspan="7" class="py-4 text-end"></td>
                                <td class="py-4 text-xl font-bold text-end">Total Price:</td>
                                <td class="py-4 text-xl font-bold text-end"> Rp.
                                    {{ number_format($total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="8" class="py-4 text-center">
                                    <button class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white"
                                        wire:click="openModal('product')" type="button">
                                        Add Product
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                @if ($isEdit)
                    <button wire:click="update" class="inline gap-2 text-white transition-all btn bg-primary" wire:target="update" wire:loading.attr="disabled">
                        <div class="flex gap-2" wire:loading.remove wire:target="update">
                            <i class="ri-file-excel-2-line"></i>
                            Update Keep
                        </div>
                        <div class="flex gap-2" wire:loading wire:target="update">
                            <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-light rounded-full"></div>
                        </div>
                    </button>
                @else
                    <button class="gap-1 btn bg-danger/20 text-danger" wire:click="resetKeep()">
                        <i class="ri-refresh-line"></i>
                        Reset
                    </button>
                    <button class="gap-1 text-white btn bg-primary" wire:click="save">
                        <i class="ri-save-line"></i>
                        Save Keep</button>
                @endif
            </div>
        </div>
    </div>
</div>
