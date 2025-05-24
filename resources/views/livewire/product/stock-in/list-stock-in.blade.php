<div>
    <x-modal wire:model="isOpen" title="Detail PreOrder" closeButton="closeModal" large="true">
        @if ($isExport)
            @include('livewire.product.stock-in.export')
        @else
            @include('livewire.product.stock-in.detail-stock-in')
        @endif
    </x-modal>
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <div class="relative w-full">
                    <a class="text-white btn bg-primary" wire:navigate href="{{ route('create-stock-in') }}" type="button">
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
                @if ($stockIns->count() > 0)
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
                                Stock Type
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
                        @foreach ($stockIns as $stockIn)
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{($stockIns->currentpage() - 1) * $stockIns->perpage() + $loop->index + 1}}
                                </th>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ \Carbon\Carbon::parse($stockIn->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ App\Enums\StockType::getLabel($stockIn->stock_type) }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $stockIn->total_items }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ \Carbon\Carbon::parse($stockIn->updated_at)->format('H:i d F Y') }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center space-x-3">
                                        <button wire:click="show({{ $stockIn->id }})" class="text-primary">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <a wire:navigate href="{{ route('create-stock-in', ['stockin' => $stockIn->id]) }}"
                                            class="text-info">
                                            <i class="ri-edit-circle-line"></i>
                                        </a>
                                        <button wire:click="deleteAlert({{ $stockIn->id }})" class="text-danger">
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
                        <p class="my-5 text-base">No Stock In Data Found</p>
                    </div>
                @endif
            </table>
        </div>

        <div class="px-3 py-4">
            <x-data-pagination
                :paginator="$stockIns"
                :perPageOptions="$perPageOptions"
                perPageProperty="perPage"
                pageName="page"
            />
        </div>
    </div>
</div>
