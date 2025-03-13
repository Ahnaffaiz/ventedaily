<div class="overflow-x-auto">
    <div class="pt-5">
        <div data-fc-type="tab" class="">
            <nav class="flex space-x-2 border-b border-gray-200 dark:border-gray-600" aria-label="Tabs" role="tablist">
                <button data-fc-target="#sale-detail" type="button" class="inline-flex items-center gap-2 px-4 py-3 -mb-px text-sm font-medium text-center text-gray-500 border rounded-t-lg fc-tab-active:bg-white fc-tab-active:border-b-transparent fc-tab-active:text-primary dark:fc-tab-active:bg-gray-800 dark:fc-tab-active:border-b-gray-800 dark:fc-tab-active:text-white bg-gray-50 hover:text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 active" id="card-type-tab-item-1" aria-controls="sale-detail" role="tab">
                    Sale
                </button> <!-- button-end -->
                <button data-fc-target="#shipping" type="button" class="inline-flex items-center gap-2 px-4 py-3 -mb-px text-sm font-medium text-center text-gray-500 border rounded-t-lg fc-tab-active:bg-white fc-tab-active:border-b-transparent fc-tab-active:text-primary dark:fc-tab-active:bg-gray-800 dark:fc-tab-active:border-b-gray-800 dark:fc-tab-active:text-white bg-gray-50 hover:text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:text-gray-300" id="card-type-tab-item-2" aria-controls="shipping" role="tab">
                    Shipping
                </button> <!-- button-end -->
                <button data-fc-target="#withdrawal" type="button" class="inline-flex items-center gap-2 px-4 py-3 -mb-px text-sm font-medium text-center text-gray-500 border rounded-t-lg fc-tab-active:bg-white fc-tab-active:border-b-transparent fc-tab-active:text-primary dark:fc-tab-active:bg-gray-800 dark:fc-tab-active:border-b-gray-800 dark:fc-tab-active:text-white bg-gray-50 hover:text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:text-gray-300" id="card-type-tab-item-3" aria-controls="withdrawal" role="tab">
                    Withdrawal
                </button> <!-- button-end -->
            </nav> <!-- nav-end -->

            <div class="mt-3">
                <div id="sale-detail" role="tabpanel" aria-labelledby="card-type-tab-item-1">
                    @include('livewire.sale.modal-detail.sale')
                </div> <!-- sale-detail end -->

                <div id="shipping" class="hidden" role="tabpanel" aria-labelledby="card-type-tab-item-2">
                    @include('livewire.sale.modal-detail.shipping')
                </div> <!-- shipping end -->

                <div id="withdrawal" class="hidden" role="tabpanel" aria-labelledby="card-type-tab-item-3">
                    @include('livewire.sale.modal-detail.withdrawal')
                </div> <!-- withdrawal end -->
            </div>

        </div> <!-- tab-end -->
    </div>
</div>
