<div class="grid gap-6 xl:grid-cols-2 lg:grid-cols-2">
    <div class="col-xl-6 col-lg-12">
        <div class="h-56 overflow-auto card">
            <div class="sticky top-0 z-10 flex items-center justify-between bg-gray-200 card-header dark:bg-gray-800">
                <h4 class="card-title">Transfer to Store</h4>
                <button class="inline gap-2 text-white transition-all btn btn-sm bg-primary" wire:click="transferProductAlert('store')">
                    Transfer
                </button>
            </div>

            <table class="w-full">
                @if ($transferToStores->count() > 0)
                    <thead
                        class="sticky bg-gray-200 border-b border-gray-100 dark:bg-gray-800 dark:border-b-gray-700 top-12 z-5">
                        <tr>
                            <th class="w-1/3 p-2 text-start">Product</th>
                            <th class="w-1/3 p-2 text-start">Size</th>
                            <th class="w-1/3 p-2 text-start">Color</th>
                            <th class="w-1/3 p-2 text-start">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transferToStores as $keepProduct)
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }}">
                                <td class="p-2">{{ $keepProduct->productStock->product->name }}</td>
                                <td class="p-2">{{ $keepProduct->productStock->size->name }}</td>
                                <td class="p-2">{{ $keepProduct->productStock->color->name }}</td>
                                <td class="p-2">{{ $keepProduct->home_stock }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <div class="my-5 text-center">
                        <i class="text-4xl ri-file-warning-line"></i>
                        <p class="my-5 text-base">No Product Found</p>
                    </div>
                @endif
            </table>
        </div>
    </div>
    <div class="col-xl-6 col-lg-12">
        <div class="h-56 overflow-auto card">
            <div class="sticky top-0 z-10 flex items-center justify-between bg-gray-200 card-header dark:bg-gray-800">
                <h4 class="card-title">Transfer to Home</h4>
                <button wire:click="exportTransferProduct('home')" class="inline gap-2 text-white transition-all btn btn-sm bg-success" wire:target="exportTransferProduct('home')" wire:loading.attr="disabled">
                    <div class="flex gap-2" wire:loading.remove wire:target="exportTransferProduct('home')">
                        <i class="ri-file-excel-2-line"></i>
                        Excel
                    </div>
                    <div class="flex gap-2" wire:loading wire:target="exportTransferProduct('home')">
                        <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-light rounded-full"></div>
                    </div>
                </button>
            </div>

            <table class="w-full">
                @if ($transferToHomes->count() > 0)
                    <thead
                        class="sticky bg-gray-200 border-b border-gray-100 dark:bg-gray-800 dark:border-b-gray-700 top-12 z-5">
                        <tr>
                            <th class="w-1/3 p-2 text-start">Product</th>
                            <th class="w-1/3 p-2 text-start">Size</th>
                            <th class="w-1/3 p-2 text-start">Color</th>
                            <th class="w-1/3 p-2 text-start">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transferToHomes as $keepProduct)
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }}">
                                <td class="p-2">{{ $keepProduct->productStock->product->name }}</td>
                                <td class="p-2">{{ $keepProduct->productStock->size->name }}</td>
                                <td class="p-2">{{ $keepProduct->productStock->color->name }}</td>
                                <td class="p-2">{{ $keepProduct->store_stock }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <div class="my-5 text-center">
                        <i class="text-4xl ri-file-warning-line"></i>
                        <p class="my-5 text-base">No Product Found</p>
                    </div>
                @endif
            </table>
        </div>
    </div>

</div>
