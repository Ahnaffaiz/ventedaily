<div>
    @if ($productPreviews?->count() <= 0)
    <x-input-text type="file" name="product_file" id="product_file" title="Upload File Excel" accept=".xlsx, .xls"/>
    @else
    <div class="flex justify-between ">
        <h2 class="text-xl text-gray-500 dark:text-gray-200">Import Product</h2>
        <div class="flex gap-2">
            <button wire:click="resetProductPreview" class="inline gap-2 transition-all btn bg-danger/25 text-danger hover:bg-danger hover:text-white" wire:target="resetProductPreview" wire:loading.attr="disabled">
                <div class="flex gap-2" wire:loading.remove wire:target="resetProductPreview">
                    <i class="ri-refresh-line"></i>
                    Reset Form
                </div>
                <div class="flex gap-2" wire:loading wire:target="resetProductPreview">
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
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Category</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">Imei</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Code</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Status</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Favorite</th>
                    <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($productPreviews as $product)
                    <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                        <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            @if ($product->error)
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-danger/10 text-danger">Failed</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-xs font-medium bg-success/10 text-success">Success</span>
                            @endif
                        </th>
                        <td class="px-4 py-4 text-sm text-danger whitespace-nowrap dark:text-danger">
                            @if ($product->error)
                                @foreach ($product->error as $error)
                                    <span class="text-sm">{{ $error }}</span>
                                @endforeach
                            @endif
                        </td>
                        <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $loop->iteration }}
                        </th>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $product->name }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $product->category?->name }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $product->imei }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $product->code }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $product->status }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $product->is_favorite }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                            {{ $product->desc }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
