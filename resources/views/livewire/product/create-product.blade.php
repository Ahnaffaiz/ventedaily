<div>
    <div class="card">
        <div class="p-6">
            <h4 class="mb-2 card-title">Product</h4>
            <p class="mb-4">General Information</p>
            <div class="grid gap-6 lg:grid-cols-2">
                <x-input-text name="name" id="name" title="Name" placeholder="Input Product Name Here" />
                <x-input-text name="imei" id="imei" title="Barcode Imei" placeholder="Input Imei Barcode Here" />
                <x-input-select id="category_id" name="category_id" title="Category" placeholder="Select Category"
                    :options="$categories" />
                <x-input-select id="status" name="status" title="Product Status"
                    :options="App\Enums\ProductStatus::asSelectArray()" placeholder="Select status" />
                <x-input-switch id="is_favorite" name="is_favorite" title="Favorite" />
            </div>
            <div class="flex justify-end">
                <button class="mt-5 text-white btn bg-primary" href="{{ route('create-product') }}" wire:click="save">
                    Save</button>
            </div>
        </div>
    </div>
    @if ($product)
        <div class="card">
            <div class="p-6">
                <h4 class="mb-2 card-title">Product</h4>
                <p class="mb-4">General Information</p>
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            @if ($productStocks->count() > 0)
                                <thead>
                                    <tr>
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                            wire:click="sortByColumn('size')">
                                            Size
                                            @if ($sortBy === 'size')
                                                @if ($sortDirection === 'asc')
                                                    <i class="ri-arrow-up-s-line"></i>
                                                @else
                                                    <i class="ri-arrow-down-s-line"></i>
                                                @endif
                                            @else
                                                <i class="ri-expand-up-down-line"></i>
                                            @endif
                                        </th>
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                            wire:click="sortByColumn('color')">
                                            Color
                                            @if ($sortBy === 'color')
                                                @if ($sortDirection === 'asc')
                                                    <i class="ri-arrow-up-s-line"></i>
                                                @else
                                                    <i class="ri-arrow-down-s-line"></i>
                                                @endif
                                            @else
                                                <i class="ri-expand-up-down-line"></i>
                                            @endif
                                        </th>
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                            wire:click="sortByColumn('selling_price')">
                                            Selling Price
                                            @if ($sortBy === 'selling_price')
                                                @if ($sortDirection === 'asc')
                                                    <i class="ri-arrow-up-s-line"></i>
                                                @else
                                                    <i class="ri-arrow-down-s-line"></i>
                                                @endif
                                            @else
                                                <i class="ri-expand-up-down-line"></i>
                                            @endif
                                        </th>
                                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                            wire:click="sortByColumn('purchase_price')">
                                            Purchase Price
                                            @if ($sortBy === 'purchase_price')
                                                @if ($sortDirection === 'asc')
                                                    <i class="ri-arrow-up-s-line"></i>
                                                @else
                                                    <i class="ri-arrow-down-s-line"></i>
                                                @endif
                                            @else
                                                <i class="ri-expand-up-down-line"></i>
                                            @endif
                                        </th>
                                        @if ($showColumns['all_stock'])
                                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                                wire:click="sortByColumn('all_stock')">
                                                All Stock
                                                @if ($sortBy === 'all_stock')
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
                                        @if ($showColumns['home_stock'])
                                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                                wire:click="sortByColumn('home_stock')">
                                                Home Stock
                                                @if ($sortBy === 'home_stock')
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
                                        @if ($showColumns['qc_stock'])
                                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                                wire:click="sortByColumn('qc_stock')">
                                                QC Stock
                                                @if ($sortBy === 'qc_stock')
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
                                        @if ($showColumns['storage_stock'])
                                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                                wire:click="sortByColumn('storage_stock')">
                                                Storage Stock
                                                @if ($sortBy === 'storage_stock')
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
                                        @if ($showColumns['vermak_stock'])
                                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                                wire:click="sortByColumn('vermak_stock')">
                                                Vermak Stock
                                                @if ($sortBy === 'vermak_stock')
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
                                    @foreach ($productStocks as $productStock)
                                        <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                            <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                {{($productStock->currentpage() - 1) * $productStock->perpage() + $loop->index + 1}}
                                            </th>
                                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                {{ $productStock->size->name }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                {{ $productStock->color->name }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                {{ $productStock->selling_price }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                {{ $productStock->purchase_price }}
                                            </td>
                                            @if ($showColumns['all_stock'])
                                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                    {{ $product->all_stock }}
                                                </td>
                                            @endif
                                            @if ($showColumns['home_stock'])
                                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                    {{ $product->home_stock }}
                                                </td>
                                            @endif
                                            @if ($showColumns['qc_stock'])
                                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                    {{ $product->qc_stock }}
                                                </td>
                                            @endif
                                            @if ($showColumns['storage_stock'])
                                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                    {{ $product->storage_stock }}
                                                </td>
                                            @endif
                                            @if ($showColumns['vermak_stock'])
                                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                    {{ $product->vermak_stock }}
                                                </td>
                                            @endif
                                            @if ($showColumns['created_at'])
                                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                    {{ $product->created_at }}
                                                </td>
                                            @endif
                                            @if ($showColumns['updated_at'])
                                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                                    {{ $product->updated_at }}
                                                </td>
                                            @endif
                                            <td class="px-4 py-4">
                                                <div class="flex items-center justify-center space-x-3">
                                                    <button wire:click="edit({{ $productStock->id }})"><i
                                                            class="ri-edit-circle-line"></i></button>
                                                    <button wire:click="deleteAlert({{ $productStock->id }})"><i
                                                            class="text-base ri-delete-bin-2-line"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @else
                                <div class="text-center">
                                    <i class="text-4xl ri-file-warning-line"></i>
                                    <p class="my-5 text-base">No Product Found</p>
                                </div>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
