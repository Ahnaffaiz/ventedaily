<div>
    @php
        $saveButton = null;
        $title = 'Expense';
        if ($isExport) {
            $title = 'Export Expense Recap';
            $saveButton = null;
        } else {
            if ($expense) {
                $title = 'Update Expense';
                $saveButton = 'update';
            } else {
                $title = 'Create Expense';
                $saveButton = 'save';
            }
        }
    @endphp
    <x-modal wire:model="isOpen" title="{{ $title }}"
        saveButton="{{ $saveButton }}" closeButton="closeModal">
        @if ($isExport)
            @include('livewire.cost-expense.modal.export')
        @else
            @include('livewire.cost-expense.modal.create')
        @endif
    </x-modal>
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <div class="relative w-full">
                    <button class="text-white btn bg-primary" wire:click="openModal" type="button">
                        Create </button>
                    <button class="text-white btn bg-success" wire:click="openModalExport" type="button">
                        <i class="ri-file-download-line me-1"></i>
                        Export </button>
                </div>
            </div>
            <div class="flex justify-end mb-4">
                <div class="relative mr-4 ms-auto">
                    <input type="search" class="relative border-none form-input bg-black/5 ps-8" wire:model.live="query"
                        placeholder="Search...">
                    <span class="absolute z-10 text-base -translate-y-1/2 ri-search-line start-2 top-1/2"></span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                @if ($expenses->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Date</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Cost Name</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Desc</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Amount</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Qty</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Total Amount</th>
                            <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($expenses as $expense)
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{($expenses->currentpage() - 1) * $expenses->perpage() + $loop->index + 1}}
                                </th>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $expense->name }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $expense->desc }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    Rp. {{ number_format($expense->amount, '0', ',' , '.') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $expense->qty }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    Rp. {{ number_format($expense->total_amount, '0', ',' , '.') }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center space-x-3">
                                        <button wire:click="edit({{ $expense->id }})"><i
                                                class="ri-edit-circle-line text-primary"></i></button>
                                        <button wire:click="deleteAlert({{ $expense->id }})"><i
                                                class="text-base ri-delete-bin-2-line text-danger"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <div class="text-center">
                        <i class="text-4xl ri-file-warning-line"></i>
                        <p class="my-5 text-base">No Expense Found</p>
                    </div>
                @endif
            </table>
        </div>

        <div class="px-3 py-4">
            <div class="flex flex-col items-center md:flex-row md:justify-between gap-4">
                <div class="flex flex-col items-center md:items-start">
                    <div class="mt-2 text-sm text-center md:text-left text-gray-600">
                        Showing {{ $expenses->firstItem() ?? 0 }} to {{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }} entries
                    </div>
                </div>
                <div class="mt-2 md:mt-0">
                    <x-pagination :paginator="$expenses" pageName="page" />
                </div>
            </div>
        </div>
    </div>
</div>
