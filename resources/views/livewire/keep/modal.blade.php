<x-modal wire:model="isOpen" title="Add Product" saveButton="closeModal" closeButton="closeModal" large="true">
    <div>
        <form class="pb-28">
            <x-input-select-search id="product_id" name="product_id" title="Product" placeholder="Select Product"
                :options="$products" searchFunction="searchProduct"/>
            @if ($productStockList)
                <div class="mt-4 border border-gray-300 rounded-md">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Color</th>
                                <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Size</th>
                                <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Total Stock</th>
                                <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Home Stock</th>
                                <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Store Stock</th>
                                <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Price</th>
                                <th class="px-4 py-4 text-sm font-medium text-center text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productStockList as $productStock)
                                <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }}">
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $productStock['color']['name'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $productStock['size']['name'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $productStock['total_stock'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $productStock['home_stock'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $productStock['store_stock'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        Rp. {{ number_format($productStock['selling_price'], 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if ($productStock['total_stock'] > 0)
                                            <div class="flex items-center justify-center gap-2">
                                                <button wire:click="removeProductStock({{ $productStock['id'] }})"
                                                    class="h-8 px-4 py-1 rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                    type="button">
                                                    -
                                                </button>

                                                <input type="number"
                                                    wire:model.lazy="cart.{{ $productStock['id'] }}.{{ 'quantity' }}"
                                                    wire:change="addToCart({{ $productStock['id'] }})"
                                                    class="w-16 h-8 text-center text-gray-900 border border-gray-200 rounded-md no-arrow font-base" disabled>

                                                <button wire:click="addProductStock({{ $productStock['id'] }})"
                                                    class="h-8 px-4 py-1 rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                    type="button">
                                                    +
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <span class="px-4 py-2 text-white border-none rounded-full bg-danger/50">Out off Stock</span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </form>
    </div>
</x-modal>
