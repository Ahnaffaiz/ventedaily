<div class="overflow-x-auto">
    <div class="p-6 mb-2 border">
        <h4 class="text-xl">Recap Purchase Order</h4>
        <form>
            <div class="grid grid-cols-2 gap-2">
                <x-input-text id="start_date" name="start_date" title="Start Date" type="date"/>
                <x-input-text id="end_date" name="end_date" title="End Date" type="date"/>
            </div>
        </form>
        <div class="flex items-center justify-end gap-2 mt-4">
            <button wire:click="exportProductPurchasePdf" class="inline gap-2 text-white transition-all btn bg-danger" wire:target="exportProductPurchasePdf" wire:loading.attr="disabled">
                <div class="flex gap-2" wire:loading.remove wire:target="exportProductPurchasePdf">
                    <i class="ri-file-pdf-line"></i>
                    PDF
                </div>
                <div class="flex gap-2" wire:loading wire:target="exportProductPurchasePdf">
                    <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-light rounded-full"></div>
                </div>
            </button>
            <button wire:click="exportProductPurchaseExcel" class="inline gap-2 text-white transition-all btn bg-success" wire:target="exportProductPurchaseExcel" wire:loading.attr="disabled">
                <div class="flex gap-2" wire:loading.remove wire:target="exportProductPurchaseExcel">
                    <i class="ri-file-excel-2-line"></i>
                    Excel
                </div>
                <div class="flex gap-2" wire:loading wire:target="exportProductPurchaseExcel">
                    <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-light rounded-full"></div>
                </div>
            </button>
        </div>
    </div>
</div>
