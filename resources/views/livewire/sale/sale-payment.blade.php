<div>
    <div class="grid grid-cols-3 gap-4">
        <div class="p-6 border border-gray-200 rounded-md ">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <tbody>
                    <tr>
                        <td colspan="5" class="py-4 text-end"></td>
                        <td class="text-start">
                            <p class="mt-2 mb-2 text-lg font-semibold">Sub Total :</p>
                            <div class="mb-2">
                                <span class="text-base font-bold text-success">
                                    Discount
                                    @if (strtolower($sale?->discount_type) === strtolower(App\Enums\DiscountType::PERSEN))
                                        <span
                                            class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-sm font-medium bg-success/10 text-success">{{ $sale?->discount }}%</span>
                                    @endif
                                </span> :
                            </div>
                            <div class="mb-2">
                                <span class="text-base font-bold text-danger">
                                    Tax
                                    <span
                                        class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-full text-sm font-medium bg-danger/10 text-danger">{{ $sale?->tax }}%</span>
                                </span> :
                            </div>
                        </td>
                        <td class="font-semibold text-md text-start ps-4">
                            <p class="mt-2 text-lg font-semibold text-end">Rp
                                {{ number_format($sale?->sub_total, 0, ',', '.') }}
                            </p>
                            <p class="mt-2 text-base font-semibold text-success text-end">
                                -Rp.
                                {{ $sale?->discount_type === App\Enums\DiscountType::PERSEN ? number_format($sale?->sub_total * (int) $sale?->discount / 100, 0, ',', '.') : number_format($sale?->discount, 0, ',', '.') }}
                            </p>
                            <p class="mt-2 text-base font-semibold text-danger text-end">
                                +Rp.
                                {{ number_format($sub_total_after_discount * (int) $sale?->tax / 100, 0, ',', '.') }}
                            </p>
                        </td>
                    </tr>
                    <tr class="border-none">
                        <td colspan="5" class="text-start"></td>
                        <td class="text-lg font-semibold text-start">Total Price:</td>
                        <td class="text-lg font-semibold text-end"> Rp.
                            {{ number_format($sale?->total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="border-none">
                        <td colspan="5" class="py-4 text-start"></td>
                        <td class="py-4 text-lg font-bold text-start ">Outs Balance:</td>
                        <td class="py-4 text-lg font-bold text-end text-warning"> Rp.
                            {{ number_format($sale?->outstanding_balance, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-span-2 p-6 border border-gray-200 rounded-md">
            <div class="">
                <div class="pt-4 section">
                    <div class="grid lg:grid-cols-3 lg:gap-3 md:grid-cols-2 md:gap-2">
                        <x-input-select id="payment_type" name="payment_type" title="Payment Type"
                            :options="App\Enums\PaymentType::asSelectArray()" placeholder="Select Payment Type" />
                        <x-input-text id="cash_received" name="cash_received" title="Cash Received" type="number"
                            prepend="Rp." />
                        <x-input-text id="cash_change" name="cash_change" title="Change" type="number" prepend="Rp."
                            disabled="true" />
                    </div>
                    @if (strtolower($payment_type) === App\Enums\PaymentType::TRANSFER)
                        <div class="grid lg:grid-cols-3 md:grid-cols-2 lg:gap-2 md:gap-2">
                            <x-input-select id="bank_id" name="bank_id" title="Bank"
                                :options="App\Models\Bank::all()->pluck('name', 'id')->toArray()"
                                placeholder="Select Payment Type" />
                            <x-input-text id="account_number" name="account_number" title="Account Number" type="number" />
                            <x-input-text id="account_name" name="account_name" title="Account Name" />
                        </div>
                    @endif
                    <x-input-text id="reference" name="reference" title="Reference" />
                    <x-textarea-input id="desc" name="desc" title="Sale Note" />
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button class="text-white btn bg-primary" wire:click="save" type="button">
                    Save </button>
            </div>
        </div>
    </div>
    <div class="mt-4 overflow-x-auto border border-gray-200 rounded-md">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <h4 class="card-title">Payment List</h4>
            </div>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            @if ($payments->count() > 0)
                <thead>
                    <tr>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Time</th>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Date</th>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Amount</th>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Type</th>
                        <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($payments as $payment)
                        <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                            <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{$loop->iteration}}
                            </th>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ \Carbon\Carbon::parse($payment->date)->translatedFormat('H.i') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ \Carbon\Carbon::parse($payment->date)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $payment->amount }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $payment->payment_type }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center space-x-3">
                                    <button wire:click="edit({{ $payment->id }})" class="text-info"><i
                                            class="ri-edit-circle-line"></i></button>
                                    <button wire:click="printPayment({{ $payment->id }})" class="text-info"><i
                                            class="ri-printer-line"></i></button>
                                    @if (strtolower($payment->reference) != 'first payment')
                                        <button wire:click="deleteAlert({{ $payment->id }})" class="text-danger"><i
                                                class="text-base ri-delete-bin-2-line"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @else
                <div class="text-center">
                    <i class="text-4xl ri-file-warning-line"></i>
                    <p class="my-5 text-base">No Payment Found</p>
                </div>
            @endif
        </table>
    </div>
</div>

@script
<script>
    Livewire.on('print-sale-payment', (url) => {
        let printWindow = window.open(url, '_blank', 'width=100,height=100,resizable=yes,scrollbars=yes,left=50,right=50');

        if (printWindow) {
            printWindow.focus();

            printWindow.onload = function () {
                let body = printWindow.document.body;
                let width = body.scrollWidth;
                let height = body.scrollHeight;
                let left = (screen.width - width) / 2;
                let top = (screen.height - height) / 2;

                printWindow.resizeTo(width + 50, height + 250);
            };
        }
    });
</script>
@endscript
