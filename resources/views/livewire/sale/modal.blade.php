<x-modal wire:model="isOpen" title="Add Product" saveButton="closeModal" closeButton="closeModal">
    <div>
        @if ($modalType == 'product')
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
                                @if ($preOrder || $sale?->pre_order_id)
                                    <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Pre Order Stock</th>
                                @else
                                    @if ($group_id == 2)
                                        <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Home Stock</th>
                                    @endif
                                    @if ($group_id == 1)
                                        <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Store Stock</th>
                                    @endif
                                @endif
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
                                    @if ($preOrder || $sale?->pre_order_id)
                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $productStock['pre_order_stock'] }}
                                        </td>
                                    @else
                                        @if ($group_id == 2)
                                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $productStock['home_stock'] }}
                                            </td>
                                        @endif
                                        @if ($group_id == 1)
                                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $productStock['store_stock'] }}
                                            </td>
                                        @endif
                                    @endif
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        Rp. {{ number_format($productStock['selling_price'], 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if ($productStock['all_stock'] > 0)
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
        @elseif($modalType == 'discount')
            <form class="pb-16">
                <div class="mt-5">
                    <div class="flex gap-5">
                        <div class="flex items-center">
                            <input type="radio" class="form-radio text-primary" wire:model.live="is_discount_program" value="yes" id="discount_yes">
                            <label class="ms-1.5" for="discount_yes">Discount Code</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" class="form-radio text-primary" wire:model.live="is_discount_program" value="no" id="discount_no">
                            <label class="ms-1.5" for="discount_no">Custom</label>
                        </div>
                    </div>
                </div>
                @if ($is_discount_program === 'yes')
                    <div class="pb-8">
                        <x-input-select-search id="discount_id" name="discount_id" title="Discount Code" placeholder="Input Discount Code"
                    :options="$discount_programs" searchFunction="searchDiscount"/>
                    @if ($discount_id)
                        <div class="px-5 py-3 mt-2 text-sm border rounded-md bg-success/10 text-success border-success/20" role="alert">
                            <span class="font-bold">{{ $discount_program->name }} ({{ strtolower($discount_type) === App\Enums\DiscountType::RUPIAH ? 'Rp.' : '' }} {{ $discount_program->value }} {{ strtolower($discount_type) === App\Enums\DiscountType::RUPIAH ? '' : '%' }})</span> - Successfully Apply
                        </div>
                    @endif
                    </div>
                @else
                    {{ $discount_type }}
                    <x-input-select id="discount_type" name="discount_type" title="Discount Type"
                        :options="App\Enums\DiscountType::asSelectArray()" />
                    <x-input-text type="number" id="discount" name="discount" title="Discount"
                        placeholder="Input Discount without Rp or %"
                        prepend="{{ strtolower($discount_type) === App\Enums\DiscountType::RUPIAH ? 'Rp.' : '%' }}" />
                @endif
            </form>
        @elseif ($modalType == 'tax')
            <form>
                <x-input-text type="number" id="tax" name="tax" title="Tax" placeholder="Input Tax" prepend="%" />
            </form>
        @elseif ($modalType == 'ship')
            <form>
                <x-input-text type="number" id="ship" name="ship" title="Shipping Cost"
                    placeholder="Input Shipping Cost" prepend="Rp" />
            </form>
        @endif
    </div>
</x-modal>
