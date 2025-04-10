<div>
    <div class="relative p-4 mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="grid gap-2 md:grid-cols-2">
            <x-input-text id="start_date" name="start_date" type="date" title="End Date"></x-input-text>
            <x-input-text id="end_date" name="end_date" type="date" title="End Date"></x-input-text>
        </div>
        <div class="flex items-center justify-end gap-2 mt-4">
            <button wire:click="generateReport" class="inline gap-2 text-white transition-all btn bg-success" wire:target="generateReport" wire:loading.attr="disabled">
                <div class="flex gap-2" wire:loading.remove wire:target="generateReport">
                    <i class="ri-file-excel-2-line"></i>
                    Export
                </div>
                <div class="flex gap-2" wire:loading wire:target="generateReport">
                    <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-light rounded-full"></div>
                </div>
            </button>
        </div>
    </div>
</div>
