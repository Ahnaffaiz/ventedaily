<div>
    @include('livewire.pre-order.modal')
    {{-- product --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">No Pre Order :
                <span class="font-bold text-success font-md">{{ $no_pre_order }}</span>
            </h4>
        </div>
        <div class="p-6">
            <div class="section">
                <div class="grid gap-3 lg:grid-cols-3 md:grid-cols-2">
                    <x-input-select-search id="customer_id" name="customer_id" title="Customer" :options="$customers"
                        placeholder="Select Customer" searchFunction="searchCustomer" :selected-label="$selectedCustomerLabel" />
                </div>
                <div class="mt-4">
                    <x-textarea-input id="desc" name="desc" title="PreOrder Note" />
                </div>
            </div>
            <div class="mt-4 overflow-x-auto border border-gray-200 rounded-md section">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Product Name</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Color</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Size</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Pre Order Stock</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Price (@)</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Qty</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Total</th>
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
                                        {{ $productStock['pre_order_stock'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ number_format($productStock['selling_price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="removeProductStock({{ $productStock['id'] }})"
                                                class="h-8 px-4 py-1 text-sm rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                type="button">
                                                -
                                            </button>

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
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="7" class="py-4 text-center">
                                    <button class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white"
                                        wire:click="openModal('product')" type="button">
                                        Add Product
                                    </button>
                                </td>
                            </tr>
                            <tr class="border-none">
                                <td colspan="6" class="py-4 text-end"></td>
                                <td class="py-4 text-xl font-bold text-end">Total Price:</td>
                                <td class="py-4 text-xl font-bold text-end"> Rp.
                                    {{ number_format($total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" class="py-4 text-center">
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
                    <button class="gap-1 text-white btn bg-primary" wire:click="update">
                        <i class="ri-save-line"></i>
                        Update PreOrder</button>
                @else
                    <button class="gap-1 btn bg-danger/20 text-danger" wire:click="resetPreOrder()">
                        <i class="ri-refresh-line"></i>
                        Reset
                    </button>
                    <button class="gap-1 text-white btn bg-primary" wire:click="save">
                        <i class="ri-save-line"></i>
                        Save PreOrder</button>
                @endif
            </div>
        </div>
    </div>
</div>
