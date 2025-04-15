<div>
    @include('livewire.product.transfer-stock.modal')
    {{-- product --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Form Transfer Stock</h4>
        </div>
        <div class="p-6">
            <div class="section">
                <div class="grid gap-2 lg:grid-cols-2 md:grid-cols-2">
                    <x-input-select id="transfer_from" name="transfer_from" title="Transfer From" :options="App\Enums\StockType::asSelectArray()" placeholder="Transfer From" />
                    <x-input-select id="transfer_to" name="transfer_to" title="Transfer To" :options="App\Enums\StockType::asSelectArray()" placeholder="Transfer To" />
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
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Max Stock</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Keep Status</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Qty</th>
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
                                        {{ $productStock['max_stock'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                        @if ($productStock['keep_product_id'])
                                            @php
                                            $keepProduct = App\Models\KeepProduct::whereIn('id', $productStock['keep_product_id'])->get()->sum($transfer_from);
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-info/10 text-info">Keep : {{ $keepProduct }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="removeProductStock({{ $productStock['id'] }})"
                                                class="h-8 px-4 py-1 text-sm rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                type="button">
                                                -
                                            </button>

                                            <input type="number"
                                                wire:model.lazy="cart.{{ $productStock['id'] }}.{{ 'stock' }}"
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
                    <button class="gap-1 text-white btn bg-primary" wire:click="update">
                        <i class="ri-save-line"></i>
                        Update Transfer
                    </button>
                @else
                    <button class="gap-1 text-white btn bg-primary" wire:click="save">
                        <i class="ri-save-line"></i>
                        Save Transfer
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
