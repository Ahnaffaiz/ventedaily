@php
    $saveButton = null;
    $saveLabel = 'Save';
    if ($isStock) {
        $saveButton = 'saveStock';
    } elseif($isImport) {
        $saveLabel = 'Import';
        if($importType == 'product') {
            if($productPreviews?->count() > 0) {
                $saveButton = 'saveProductAlert';
            } else {
                $saveButton = 'previewProduct';
            }
        } elseif($importType == 'stock') {
            if($stockPreviews?->count() > 0) {
                $saveButton = 'saveProductStockAlert';
            } else {
                $saveButton = 'previewProductStock';
            }
        }
    } elseif($isHistory) {
        $saveButton = 'showHistory';
        $saveLabel = 'Show History';
    }
    else {
        $saveButton = $product ? 'update' : 'save';
    }
@endphp

<div x-data="{ open: @entangle('isOpen') }"
     x-show="open"
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" x-show="open"></div>
        <div class="relative w-full max-w-{{ $isProductStock || $isImport ? '4xl' : '2xl' }} p-6 mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ $product ? 'Edit ' . $product?->name : 'Product' }}
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" wire:click="closeModal">
                    <span class="sr-only">Close</span>
                    <i class="text-xl ri-close-line"></i>
                </button>
            </div>

            <!-- Content -->
            <div class="mt-6">
                @if ($isProductStock)
                    @livewire('product.product-stock', ['product' => $product], key($product->id))
                @elseif($isStock)
                    <h1 class="mb-4 text-xl">Stock Transfer</h1>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Transfer From</label>
                            <select wire:model="stockFrom" class="w-full form-select">
                                @foreach($stockTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Transfer To</label>
                            <select wire:model="stockTo" class="w-full form-select">
                                @foreach($stockTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Stock</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                Max Stock: {{ $stockTotal }}
                            </span>
                            <input type="number" wire:model="stockAmount" class="w-full pl-24 form-input" />
                        </div>
                    </div>
                @elseif($isImport)
                    @if ($importType == 'product')
                        @include('livewire.product.import-product')
                    @elseif($importType == 'stock')
                        @include('livewire.product.import-product-stock')
                    @endif
                @elseif($isHistory)
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Start Date</label>
                            <input type="date" wire:model="start_date" class="w-full form-input" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">End Date</label>
                            <input type="date" wire:model="end_date" class="w-full form-input" />
                        </div>
                    </div>
                @else
                    <div>
                        <form>
                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
                                    <input type="text" wire:model="name" class="w-full form-input" placeholder="Input Product Name Here" />
                                </div>

                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Imei</label>
                                <div class="relative">
                                    <input type="text" id="imei" class="w-full pe-8 form-input {{ $errors->first('imei') ? 'border-2 border-danger' : '' }}"
                                           wire:model.live="imei" placeholder="Input Imei">
                                    <button type="button" wire:click="generateImei"
                                            class="absolute inset-y-0 right-0 flex items-center px-3 text-primary">
                                        <i class="ri-refresh-line" wire:loading.remove wire:target="generateImei"></i>
                                        <div wire:loading wire:target="generateImei"
                                             class="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></div>
                                    </button>
                                </div>
                                @error('imei')
                                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                                @enderror

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Category</label>
                                    <select wire:model="category_id" class="w-full form-select">
                                        <option value="">Select Category</option>
                                        @foreach(\App\Models\Category::all() as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Product Status</label>
                                    <select wire:model="status" class="w-full form-select">
                                        <option value="">Select status</option>
                                        @foreach(App\Enums\ProductStatus::asSelectArray() as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex items-center">
                                    <label class="relative inline-flex cursor-pointer">
                                        <input type="checkbox" wire:model="is_favorite" class="sr-only">
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Favorite</span>
                                    </label>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Description</label>
                                    <textarea wire:model="desc" rows="3" class="w-full form-textarea"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" class="btn btn-flat" wire:click="closeModal">
                    Cancel
                </button>
                @if($saveButton)
                    <button type="button" class="btn btn-primary" wire:click="{{ $saveButton }}">
                        {{ $saveLabel }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Edit Stock Modal --}}
<div x-data="{ open: @entangle('isEditStock').live }"
     x-show="open"
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" x-show="open"></div>
        <div class="relative w-full max-w-2xl p-6 mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Edit Product Stock
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" wire:click="$toggle('isEditStock')">
                    <span class="sr-only">Close</span>
                    <i class="text-xl ri-close-line"></i>
                </button>
            </div>

            @if($editingProduct)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-sm font-bold">Color Size</th>
                                <th class="px-4 py-2 text-sm font-bold">Home</th>
                                <th class="px-4 py-2 text-sm font-bold">Store</th>
                                <th class="px-4 py-2 text-sm font-bold">Pre Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($editingProduct->productStocks as $stock)
                            <tr>
                                <td class="px-4 py-2">{{ $stock->color->name }} {{ $stock->size->name }}</td>
                                <td class="px-4 py-2">
                                    <input type="number" wire:model="editingStocks.{{ $stock->id }}.home_stock" class="w-full form-input" />
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" wire:model="editingStocks.{{ $stock->id }}.store_stock" class="w-full form-input" />
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" wire:model="editingStocks.{{ $stock->id }}.pre_order_stock" class="w-full form-input" />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="flex justify-end gap-x-4 mt-6">
                <button type="button" class="btn btn-flat" wire:click="$toggle('isEditStock')">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" wire:click="saveStockChanges">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
