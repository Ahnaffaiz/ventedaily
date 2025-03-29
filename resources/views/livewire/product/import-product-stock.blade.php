<div>
    @if ($stockPreviews?->count() <= 0)
    <x-input-text type="file" name="stock_file" id="stock_file" title="Upload File Stock Excel" accept=".xlsx, .xls"/>
    @else
    <div class="flex justify-between ">
        <h2 class="text-xl text-gray-500 dark:text-gray-200">Import Product Stock</h2>
        <div class="flex gap-2">
            <button wire:click="resetProductStockPreview" class="inline gap-2 transition-all btn bg-danger/25 text-danger hover:bg-danger hover:text-white" wire:target="resetProductStockPreview" wire:loading.attr="disabled">
                <div class="flex gap-2" wire:loading.remove wire:target="resetProductStockPreview">
                    <i class="ri-refresh-line"></i>
                    Reset Form
                </div>
                <div class="flex gap-2" wire:loading wire:target="resetProductStockPreview">
                    <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-light rounded-full"></div>
                </div>
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="relative min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Status</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Error</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Name</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Color</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Size</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Status</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Purchase Price</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Selling Price</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">All Stock</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Home Stock</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Store Stock</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Pre Order Stock</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($stockPreviews as $productStock)
                    <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                        <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            @if ($productStock->error)
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-danger/10 text-danger">Failed</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-success/10 text-success">Success</span>
                            @endif
                        </th>
                        <td class="px-4 py-4 text-sm text-danger whitespace-nowrap dark:text-danger">
                            @if ($productStock->error)
                                @foreach ($productStock->error as $error)
                                    <p class="text-sm">
                                        <i class="ri-information-line"></i>
                                        {{ $error }}
                                    </p>
                                @endforeach
                            @endif
                        </td>
                        <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $loop->iteration }}
                        </th>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $productStock->product?->name }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $productStock->color?->name }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $productStock->size?->name }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $productStock->status }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ number_format($productStock->purchase_price, '0', ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ number_format($productStock->selling_price, '0', ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $productStock->all_stock }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $productStock->home_stock }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $productStock->store_stock }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $productStock->pre_order_stock }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
