
<div>
    @include('livewire.product.modal')
    @include('livewire.product.information')
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <div class="relative w-full">
                    <button class="text-white btn bg-primary" wire:click="openModal" type="button"> Create </button>
                </div>
                <div class="ms-2">
                    <a href="javascript:void(0)" data-fc-type="dropdown" class="text-sm text-white btn bg-primary">
                        Import <i class="ri-arrow-down-s-fill ms-1"></i>
                    </a>

                    <div class="fc-dropdown fc-dropdown-open:opacity-100 opacity-0 min-w-[10rem] z-50 transition-all duration-300 bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 rounded-md py-1 hidden">
                        <a wire:click="openImportModal('product')" class="flex items-center py-1.5 px-5 text-sm text-gray-500 hover:bg-light hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                            Product
                        </a>
                        <a wire:click="openImportModal('stock')" class="flex items-center py-1.5 px-5 text-sm text-gray-500 hover:bg-light hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                            Stock
                        </a>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mb-4">
                <div class="relative mr-4 ms-auto">
                    <input type="search" class="relative border-none form-input bg-black/5 ps-8" wire:model.live="query"
                        placeholder="Search...">
                    <span class="absolute z-10 text-base -translate-y-1/2 ri-search-line start-2 top-1/2"></span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" wire:poll.30s>
                @if ($products->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col" class="w-2/12 px-4 py-4 text-sm font-medium text-center text-gray-900 dark:text-gray-100">No</th>
                            <th scope="col" class="w-2/12 px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start"
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
                                <th scope="col" class="w-2/12 px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start"
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
                            @if ($showColumns['all_stock'])
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-center text-gray-900 dark:text-gray-100"
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
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-center text-gray-900 dark:text-gray-100"
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
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-center text-gray-900 dark:text-gray-100"
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
                                <th scope="col" class="w-1/12 px-4 py-4 text-sm font-medium text-center text-gray-900 dark:text-gray-100"
                                    wire:click="sortByColumn('product_stocks_sum_pre_order_stock')">
                                    PO Stock
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
                            <th scope="col" class="justify-end w-2/12 px-4 py-4 pr-3 text-sm font-medium text-center text-gray-900 dark:text-gray-100">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($products as $product)
                            <tr>
                                <th class="px-4 py-4 text-sm text-gray-800 whitespace-nowrap dark:text-gray-200">
                                    {{($products->currentpage() - 1) * $products->perpage() + $loop->index + 1}}
                                </th>
                                <td class="px-4 py-4 text-sm text-gray-800 whitespace-nowrap dark:text-gray-200">
                                    {{ $product->name }}
                                </td>
                                @if ($showColumns['category_id'])
                                    <td class="px-4 py-4 text-sm text-gray-800 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->category->name }}
                                    </td>
                                @endif
                                @if ($showColumns['all_stock'])
                                    <td class="px-4 py-4 text-sm text-center text-gray-800 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->totalStock() }}
                                        @if ($product->allStockInKeep() > 0)
                                            <span
                                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-warning text-white">{{ $product->allStockInKeep() }}</span>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['home_stock'])
                                    <td class="px-4 py-4 text-sm text-center text-gray-800 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->homeStock() }}
                                        @if ($product->homeStockInKeep() > 0)
                                            <span
                                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-warning text-white">{{ $product->homeStockInKeep() }}</span>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['store_stock'])
                                    <td class="px-4 py-4 text-sm text-center text-gray-800 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->storeStock() }}
                                        @if ($product->storeStockInKeep() > 0)
                                            <span
                                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-warning text-white">{{ $product->storeStockInKeep() }}</span>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['pre_order_stock'])
                                    <td class="px-4 py-4 text-sm text-center text-gray-800 whitespace-nowrap dark:text-gray-200">
                                        {{ $product->preOrderStock() }}
                                        @if ($product->preOrderStockInUse() > 0)
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-danger text-white">{{ $product->preOrderStockInUse() }}</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center space-x-3">
                                        <button wire:click="addProductStock({{ $product->id }})" class="text-primary">
                                            <i class="ri-archive-line"></i>
                                        </button>
                                        <button wire:click="edit({{ $product->id }})" class="text-info">
                                            <i class="ri-edit-circle-line"></i>
                                        </button>
                                        <button wire:click="deleteAlert({{ $product->id }})" class="text-danger">
                                            <i class="text-base ri-delete-bin-2-line"></i>
                                        </button>
                                        <button wire:click="toggleRow({{ $product->id }})" type="button"
                                            class="inline-flex transition-all duration-300">
                                            <i class="text-xl transition-all text-warning ri-arrow-down-s-line
                                                {{ in_array($product->id, $openRows) ? 'rotate-180' : '' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @if (in_array($product->id, $openRows))
                                <tr class="w-full overflow-hidden transition-[height] duration-300">
                                    <td colspan="8" class="py-2">
                                        <table class="min-w-full divide-gray-200 divide-b dark:divide-gray-700">
                                            <tbody>
                                                @foreach ($product->productStocks as $productStock)
                                                <tr class="bg-gray-100 border-gray-200 dark:bg-gray-900 border-y dark:border-gray-700">
                                                    <td class="w-2/12 py-2"></td>
                                                    <td class="w-2/12 px-4 py-2 text-sm text-gray-800 text-start whitespace-nowrap dark:text-gray-200">{{ $productStock->color->name }}</td>
                                                    <td class="w-2/12 px-4 py-2 text-sm text-gray-800 text-start whitespace-nowrap dark:text-gray-200">{{ $productStock->size->name }}</td>
                                                    <td class="w-1/12 px-4 py-2 text-sm text-center text-gray-800 whitespace-nowrap dark:text-gray-200">
                                                        {{ $productStock->all_stock }}
                                                        @if ($productStock->allStockInKeep() > 0)
                                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-warning text-white">{{ $productStock->allStockInKeep() }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="w-1/12 px-4 py-2 text-sm text-center text-gray-800 whitespace-nowrap dark:text-gray-200">
                                                        {{ $productStock->home_stock }}
                                                        @if ($productStock->homeStockInKeep() > 0)
                                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-warning text-white">{{ $productStock->homeStockInKeep() }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="w-1/12 px-4 py-2 text-sm text-center text-gray-800 whitespace-nowrap dark:text-gray-200">
                                                        {{ $productStock->store_stock }}
                                                        @if ($productStock->storeStockInKeep() > 0)
                                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-warning text-white">{{ $productStock->storeStockInKeep() }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="w-1/12 px-4 py-2 text-sm text-center text-gray-800 whitespace-nowrap dark:text-gray-200">
                                                        {{ $productStock->pre_order_stock }}
                                                        @if ($productStock->preOrderStockInUse() > 0)
                                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-danger text-white">{{ $productStock->preOrderStockInUse() }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="w-2/12 px-4 py-2 text-sm text-center text-gray-800 whitespace-nowrap dark:text-gray-200">
                                                        <div class="flex items-center justify-center space-x-3">
                                                            <button wire:click="transferStock({{ $productStock->id }})" class="text-primary">
                                                                <i class="ri-arrow-left-right-line"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
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
            <div class="flex justify-start mb-6">
                <div class="flex gap-2 pe-4">
                    <div class="p-2 rounded-full bg-warning"></div>
                    <span>Product In Pre Keep</span>
                </div>
                <div class="flex gap-2">
                    <div class="p-2 rounded-full bg-danger"></div>
                    <span>Product In Pre Order</span>
                </div>
            </div>
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
@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/cropper/cropper.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('vendor/cropper/cropper.js') }}"></script>
@endpush
