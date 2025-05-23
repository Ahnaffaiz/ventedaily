<div class="overflow-x-auto">
    <div class="p-6 mb-2 border">
        <h4 class="text-xl">Rekap Penjualan Produk</h4>
        <form>
            <div class="flex gap-5 mt-4">
                <div class="flex items-center">
                    <input type="radio" class="form-radio text-primary" wire:model.live="exportType" value="product" id="product">
                    <label class="ms-1.5" for="product">Rekap Retur Produk</label>
                </div>
                <div class="flex items-center">
                    <input type="radio" class="form-radio text-primary" wire:model.live="exportType" value="retur" id="retur">
                    <label class="ms-1.5" for="retur">Rekap Transaksi Retur</label>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <x-input-text id="start_date" name="start_date" title="Start Date" type="date"/>
                <x-input-text id="end_date" name="end_date" title="End Date" type="date"/>
            </div>
            <x-input-select id="export_status" title="" name="export_status" placeholder="All Retur " :options="App\Enums\ReturStatus::asSelectArray()" title="Status"/>
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
