<x-modal wire:model="isOpen" title="{{ $product ? 'Edit ' . $product?->name : 'Create Product' }}"
    saveButton="{{ $product ? 'update' : 'save' }}" closeButton="closeModal"
    large="{{ $isProductStock ? true : false}}">
    @if ($isProductStock)
        @livewire('product.product-stock', ['product' => $product], key($product->id))
    @elseif($isStock)
        <h1 class="text-xl">Stock Transfer</h1>
        <div class="grid grid-cols-2 gap-2">
            <x-input-text type="number" name="amount" id="amount" title="Withdrawal Amount" placeholder="Withdrawal Amount" prepend="Rp." />
            <x-input-text type="number" name="marketplace_price" id="marketplace_price" title="Marketplace Price" placeholder="Marketplace Price" prepend="Rp." />
        </div>
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
