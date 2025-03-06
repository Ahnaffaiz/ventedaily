<div>
    <x-modal wire:model="isOpen" title="Add Product" saveButton="closeModal" closeButton="closeModal">
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
                                    <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Home Stock</th>
                                    <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Store Stock</th>
                                    <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Price</th>
                                    <th class="px-4 py-4 text-sm font-medium text-center text-gray-500">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productStockList as $productStock)
                                    <tr>
                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $productStock->color->name }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $productStock->size->name }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $productStock->home_stock }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $productStock->store_stock }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            Rp. {{ number_format($productStock->selling_price, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if ($productStock->all_stock > 0)
                                                <div class="flex items-center justify-center gap-2">
                                                    <button wire:click="removeProductStock({{ $productStock->id }})"
                                                        class="h-8 px-4 py-1 rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                        type="button">
                                                        -
                                                    </button>

                                                    <input type="number"
                                                        wire:model.lazy="cart.{{ $productStock->id }}.{{ 'quantity' }}"
                                                        wire:change="addToCart({{ $productStock->id }})"
                                                        class="w-16 h-8 text-center text-gray-900 border border-gray-200 rounded-md no-arrow font-base">

                                                    <button wire:click="addProductStock({{ $productStock->id }})"
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
    {{-- product --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Keep Order</h4>
        </div>
        <div class="p-6">
            <div class="section">
                <div class="grid gap-4 lg:grid-cols-4 md:grid-cols-2">
                    <x-input-select id="group_id" name="group_id" title="Group" :options="$groups"
                        placeholder="Select Group" />
                    <x-input-select id="customer_id" name="customer_id" title="Customer" :options="$customers"
                        placeholder="Select Customer" />
                    <x-input-select id="keep_type" name="keep_type" title="Keep Type"
                        :options="App\Enums\KeepType::asSelectArray()"/>
                    @if (strtolower($keep_type) === App\Enums\KeepType::CUSTOM)
                        <x-input-text id="keep_time" name="keep_time" title="Keep Time" placeholder="Select Time"
                            type="datetime-local" />
                    @endif
                </div>
                <div class="mt-4">
                    <x-textarea-input id="desc" name="desc" title="Keep Note" />
                </div>
            </div>
            <div class="mt-4 border border-gray-200 rounded-md section">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Product Name
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Color</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Size</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Home Stock</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Store Stock</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Price (@)
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Qty</th>
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Total</th>
                            <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @if ($cart != null)
                            @foreach ($cart as $productStock)
                                <tr>
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
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="removeProductStock({{ $productStock['id'] }})"
                                                class="h-8 px-4 py-1 rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
                                                type="button">
                                                -
                                            </button>

                                            <input type="number"
                                                wire:model.lazy="cart.{{ $productStock['id'] }}.{{ 'quantity' }}"
                                                wire:change="addToCart({{ $productStock['id'] }})"
                                                class="w-16 h-8 text-center text-gray-900 border border-gray-200 rounded-md no-arrow font-base">

                                            <button wire:click="addProductStock({{ $productStock['id'] }})"
                                                class="h-8 px-4 py-1 rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md"
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
                                <td colspan="8" class="py-4 text-center">
                                    <button class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white"
                                        wire:click="openModal('product')" type="button">
                                        Add Product
                                    </button>
                                </td>
                            </tr>
                            <tr class="border-none">
                                <td colspan="5" class="py-4 text-end"></td>
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
                    <button class="gap-1 text-white btn bg-primary" wire:click="update">
                        <i class="ri-save-line"></i>
                        Update Keep</button>
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
