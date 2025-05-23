<div>
    @include('livewire.sale.modal')
    {{-- product --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                Sale
                <span class="font-bold text-success dark:text-success">{{ $no_sale }}</span>
            </h4>
        </div>
        <div class="p-6">
            @if (!$isEdit)
                <div class="flex gap-5">
                    <div class="flex items-center">
                        <input type="radio" class="form-radio text-primary" wire:model.live="saleFrom" value="keep" id="keep">
                        <label class="ms-1.5 dark:text-gray-200" for="keep">Keep</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" class="form-radio text-primary" wire:model.live="saleFrom" value="pre_order" id="pre_order">
                        <label class="ms-1.5 dark:text-gray-200" for="pre_order">Pre Order</label>
                    </div>
                </div>
                <div class="section">
                    @if ($saleFrom === 'keep')
                        <x-input-select-search id="keep_id" name="keep_id" title="Keep Code" placeholder="Search by Keep Code or Order ID"
                            :options="$keeps" searchFunction="searchKeep" />
                    @elseif ($saleFrom === 'pre_order')
                        <x-input-select-search id="pre_order_id" name="pre_order_id" title="Pre Order Code" placeholder="Type Pre Order Code"
                            :options="$preOrders" searchFunction="searchPreOrder" />
                    @endif
                </div>
            @endif
            <div class="section">
                <div class="grid gap-4 md:grid-cols-3">
                    <x-input-select id="group_id" name="group_id" title="Group" :options="$groups"
                        placeholder="Select Group" />
                        <x-input-select-search
                        id="customer_id"
                        name="customer_id"
                        title="Customer"
                        :options="$customers"
                        placeholder="Select Customer"
                        searchFunction="searchCustomer"
                        :selected-label="$selectedCustomerLabel"
                        wire:key="'customer-select-'.$selectedCustomerLabel"
                    />
                    <x-input-select id="term_of_payment_id" name="term_of_payment_id" title="Term of Payemnt"
                        :options="$termOfPayments" placeholder="Select Term of Payment" />
                </div>
                @if ($group_id == 2)
                    <div class="grid gap-6 md:grid-cols-2">
                        <x-input-select id="marketplace_id" name="marketplace_id" title="Marketplace" :options="$marketplaces"
                            placeholder="Select Marketplace" />
                        <x-input-text id="order_id_marketplace" name="order_id_marketplace" title="Order Id"/>
                    </div>
                @endif
            </div>
            <div class="mt-4 overflow-x-auto border border-gray-200 rounded-md dark:border-gray-600 section">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-200">No</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Product Name
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Color</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Size</th>
                            @if ($preOrder || $sale?->pre_order_id != null)
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Pre Order Stock</th>
                            @else
                                @if ($group_id == 2)
                                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Home Stock
                                    </th>
                                @endif
                                @if ($group_id == 1)
                                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Store Stock
                                    </th>
                                @endif
                            @endif
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Price (@)
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-200">Qty</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 dark:text-gray-200 text-start">Total</th>
                            <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500 dark:text-gray-200"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @if ($cart != null)
                            @foreach ($cart as $productStock)
                                <tr>
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 dark:text-gray-200 whitespace-nowrap">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-200 whitespace-nowrap">
                                        {{ $productStock['product'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-200 whitespace-nowrap">
                                        {{ $productStock['color'] }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-200 whitespace-nowrap">
                                        {{ $productStock['size'] }}
                                    </td>
                                    @if ($preOrder || $sale?->pre_order_id != null)
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-200 whitespace-nowrap">
                                            {{ $productStock['pre_order_stock'] }}
                                        </td>
                                    @else
                                        @if ($group_id == 2)
                                            <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-200 whitespace-nowrap">
                                                {{ $productStock['home_stock'] }}
                                            </td>
                                        @endif
                                        @if ($group_id == 1)
                                            <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-200 whitespace-nowrap">
                                                {{ $productStock['store_stock'] }}
                                            </td>
                                        @endif
                                    @endif
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-200 whitespace-nowrap">
                                        {{ number_format($productStock['selling_price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-200 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="removeProductStock({{ $productStock['id'] }})"
                                                class="h-8 px-4 py-1 text-sm rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md dark:text-white"
                                                type="button">
                                                -
                                            </button>

                                            <input type="number"
                                                wire:model.lazy="cart.{{ $productStock['id'] }}.{{ 'quantity' }}"
                                                wire:change="addToCart({{ $productStock['id'] }})"
                                                class="w-16 h-8 text-sm text-center text-gray-900 border border-gray-200 rounded-md form-input no-arrow font-base "
                                                disabled>

                                            <button wire:click="addProductStock({{ $productStock['id'] }})"
                                                class="h-8 px-4 py-1 text-sm rounded-md bg-primary/25 text-primary hover:bg-primary hover:text-white font-md dark:text-white"
                                                type="button">
                                                +
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-200 whitespace-nowrap">
                                        Rp. {{ number_format($productStock['total_price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-200 whitespace-nowrap"></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="8" class="py-4 text-center">
                                    <button class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white dark:text-white"
                                        wire:click="openModal('product')" type="button">
                                        Add Product
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="py-4 text-end"></td>
                                <td class="py-4 text-end">
                                    <p class="mt-2 mb-2 text-lg font-semibold">Sub Total :</p>
                                    <div class="mb-2">
                                        <a wire:click="openModal('discount')" class="text-base font-bold text-success dark:text-success">
                                            Discount
                                            @if (strtolower($discount_type) === App\Enums\DiscountType::PERSEN)
                                                <span
                                                    class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-sm font-medium bg-success/10 text-success dark:text-success">{{ $discount }}%</span>
                                            @endif
                                        </a> :
                                    </div>
                                    <div class="mb-2">
                                        <a wire:click="openModal('tax')" class="text-base font-bold text-danger dark:text-danger">
                                            Tax
                                            <span
                                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-sm font-medium bg-danger/10 text-danger dark:text-danger">{{ $tax }}%</span>
                                        </a> :
                                    </div>
                                    <div class="mb-2">
                                        <a wire:click="openModal('ship')" class="text-base font-bold">
                                            Shipping Cost
                                        </a> :
                                    </div>
                                </td>
                                <td class="py-4 font-semibold text-md text-start ps-4">
                                    <p class="mt-2 text-lg font-semibold text-end">Rp
                                        {{ number_format($sub_total, 0, ',', '.') }}
                                    </p>
                                    <p class="mt-2 text-base font-semibold text-success dark:text-success text-end">
                                        -Rp.
                                        {{ strtolower($discount_type) === App\Enums\DiscountType::PERSEN ? number_format($sub_total * (int) $discount / 100, 0, ',', '.') : number_format((int) $discount, 0, ',', '.') }}
                                    </p>
                                    <p class="mt-2 text-base font-semibold text-danger dark:text-danger text-end">
                                        +Rp. {{ number_format($sub_total_after_discount * (int) $tax / 100, 0, ',', '.') }}
                                    </p>
                                    <p class="mt-2 text-base font-semibold text-end">
                                        +Rp. {{ number_format($ship, 0, ',', '.') }}
                                    </p>
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
                                <td colspan="8" class="py-4 text-center">
                                    <button class="btn bg-primary/25 text-primary hover:bg-primary hover:text-white dark:text-white"
                                        wire:click="openModal('product')" type="button">
                                        Add Product
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="pt-4 section">
                <x-textarea-input id="desc" name="desc" title="Sale Note" />
                <div class="grid lg:grid-cols-3 lg:gap-3 md:grid-cols-2 md:gap-2">
                    <x-input-select id="payment_type" name="payment_type" title="Payment Type"
                        :options="App\Enums\PaymentType::asSelectArray()" placeholder="Select Payment Type" />
                    <x-input-text id="cash_received" name="cash_received" title="Cash Received" type="number"
                        prepend="Rp." />
                    <x-input-text id="cash_change" name="cash_change" title="Change" type="number" prepend="Rp."
                        disabled="true" />
                </div>
                @if (strtolower($payment_type) === App\Enums\PaymentType::TRANSFER)
                    <div class="grid lg:grid-cols-3 md:grid-cols-2 lg:gap-2 md:gap-2">
                        <x-input-select id="bank_id" name="bank_id" title="Bank"
                            :options="App\Models\Bank::all()->pluck('name', 'id')->toArray()"
                            placeholder="Select Payment Type" />
                        <x-input-text id="account_number" name="account_number" title="Account Number" type="number" />
                        <x-input-text id="account_name" name="account_name" title="Account Name" />
                    </div>
                @endif
            </div>
            <div class="flex justify-end gap-3 mt-6">
                @if ($isEdit)
                    <button class="gap-1 text-white btn bg-primary" wire:click="update">
                        <i class="ri-save-line"></i>
                        Save</button>
                @else
                    <button class="gap-1 btn bg-danger/20 text-danger" wire:click="resetSale()">
                        <i class="ri-refresh-line"></i>
                        Reset
                    </button>
                    <button class="gap-1 text-white btn bg-primary" wire:click="save">
                        <i class="ri-save-line"></i>
                        Save</button>
                @endif
            </div>
        </div>
    </div>
</div>
