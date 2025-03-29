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
    }
    else {
        $saveButton = $product ? 'update' : 'save';
    }
@endphp
<x-modal wire:model="isOpen" title="{{ $product ? 'Edit ' . $product?->name : 'Product' }}"
    saveButton="{{ $saveButton }}" closeButton="closeModal" saveLabel="{{ $saveLabel }}"
    large="{{ $isProductStock || $isImport ? true : false}}">
    @if ($isProductStock)
        @livewire('product.product-stock', ['product' => $product], key($product->id))
    @elseif($isStock)
        <h1 class="text-xl">Stock Transfer</h1>
        <div class="grid grid-cols-2 gap-2">
            <x-input-select id="stockFrom" name="stockFrom" title="Transfer From" :options="$stockTypes" />
            <x-input-select id="stockTo" name="stockTo" title="Transfer To" :options="$stockTypes" />
        </div>
        <x-input-text type="number" name="stockAmount" id="stockAmount" title="Stock" prepend="Max Stock: {{ $stockTotal }} "/>
    @elseif($isImport)
        @if ($importType == 'product')
            @include('livewire.product.import-product')
        @elseif($importType == 'stock')
            @include('livewire.product.import-product-stock')
        @endif
    @else
        <div>
            <form>
                <x-input-text name="name" id="name" title="Name" placeholder="Input Product Name Here" />
                @if (!$image)
                    <x-input-text name="image" id="image" title="Image" placeholder="Input Product Image" type="file" accept="image/*"/>
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
                        <div wire:ignore x-data="{
                            setUp() {
                                const image = document.getElementById('image');
                                const cropper = new Cropper(image, {
                                    aspectRatio: 1/1,
                                    autoCropArea: 1/1,
                                    viewMode: 1,
                                    crop () {
                                        @this.set('x_image', event.detail.x)
                                        @this.set('y_image', event.detail.y)
                                        @this.set('width_image', event.detail.width)
                                        @this.set('height_image', event.detail.height)
                                    }
                                })
                            }
                        }" x-init="setUp">
                        <img id="image" src="{{ $image->temporaryUrl() }}" alt="Preview Image"
                            class="rounded-3 img-fluid img-thumbnail"
                            style="width: 100%; max-width:100%">
                    </div>

                    @elseif ($current_image)
                        <img src="{{ Storage::url($current_image) }}" alt="" class="rounded w-[100%] h-[100%]">
                    @endif
                </div>
                <div class="relative ms-auto">
                    <button type="button" wire:click="generateImei" class="absolute z-10 text-base -translate-y-1/2 text-primary end-2 top-1/2">
                        <i class="ri-refresh-line" wire:loading.remove wire:target="generateImei"></i>
                        <div wire:loading wire:target="generateImei" class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-primary rounded-full" role="status" aria-label="loading">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </button>
                    <input type="text" id="imei" class="pe-8 relative form-input {{ $errors->first('imei') ? 'border-2 border-danger' : '' }}" wire:model.live="imei" placeholder="Input Imei">
                </div>
                @error('imei')
                    <span class="font-normal is-invalid text-danger text-small" id="is-invalid">{{ $message }}</span>
                @enderror
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
