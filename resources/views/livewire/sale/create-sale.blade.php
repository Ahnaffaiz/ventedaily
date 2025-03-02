<div>
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
                                    <th class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Stock</th>
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
                                            Rp. {{ number_format($productStock->selling_price, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if ($productStock->home_stock > 0)
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
            @elseif($modalType == 'discount')
                <form>
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
    {{-- product --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Sale</h4>
        </div>
        <div class="p-6">
            <div class="section">
                <x-input-select-search id="keep_id" name="keep_id" title="Keep Code" placeholder="Type Keep Code"
                    :options="$keeps" searchFunction="searchKeep"/>
            </div>
            <div class="section">
                <div class="grid gap-4 md:grid-cols-3">
                    <x-input-select id="group_id" name="group_id" title="Group" :options="$groups"
                        placeholder="Select Group" />
                    <x-input-select id="customer_id" name="customer_id" title="Customer" :options="$customers"
                        placeholder="Select Customer" />
                    <x-input-select id="term_of_payment_id" name="term_of_payment_id" title="Term of Payemnt"
                        :options="$termOfPayments" placeholder="Select Term of Payment" />
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
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Stock</th>
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
                                        {{ $productStock['stock'] }}
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
                            <tr>
                                <td colspan="5" class="py-4 text-end"></td>
                                <td class="py-4 text-end">
                                    <p class="mt-2 mb-2 text-lg font-semibold">Sub Total :</p>
                                    <div class="mb-2">
                                        <a wire:click="openModal('discount')" class="text-base font-bold text-success">
                                            Discount
                                            @if (strtolower($discount_type) === App\Enums\DiscountType::PERSEN)
                                                <span
                                                    class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-sm font-medium bg-success/10 text-success">{{ $discount }}%</span>
                                            @endif
                                        </a> :
                                    </div>
                                    <div class="mb-2">
                                        <a wire:click="openModal('tax')" class="text-base font-bold text-danger">
                                            Tax
                                            <span
                                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-sm font-medium bg-danger/10 text-danger">{{ $tax }}%</span>
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
                                    <p class="mt-2 text-base font-semibold text-success text-end">
                                        -Rp.
                                        {{ strtolower($discount_type) === App\Enums\DiscountType::PERSEN ? number_format($sub_total * (int) $discount / 100, 0, ',', '.') : number_format((int) $discount, 0, ',', '.') }}
                                    </p>
                                    <p class="mt-2 text-base font-semibold text-danger text-end">
                                        +Rp. {{ number_format($sub_total_after_discount * (int) $tax / 100, 0, ',', '.') }}
                                    </p>
                                    <p class="mt-2 text-base font-semibold text-end">
                                        +Rp. {{ number_format($ship, 0, ',', '.') }}
                                    </p>
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
                    <button class="gap-1 btn bg-danger/20 text-danger" wire:click="resetPurchase()">
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
