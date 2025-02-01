<div>
    <div class="border border-gray-200 rounded-md">
        <div class="p-6">
            <div class="grid gap-4 lg:grid-cols-3">
                <x-input-select id="color_id" name="color_id" title="Color" placeholder="Select Color"
                    :options="$colors" />
                <x-input-select id="size_id" name="size_id" title="Size" placeholder="Select Size" :options="$sizes" />
                <x-input-select id="status" name="status" title="Product Status"
                    :options="App\Enums\ProductStatus::asSelectArray()" placeholder="Select status" />
                <x-input-text type="number" name="purchase_price" id="purchase_price" title="Purchase Price"
                    placeholder="100.000" prepend="Rp." />
                <x-input-text type="number" name="selling_price" id="selling_price" title="Selling Price"
                    placeholder="150.000" prepend="Rp." />
                <x-input-text type="number" name="margin_price" id="margin_price" title="Margin" disabled="true"
                    prepend="Rp." />
            </div>
            <div class="flex justify-end mt-4">
                <button class="text-white btn bg-primary" wire:click="save" type="button">
                    Save </button>
            </div>
        </div>
    </div>
    <div class="mt-6 overflow-x-auto border border-gray-200 rounded-md">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <h4 class="card-title">Daftar Jenis Product</h4>
            </div>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            @if ($productStocks?->count() > 0)
                <thead>
                    <tr>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                            wire:click="sortByColumn('size_id')">
                            Size
                            @if ($sortBy === 'size_id')
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
                            wire:click="sortByColumn('color_id')">
                            Color
                            @if ($sortBy === 'color_id')
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
                                Home
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
                                QC
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
                                Storage
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
                                Vermak
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
                        <tr class="bg-gray-50 dark:bg-gray-900">
                            <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{($productStocks->currentpage() - 1) * $productStocks->perpage() + $loop->index + 1}}
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
                                    {{ $productStock->all_stock }}
                                </td>
                            @endif
                            @if ($showColumns['home_stock'])
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $productStock->home_stock }}
                                </td>
                            @endif
                            @if ($showColumns['qc_stock'])
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $productStock->qc_stock }}
                                </td>
                            @endif
                            @if ($showColumns['storage_stock'])
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $productStock->storage_stock }}
                                </td>
                            @endif
                            @if ($showColumns['vermak_stock'])
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $productStock->vermak_stock }}
                                </td>
                            @endif
                            @if ($showColumns['created_at'])
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $productStock->created_at }}
                                </td>
                            @endif
                            @if ($showColumns['updated_at'])
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $productStock->updated_at }}
                                </td>
                            @endif
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center pr-4 space-x-3">
                                    <button wire:click="edit({{ $productStock->id }})" class="text-info"><i
                                            class="ri-edit-circle-line"></i></button>
                                    <button wire:click="deleteAlert({{ $productStock->id }})" class="text-danger"><i
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
        <div class="px-3 py-4">
            <div class="flex justify-end">
                {{ $productStocks->links() }}
            </div>
        </div>
    </div>
</div>
