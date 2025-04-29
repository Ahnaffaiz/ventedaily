<div>
    <x-modal wire:model="isOpen" title="Detail PreOrder" closeButton="closeModal" large="true">
        @if ($isExport)
            @include('livewire.product.transfer-stock.export')
        @else
            @include('livewire.product.transfer-stock.detail-transfer-stock')
        @endif
    </x-modal>
    @include('livewire.product.transfer-stock.information')
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <div class="relative w-full">
                    <a class="text-white btn bg-primary" wire:navigate href="{{ route('create-transfer-stock') }}" type="button">
                        Create
                    </a>
                    <button type="button" class="inline text-white btn bg-success gaps-2" wire:click="openModalExport" type="button">
                        <i class="ri-file-download-line"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" wire:poll.5s>
                @if ($transferStocks->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start" wire:click="sortByColumn('created_at')">
                                Date
                                @if ($sortBy === 'created_at')
                                    @if ($sortDirection === 'asc')
                                        <i class="ri-arrow-up-s-line"></i>
                                    @else
                                        <i class="ri-arrow-down-s-line"></i>
                                    @endif
                                @else
                                    <i class="ri-expand-up-down-line"></i>
                                @endif
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">
                                Transfer From
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">
                                Transfer To
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">
                                Keep
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start" wire:click="sortByColumn('total_items')">
                                Total Items
                                @if ($sortBy === 'total_items')
                                    @if ($sortDirection === 'asc')
                                        <i class="ri-arrow-up-s-line"></i>
                                    @else
                                        <i class="ri-arrow-down-s-line"></i>
                                    @endif
                                @else
                                    <i class="ri-expand-up-down-line"></i>
                                @endif
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start" wire:click="sortByColumn('updated_at')">
                                Updated at
                                @if ($sortBy === 'updated_at')
                                    @if ($sortDirection === 'asc')
                                        <i class="ri-arrow-up-s-line"></i>
                                    @else
                                        <i class="ri-arrow-down-s-line"></i>
                                    @endif
                                @else
                                    <i class="ri-expand-up-down-line"></i>
                                @endif
                            </th>
                            <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($transferStocks as $transferStock)
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{($transferStocks->currentpage() - 1) * $transferStocks->perpage() + $loop->index + 1}}
                                </th>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ \Carbon\Carbon::parse($transferStock->created_at)->translatedFormat('H:i d F Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ App\Enums\StockType::getLabel($transferStock->transfer_from) }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ App\Enums\StockType::getLabel($transferStock->transfer_to) }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    @if ($transferStock?->transferProducts?->filter(function($item) {
                                            return !empty($item->keep_product_id) && $item->keep_product_id != '[]';
                                        })->isNotEmpty())
                                        <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-info/10 text-info">Keep Product</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $transferStock->total_items }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ \Carbon\Carbon::parse($transferStock->updated_at)->translatedFormat('H:i d F Y') }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center space-x-3">
                                        <button wire:click="show({{ $transferStock->id }})" class="text-primary">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button wire:click="exportTransferProduct({{ $transferStock->id }})" class="text-success" wire:target="exportTransferProduct({{ $transferStock->id }})" wire:loading.attr="disabled">
                                            <div class="flex gap-2" wire:loading.remove wire:target="exportTransferProduct({{ $transferStock->id }})">
                                                <i class="ri-file-excel-2-line"></i>
                                            </div>
                                            <div class="flex gap-2" wire:loading wire:target="exportTransferProduct({{ $transferStock->id }})">
                                                <div class="animate-spin w-4 h-4 border-[3px] border-current border-t-transparent text-success rounded-full"></div>
                                            </div>
                                        </button>
                                        <a wire:navigate href="{{ route('create-transfer-stock', ['transferstock' => $transferStock->id]) }}"
                                            class="text-info">
                                            <i class="ri-edit-circle-line"></i>
                                        </a>
                                        <button wire:click="deleteAlert({{ $transferStock->id }})" class="text-danger">
                                            <i class="text-base ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <div class="text-center">
                        <i class="text-4xl ri-file-warning-line"></i>
                        <p class="my-5 text-base">No Transfer Data Found</p>
                    </div>
                @endif
            </table>
        </div>

        <div class="px-3 py-4">
            <div class="flex flex-col items-center gap-4 md:flex-row md:justify-between">
                <div class="flex flex-col items-center md:items-start">
                    <div class="mt-2 text-sm text-center text-gray-600 md:text-left">
                        Showing {{ $transferStocks->firstItem() ?? 0 }} to {{ $transferStocks->lastItem() ?? 0 }} of {{ $transferStocks->total() }} entries
                    </div>
                </div>
                <div class="mt-2 md:mt-0">
                    <x-pagination :paginator="$transferStocks" pageName="listTransferStocks" />
                </div>
            </div>
        </div>
    </div>
</div>
