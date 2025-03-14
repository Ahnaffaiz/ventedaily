<div>
    @include('livewire.product.modal')
    @include('livewire.product.information')
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <div class="relative w-full">
                    <button class="text-white btn bg-primary" wire:click="openModal" type="button">
                        Create </button>
                </div>
            </div>
            <div class="flex justify-end mb-4">
                <div class="relative mr-4 ms-auto">
                    <input type="search" class="relative border-none form-input bg-black/5 ps-8" wire:model.live="query"
                        placeholder="Search...">
                    <span class="absolute z-10 text-base -translate-y-1/2 ri-search-line start-2 top-1/2"></span>
                </div>
                <div class="relative ms-auto">
                    <button data-fc-type="dropdown" data-fc-placement="bottom-end" type="button"
                        class="flex items-center py-2 pl-3 pr-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-500 dark:bg-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        id="menu-button" aria-expanded="true" aria-haspopup="true">
                        <i class="mr-2 ri-filter-line"></i>
                    </button>
                    <div class="absolute right-0 z-10 hidden w-56 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg dark:divide-gray-600 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="py-1" role="none">
                            @foreach ($showColumns as $column => $isVisible)
                                <div class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-900 hover:text-gray-900"
                                    role="menuitem" tabindex="-1" id="menu-item-0">
                                    <input type="checkbox" class="w-4 h-4 text-indigo-600 form-checkbox"
                                        wire:model.live="showColumns.{{ $column }}">
                                    <label class="block ml-3 text-sm font-medium text-gray-700" for="comments">
                                        {{ ucfirst(str_replace('_', ' ', $column)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" wire:poll.30s>
                @if ($products->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-900 dark:text-gray-100">No</th>
                            @if ($showColumns['image'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start"
                                    wire:click="sortByColumn('image')">
                                    Image
                                    @if ($sortBy === 'image')
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
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start"
                                wire:click="sortByColumn('name')">
                                Name
                                @if ($sortBy === 'name')
                                    @if ($sortDirection === 'asc')
                                        <i class="ri-arrow-up-s-line"></i>
                                    @else
                                        <i class="ri-arrow-down-s-line"></i>
                                    @endif
                                @else
                                    <i class="ri-expand-up-down-line"></i>
                                @endif
                            </th>
                            @if ($showColumns['category_id'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start"
                                    wire:click="sortByColumn('category_id')">
                                    Category
                                    @if ($sortBy === 'category_id')
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
                            @if ($showColumns['status'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start"
                                    wire:click="sortByColumn('status')">
                                    Status
                                    @if ($sortBy === 'status')
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
                            @if ($showColumns['imei'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start"
                                    wire:click="sortByColumn('imei')">
                                    Barcode Imei
                                    @if ($sortBy === 'imei')
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
                            @if ($showColumns['is_favorite'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start">
                                    Favorite
                                </th>
                            @endif
                            @if ($showColumns['code'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start"
                                    wire:click="sortByColumn('code')">
                                    Code
                                    @if ($sortBy === 'code')
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
                            @if ($showColumns['all_stock'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-900 dark:text-gray-100"
                                    wire:click="sortByColumn('product_stocks_sum_all_stock')">
                                    All Stock
                                    @if ($sortBy === 'product_stocks_sum_all_stock')
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
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-900 dark:text-gray-100"
                                    wire:click="sortByColumn('product_stocks_sum_home_stock')">
                                    Home Stock
                                    @if ($sortBy === 'product_stocks_sum_home_stock')
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
                            @if ($showColumns['store_stock'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-900 dark:text-gray-100"
                                    wire:click="sortByColumn('product_stocks_sum_store_stock')">
                                    Store Stock
                                    @if ($sortBy === 'product_stocks_sum_store_stock')
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
                            @if ($showColumns['pre_order_stock'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-900 dark:text-gray-100"
                                    wire:click="sortByColumn('product_stocks_sum_pre_order_stock')">
                                    Store Stock
                                    @if ($sortBy === 'product_stocks_sum_pre_order_stock')
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
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start"
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
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start"
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
                            <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-center text-gray-900 dark:text-gray-100">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($products as $product)
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{($products->currentpage() - 1) * $products->perpage() + $loop->index + 1}}
                                </th>
                                @if ($showColumns['image'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        @if ($product->image)
                                            <img src="{{ Storage::url($product->image) }}" alt="" srcset="" height="50" width="50">
                                        @endif
                                    </td>
                                @endif
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $product->name }}
                                </td>
                                @if ($showColumns['category_id'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->category->name }}
                                    </td>
                                @endif
                                @if ($showColumns['status'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->status }}
                                    </td>
                                @endif
                                @if ($showColumns['imei'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->imei }}
                                    </td>
                                @endif
                                @if ($showColumns['is_favorite'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        @if ($product->is_favorite)
                                            <i class="text-xl text-center ri-check-double-line text-success"></i>
                                        @else
                                            <i class="text-xl text-center text-gray-400 ri-close-line"></i>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['code'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->code }}
                                    </td>
                                @endif
                                @if ($showColumns['all_stock'])
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->totalStock() }}
                                        @if ($product->allStockInKeep() > 0)
                                            <span
                                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-warning text-white">{{ $product->allStockInKeep() }}</span>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['home_stock'])
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->homeStock() }}
                                        @if ($product->homeStockInKeep() > 0)
                                            <span
                                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-warning text-white">{{ $product->homeStockInKeep() }}</span>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['store_stock'])
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->storeStock() }}
                                        @if ($product->storeStockInKeep() > 0)
                                            <span
                                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-warning text-white">{{ $product->storeStockInKeep() }}</span>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['pre_order_stock'])
                                    <td class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->preOrderStock() }}
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
                                        <button wire:click="transferStock({{ $product->id }})" class="text-primary">
                                            <i class="ri-arrow-left-right-line"></i>
                                        </button>
                                        <button wire:click="addProductStock({{ $product->id }})" class="text-primary">
                                            <i class="ri-archive-line"></i>
                                        </button>
                                        <button wire:click="edit({{ $product->id }})" class="text-info">
                                            <i class="ri-edit-circle-line"></i>
                                        </button>
                                        <button wire:click="deleteAlert({{ $product->id }})" class="text-danger">
                                            <i class="text-base ri-delete-bin-2-line"></i>
                                        </button>
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

        <div class="px-3 py-4">
            <div class="flex justify-between">
                <div class="flex items-center">
                    <label class="w-32 text-sm font-medium text-gray-900">Per Page</label>
                    <select
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        wire:model.change="perPage">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                {{ $products->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>
</div>
