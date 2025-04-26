<div>
    <x-modal wire:model="isOpen" title="Detail Retur" closeButton="closeModal" large="true" saveButton="{{ $modal == 'status' ? 'updateStatus' : '' }}">
        @if ($modal == 'status')
            <x-input-select id="returStatus" name="returStatus" title="Retur Status" placeholder="Select Status"
                :options="App\Enums\ReturStatus::asSelectArray()" />
        @elseif ($modal == 'export')
            @include('livewire.retur.export')
        @else
            @include('livewire.retur.detail-retur')
        @endif
    </x-modal>
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <div class="relative w-full">
                    <a class="text-white btn bg-primary" wire:navigate href="{{ route('create-retur') }}" type="button">
                        Create </a>
                    <button type="button" class="inline text-white btn bg-success gaps-2" wire:click="openModalExport" type="button">
                        <i class="ri-file-download-line"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="flex justify-end mb-4">
                <div class="relative mr-4 ms-auto">
                    <input type="search" class="relative h-10 border-none form-input bg-black/5 ps-8"
                        wire:model.live="query" placeholder="Search...">
                    <span class="absolute z-10 text-base -translate-y-1/2 ri-search-line start-2 top-1/2"></span>
                </div>
                <div class="relative ms-auto">
                    <button data-fc-type="dropdown" data-fc-placement="bottom-end" type="button"
                        class="flex items-center py-2 pl-3 pr-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-500 dark:bg-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        id="menu-button" aria-expanded="true" aria-haspopup="true">
                        <i class="mr-2 ri-filter-line"></i>
                    </button>
                    <div class="absolute right-0 z-10 hidden w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" onclick="event.stopPropagation()">
                        <div class="max-h-[300px] h-56 overflow-auto mt-2">
                            <span class="m-2">Status</span>
                            <div class="flex w-full p-1">
                                <select class="form-input" wire:model.change="status">
                                    <option value="">All</option>
                                    @foreach (\App\Enums\ReturStatus::asSelectArray() as $key => $value)
                                        <option value="{{ $value }}">{{ ucfirst($value) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="py-1" role="none">
                                <span class="m-2">Column</span>
                                @foreach ($showColumns as $column => $isVisible)
                                    <div class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-900 hover:text-gray-900"
                                        role="menuitem" tabindex="-1" id="menu-item-0">
                                        <input type="checkbox" class="w-4 h-4 text-indigo-600 form-checkbox"
                                            wire:model.live="showColumns.{{ $column }}">
                                        <label class="block ml-3 text-sm font-medium text-gray-700" for="comments">
                                            {{ ucfirst(str_replace('_', ' ', $column)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" wire:poll.5s>
                @if ($returs->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                wire:click="sortByColumn('no_retur')">
                                No Retur
                                @if ($sortBy === 'no_retur')
                                    @if ($sortDirection === 'asc')
                                        <i class="ri-arrow-up-s-line"></i>
                                    @else
                                        <i class="ri-arrow-down-s-line"></i>
                                    @endif
                                @else
                                    <i class="ri-expand-up-down-line"></i>
                                @endif
                            </th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                wire:click="sortByColumn('customer_id')">
                                Customer
                                @if ($sortBy === 'customer_id')
                                    @if ($sortDirection === 'asc')
                                        <i class="ri-arrow-up-s-line"></i>
                                    @else
                                        <i class="ri-arrow-down-s-line"></i>
                                    @endif
                                @else
                                    <i class="ri-expand-up-down-line"></i>
                                @endif
                            </th>
                            @if ($showColumns['group'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('group_id')">
                                    Group
                                    @if ($sortBy === 'group_id')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['status'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('status')">
                                    Status
                                    @if ($sortBy === 'status')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['reason'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('reason')">
                                    Reason
                                    @if ($sortBy === 'reason')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['total_items'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('total_items')">
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
                            @endif
                            @if ($showColumns['total_price'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('total_price')">
                                    Total Price
                                    @if ($sortBy === 'total_price')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['created_at'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('created_at')">
                                    Created at
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
                            @endif
                            @if ($showColumns['updated_at'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('updated_at')">
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
                            @endif
                            <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($returs as $retur)
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{($returs->currentpage() - 1) * $returs->perpage() + $loop->index + 1}}
                                </th>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $retur->no_retur }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $retur->sale->customer?->name }}
                                </td>
                                @if ($showColumns['group'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        @if (strtolower($retur->sale->customer?->group?->name) === 'reseller')
                                            <span
                                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-info/10 text-info">
                                                {{ ucwords($retur->sale->customer?->group?->name) }}
                                            </span>
                                        @elseif (strtolower($retur->sale->customer?->group?->name) === 'online')
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-warning/10 text-warning">
                                                {{ ucwords($retur->sale->customer?->group?->name) }}
                                            </span>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['status'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        <button wire:click="changeStatus({{ $retur->id }})">
                                            @if (strtolower($retur->status) === strtolower(App\Enums\ReturStatus::TAKEN))
                                                <span
                                                    class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-primary/10 text-primary">
                                                    {{ ucwords($retur->status) }}
                                                </span>
                                            @elseif (strtolower($retur->status) === strtolower(App\Enums\ReturStatus::PROCESSING))
                                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-warning/10 text-warning">
                                                    {{ ucwords($retur->status) }}
                                                </span>
                                            @elseif (strtolower($retur->status) === strtolower(App\Enums\ReturStatus::BACK_TO_STOCK))
                                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-success/10 text-success">
                                                    {{ ucwords($retur->status) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-danger/10 text-danger">
                                                    {{ ucwords($retur->status) }}
                                                </span>
                                            @endif
                                        </button>
                                    </td>
                                @endif
                                @if ($showColumns['reason'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        @if (strtolower($retur->reason) === strtolower(App\Enums\ReturReason::SWAP_ITEM))
                                            <span
                                                class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-warning/10 text-warning">
                                                {{ ucwords($retur->reason) }}
                                            </span>
                                        @elseif (strtolower($retur->reason) === strtolower(App\Enums\ReturReason::SWAP_MONEY))
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-danger/10 text-danger">
                                                {{ ucwords($retur->reason) }}
                                            </span>
                                        @elseif (strtolower($retur->reason) === strtolower(App\Enums\ReturReason::DEPOSIT))
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-primary/10 text-primary">
                                                {{ ucwords($retur->reason) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-secondary/10 text-secondary">
                                                {{ ucwords($retur->reason) }}
                                            </span>
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['total_items'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $retur->total_items }}
                                    </td>
                                @endif
                                @if ($showColumns['total_price'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        Rp. {{ number_format($retur->total_price, 0, ',', '.') }}
                                    </td>
                                @endif
                                @if ($showColumns['created_at'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ \Carbon\Carbon::parse($retur->created_at)->format('d/m/Y') }}
                                    </td>
                                @endif
                                @if ($showColumns['updated_at'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ \Carbon\Carbon::parse($retur->updated_at)->format('d/m/Y') }}
                                    </td>
                                @endif
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center space-x-3">
                                        <button wire:click="show({{ $retur->id }})" class="text-primary">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button wire:click="printPayment({{ $retur->id }})" class="text-info"><i
                                            class="ri-printer-line"></i></button>
                                        <button wire:navigate href="{{ route('create-retur', ['retur' => $retur->id]) }}"
                                            class="text-info">
                                            <i class="ri-edit-circle-line"></i>
                                        </button>
                                        <button wire:click="deleteAlert({{ $retur->id }})" class="text-danger">
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
                        <p class="my-5 text-base">No Retur Found</p>
                    </div>
                @endif
            </table>
        </div>

        <div class="px-3 py-4">
            <div class="flex justify-between">
                <div class="flex flex-col">
                    <div class="flex items-center">
                        <label class="w-32 text-sm font-medium text-gray-900">Per Page</label>
                        <select
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            wire:model.change="perPage">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        Showing {{ $returs->firstItem() ?? 0 }} to {{ $returs->lastItem() ?? 0 }} of {{ $returs->total() }} entries
                    </div>
                </div>
                <x-pagination :paginator="$returs" pageName="listReturs" />
            </div>
        </div>
    </div>
    <div class="mt-2">
        @livewire('retur.resume-retur', ['user' => $user], key($user->id))
    </div>
</div>

@script
<script>
    Livewire.on('print-retur-payment', (url) => {
        let printWindow = window.open(url, '_blank', 'width=100,height=100,resizable=yes,scrollbars=yes,left=50,right=50');

        if (printWindow) {
            printWindow.focus();

            printWindow.onload = function () {
                let body = printWindow.document.body;
                let width = body.scrollWidth;
                let height = body.scrollHeight;
                let left = (screen.width - width) / 2;
                let top = (screen.height - height) / 2;

                printWindow.resizeTo(width + 60, height + 250);
            };
        }
    });
</script>
@endscript
