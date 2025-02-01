<div>
    <x-modal wire:model="isOpen" title="{{ $product ? 'Edit ' . $product?->name : 'Create Product' }}"
        saveButton="{{ $product ? 'update' : 'save' }}" closeButton="closeModal"
        large="{{ $isProductStock ? true : false}}">
        @if ($isProductStock)
            @livewire('product.product-stock', ['product' => $product], key($product->id))
        @else
            <div>
                <form>
                    <x-input-text name="name" id="name" title="Name" placeholder="Input Product Name Here" />
                    @if (!$image)
                        <x-input-text name="image" id="image" title="Image" placeholder="Input Product Image" type="file" />
                        <small class="text-muted">Image max. 512 kb </small>
                    @endif
                    <div wire:loading wire:target="image">
                        <div class="card-body">
                            <div>
                                <div class="spinner-grow spinner-grow-sm" role="status">
                                    <span class="sr-only"></span>
                                </div>
                                Uploading...
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 text-center">
                        @if ($image)
                            <div class="flex justify-end">
                                <a href="#" class="text-sm text-danger" wire:click="deleteImage">
                                    <i class="inline ri-delete-bin-line"></i> Delete
                                </a>
                            </div>

                            <div wire:ignore>
                                <img id="image" src="{{ $image->temporaryUrl() }}" alt="Preview Image"
                                    class="rounded w-[100%] h-[100%]">
                            </div>

                        @elseif ($current_image)
                            <img src="{{ Storage::url($current_image) }}" alt="" class="rounded w-[100%] h-[100%]">
                        @endif
                    </div>
                    <x-input-text name="imei" id="imei" title="Barcode Imei" placeholder="Input Imei Barcode Here" />
                    <x-input-select id="category_id" name="category_id" title="Category" placeholder="Select Category"
                        :options="App\Models\Category::all()->pluck('name', 'id')->toArray()" />
                    <x-input-select id="status" name="status" title="Product Status"
                        :options="App\Enums\ProductStatus::asSelectArray()" placeholder="Select status" />
                    <x-input-switch id="is_favorite" name="is_favorite" title="Favorite" />
                    <x-textarea-input id="desc" name="desc" title="Description" />
                </form>
            </div>
        @endif
    </x-modal>
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
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                @if ($products->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
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
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
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
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
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
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
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
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">
                                    Favorite
                                </th>
                            @endif
                            @if ($showColumns['code'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
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
                        @foreach ($products as $product)
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{($products->currentpage() - 1) * $products->perpage() + $loop->index + 1}}
                                </th>
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
                                    <div class="flex items-center justify-center pr-4 space-x-3">
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
