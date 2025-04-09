<div class="overflow-x-auto">
    <div class="p-6 mb-2 border">
        <h4 class="text-xl">Rekap Pengiriman Product</h4>
        <form>
            <div class="flex gap-5 mt-4">
                <div class="flex items-center">
                    <input type="radio" class="form-radio text-primary" wire:model.live="isStockFrom" value="all_stock" id="all_stock">
                    <label class="ms-1.5" for="all_stock">Semua Stock</label>
                </div>
                <div class="flex items-center">
                    <input type="radio" class="form-radio text-primary" wire:model.live="isStockFrom" value="specific_stock" id="specific_stock">
                    <label class="ms-1.5" for="specific_stock">Stok Tertentu</label>
                </div>
            </div>
            @if ($isStockFrom == 'specific_stock')
                <div class="grid grid-cols-2 gap-2">
                    <x-input-select id="stockFrom" name="stockFrom" title="Transfer From" :options="App\Enums\StockType::asSelectArray()" />
                    <x-input-select id="stockTo" name="stockTo" title="Transfer To" :options="App\Enums\StockType::asSelectArray()" />
                </div>
            @endif
            <div class="grid grid-cols-2 gap-2">
                <x-input-text id="start_date" name="start_date" title="Start Date" type="date"/>
                <x-input-text id="end_date" name="end_date" title="End Date" type="date"/>
            </div>
        </form>
        <div class="flex items-center justify-end gap-2 mt-4">
            <button wire:click="exportExcel" class="inline gap-2 text-white transition-all btn bg-success" wire:target="exportExcel" wire:loading.attr="disabled">
                <div class="flex gap-2" wire:loading.remove wire:target="exportExcel">
                    <i class="ri-file-excel-2-line"></i>
                    Excel
                </div>
                <div class="flex gap-2" wire:loading wire:target="exportExcel">
                    <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-light rounded-full"></div>
                </div>
            </button>
        </div>
    </div>
</div>
