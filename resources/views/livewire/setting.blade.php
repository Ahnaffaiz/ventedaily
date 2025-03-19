<div>
    <div class="grid gap-4 lg:grid-cols-3 md:grid-cols-2">
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">General Settings</div>
                </div>
                <div class="p-6 section">
                    <x-input-text type="text" name="name" id="name" title="Store Name"
                        placeholder="Your Store Name Here" />
                    <div class="mt-4">
                        @if (!$logo)
                            <x-input-text name="logo" id="logo" title="Logo" placeholder="Input Store Logo" type="file" />
                            <small class="text-muted">Logo max. 512 kb </small>
                        @endif
                        <div wire:loading wire:target="logo">
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
                            @if ($logo)
                                <div class="flex justify-end">
                                    <a href="#" class="text-sm text-danger" wire:click="deleteLogo">
                                        <i class="inline ri-delete-bin-line"></i> Delete
                                    </a>
                                </div>

                                <div wire:ignore>
                                    <img id="logo" src="{{ $logo->temporaryUrl() }}" alt="Preview Logo"
                                        class="rounded w-[100%] h-[100%]">
                                </div>

                            @elseif ($current_logo)
                                <img src="{{ Storage::url($current_logo) }}" alt="" class="rounded w-[100%] h-[100%]">
                            @endif
                        </div>
                    </div>
                    <x-input-text type="text" name="owner" id="owner" title="Owner" placeholder="Your Owner Name Here" />
                    <x-input-text id="telp" name="telp" title="Phone" type="tel" prepend="+62" />
                    <x-textarea-input id="address" name="address" title="Address" />
                    <x-input-text type="time" name="keep_timeout" id="keep_timeout" title="Keep Timeout" placeholder="Keep Timeout" />
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Transaction Setting</div>
            </div>
            <div class="p-4 section">
                <x-input-text type="text" name="keep_code" id="keep_code" title="Keep Code" placeholder="Keep Code" />
                <x-input-text type="number" name="keep_increment" id="keep_increment" title="Keep Increment"
                    placeholder="Keep Increment" />
            </div>
            <div class="p-4 section">
                <x-input-text type="text" name="sale_code" id="sale_code" title="Sale Code" placeholder="Sale Code" />
                <x-input-text type="number" name="sale_increment" id="sale_increment" title="Sale Increment"
                    placeholder="Sale Increment" />
            </div>
            <div class="p-4 section">
                <x-input-text type="text" name="pre_order_code" id="pre_order_code" title="Pre Order Code" placeholder="Sale Code" />
                <x-input-text type="number" name="pre_order_increment" id="sale_increment" title="Pre Order Increment"
                    placeholder="Pre Order Increment" />
            </div>
            <div class="p-4 section">
                <x-input-text type="text" name="retur_code" id="pre_order_code" title="Retur Code" placeholder="Retur Code" />
                <x-input-text type="number" name="retur_increment" id="sale_increment" title="Retur Increment"
                    placeholder="Retur Increment" />
            </div>
            <button class="flex justify-end m-6 mt-5 text-white btn bg-primary" wire:click="save">Save Change</button>
        </div>
    </div>
</div>
