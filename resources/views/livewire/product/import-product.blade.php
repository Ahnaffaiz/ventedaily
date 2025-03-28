<div>
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-end justify-start gap-4 p-4">
            <x-input-text type="file" name="product_file" id="product_file" title="Upload File Excel" accept=".xlsx, .xls"/>
            <button wire:click="importProduct" class="inline gap-2 text-white transition-all btn bg-primary" wire:target="importProduct" wire:loading.attr="disabled">
                <div class="flex gap-2" wire:loading.remove wire:target="importProduct">
                    Import
                </div>
                <div class="flex gap-2" wire:loading wire:target="importProduct">
                    <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-light rounded-full"></div>
                </div>
            </button>
        </div>
    </div>
</div>
